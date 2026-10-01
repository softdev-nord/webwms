<?php

declare(strict_types=1);

namespace WebWMS\Security\Infrastructure\Oidc;

use JsonException;
use OpenSSLAsymmetricKey;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WebWMS\Security\Application\ExternalIdentity;
use WebWMS\Security\Application\OidcAuthorization;
use WebWMS\Security\Application\OidcClient;
use WebWMS\Security\Application\SsoAuthenticationException;
use WebWMS\Security\Domain\IdentityProvider;

readonly class HttpOidcClient implements OidcClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function authorization(IdentityProvider $provider, string $callbackUrl): OidcAuthorization
    {
        $configuration = $this->configuration($provider);
        $state = $this->random();
        $nonce = $this->random();
        $verifier = $this->random(64);
        $challenge = $this->base64Url(hash('sha256', $verifier, true));
        $url = $configuration['authorization_endpoint'] . '?' . http_build_query([
            'client_id' => $provider->clientId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => implode(' ', $provider->scopes),
            'state' => $state,
            'nonce' => $nonce,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return new OidcAuthorization($url, $state, $nonce, $verifier);
    }

    public function identity(IdentityProvider $provider, string $callbackUrl, string $code, string $nonce, string $codeVerifier): ExternalIdentity
    {
        $configuration = $this->configuration($provider);
        $secret = getenv($provider->clientSecretEnv);
        if (!is_string($secret) || $secret === '') {
            throw new SsoAuthenticationException('The OIDC client secret is not configured.');
        }

        $tokens = $this->httpClient->request('POST', $configuration['token_endpoint'], [
            'auth_basic' => [$provider->clientId, $secret],
            'body' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $callbackUrl,
                'code_verifier' => $codeVerifier,
            ],
            'headers' => ['Accept' => 'application/json'],
            'timeout' => 10,
        ])->toArray(false);
        $idToken = $tokens['id_token'] ?? null;
        if (!is_string($idToken) || $idToken === '') {
            throw new SsoAuthenticationException('The OIDC token response contains no ID token.');
        }

        $claims = $this->verifiedClaims($idToken, $provider, $configuration['jwks_uri']);
        $now = time();
        $audience = $claims['aud'] ?? null;
        $validAudience = $audience === $provider->clientId || (is_array($audience) && in_array($provider->clientId, $audience, true));
        if (($claims['iss'] ?? null) !== $provider->issuerUrl
            || !$validAudience
            || !is_int($claims['exp'] ?? null)
            || $claims['exp'] < $now - 60
            || ($claims['nonce'] ?? null) !== $nonce
            || !is_string($claims['sub'] ?? null)
            || !is_string($claims['email'] ?? null)
            || ($claims['email_verified'] ?? true) !== true
        ) {
            throw new SsoAuthenticationException('The OIDC ID token claims are invalid.');
        }

        return new ExternalIdentity($claims['sub'], strtolower($claims['email']));
    }

    /** @return array{authorization_endpoint: string, token_endpoint: string, jwks_uri: string} */
    private function configuration(IdentityProvider $provider): array
    {
        $data = $this->httpClient->request('GET', $provider->issuerUrl . '/.well-known/openid-configuration', [
            'headers' => ['Accept' => 'application/json'],
            'timeout' => 10,
        ])->toArray(false);
        if (($data['issuer'] ?? null) !== $provider->issuerUrl) {
            throw new SsoAuthenticationException('The OIDC discovery issuer does not match the configured issuer.');
        }

        $configuration = [];
        foreach (['authorization_endpoint', 'token_endpoint', 'jwks_uri'] as $key) {
            $value = $data[$key] ?? null;
            if (!is_string($value) || !str_starts_with($value, 'https://')) {
                throw new SsoAuthenticationException(sprintf('OIDC discovery field "%s" is invalid.', $key));
            }
            $configuration[$key] = $value;
        }

        return [
            'authorization_endpoint' => $configuration['authorization_endpoint'],
            'token_endpoint' => $configuration['token_endpoint'],
            'jwks_uri' => $configuration['jwks_uri'],
        ];
    }

    /** @return array<string, mixed> */
    private function verifiedClaims(string $jwt, IdentityProvider $provider, string $jwksUrl): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new SsoAuthenticationException('The OIDC ID token is malformed.');
        }

        $header = $this->jsonPart($parts[0]);
        $claims = $this->jsonPart($parts[1]);
        if (($header['alg'] ?? null) !== 'RS256' || !is_string($header['kid'] ?? null)) {
            throw new SsoAuthenticationException('Only signed RS256 OIDC ID tokens are accepted.');
        }

        $jwks = $this->httpClient->request('GET', $jwksUrl, ['headers' => ['Accept' => 'application/json'], 'timeout' => 10])->toArray(false);
        $keys = $jwks['keys'] ?? null;
        if (!is_array($keys)) {
            throw new SsoAuthenticationException('The OIDC key set is invalid.');
        }

        $publicKey = null;
        foreach ($keys as $key) {
            if (is_array($key) && ($key['kid'] ?? null) === $header['kid'] && ($key['kty'] ?? null) === 'RSA') {
                $publicKey = $this->rsaPublicKey($key);

                break;
            }
        }
        if (!$publicKey instanceof OpenSSLAsymmetricKey) {
            throw new SsoAuthenticationException('The signing key of the OIDC ID token was not found.');
        }

        $signature = $this->base64UrlDecode($parts[2]);
        if (openssl_verify($parts[0] . '.' . $parts[1], $signature, $publicKey, OPENSSL_ALGO_SHA256) !== 1) {
            throw new SsoAuthenticationException(sprintf('The OIDC ID token signature for "%s" is invalid.', $provider->code));
        }

        return $claims;
    }

    /** @param array<string, mixed> $jwk */
    private function rsaPublicKey(array $jwk): OpenSSLAsymmetricKey|false
    {
        if (!is_string($jwk['n'] ?? null) || !is_string($jwk['e'] ?? null)) {
            return false;
        }

        $modulus = $this->asn1Integer($this->base64UrlDecode($jwk['n']));
        $exponent = $this->asn1Integer($this->base64UrlDecode($jwk['e']));
        $rsa = $this->asn1("\x30", $modulus . $exponent);
        $algorithm = hex2bin('300d06092a864886f70d0101010500');
        if (!is_string($algorithm)) {
            return false;
        }
        $der = $this->asn1("\x30", $algorithm . $this->asn1("\x03", "\x00" . $rsa));
        $pem = "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";

        return openssl_pkey_get_public($pem);
    }

    private function asn1Integer(string $value): string
    {
        $value = ltrim($value, "\x00");
        if ($value === '' || (ord($value[0]) & 0x80) !== 0) {
            $value = "\x00" . $value;
        }

        return $this->asn1("\x02", $value);
    }

    private function asn1(string $tag, string $value): string
    {
        $length = strlen($value);
        if ($length < 128) {
            return $tag . chr($length) . $value;
        }

        $encoded = '';
        while ($length > 0) {
            $encoded = chr($length & 0xff) . $encoded;
            $length >>= 8;
        }

        return $tag . chr(0x80 | strlen($encoded)) . $encoded . $value;
    }

    /** @return array<string, mixed> */
    private function jsonPart(string $part): array
    {
        try {
            $decoded = json_decode($this->base64UrlDecode($part), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new SsoAuthenticationException('The OIDC ID token contains invalid JSON.', previous: $exception);
        }
        if (!is_array($decoded)) {
            throw new SsoAuthenticationException('The OIDC ID token part must be an object.');
        }

        return $decoded;
    }

    private function random(int $bytes = 32): string
    {
        return $this->base64Url(random_bytes($bytes));
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if (!is_string($decoded)) {
            throw new RuntimeException('Invalid Base64URL value.');
        }

        return $decoded;
    }
}

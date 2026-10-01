<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Security\Infrastructure;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use WebWMS\Security\Domain\IdentityProvider;
use WebWMS\Security\Infrastructure\Oidc\HttpOidcClient;

class HttpOidcClientTest extends TestCase
{
    public function testItBuildsAuthorizationCodeFlowWithStateNonceAndPkce(): void
    {
        $response = new MockResponse(json_encode([
            'issuer' => 'https://id.example',
            'authorization_endpoint' => 'https://id.example/authorize',
            'token_endpoint' => 'https://id.example/token',
            'jwks_uri' => 'https://id.example/keys',
        ], JSON_THROW_ON_ERROR), ['response_headers' => ['content-type: application/json']]);
        $client = new HttpOidcClient(new MockHttpClient($response));
        $provider = new IdentityProvider('id', 'tenant', 'company', 'Company', 'https://id.example', 'client', 'OIDC_SECRET', ['openid', 'email']);

        $authorization = $client->authorization($provider, 'https://wms.example/v3/sso/callback');
        parse_str((string) parse_url($authorization->url, PHP_URL_QUERY), $query);

        self::assertSame('code', $query['response_type']);
        self::assertSame('S256', $query['code_challenge_method']);
        self::assertSame($authorization->state, $query['state']);
        self::assertSame($authorization->nonce, $query['nonce']);
        self::assertNotSame('', $authorization->codeVerifier);
    }
}

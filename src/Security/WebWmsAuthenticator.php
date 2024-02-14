<?php

declare(strict_types=1);

namespace WebWMS\Security;

use Override;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use WebWMS\Repository\UserRepository;

/**
 * @package:    WebWMS\Security
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        WebWmsAuthenticator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class WebWmsAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Override]
    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $this->getLoginUrl($request) === $request->getRequestUri();
    }

    #[Override]
    public function authenticate(Request $request): Passport
    {
        $username = (string) $request->request->get('username');
        $password = (string) $request->request->get('password');

        return new Passport(
            new UserBadge($username, function ($userIdentifier) {
                $user = $this->userRepository->findOneBy(['username' => $userIdentifier]);

                if ($user === null) {
                    throw new UserNotFoundException();
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge(
                    'authenticate',
                    (string) $request->request->get('_csrf_token')
                ),
                new RememberMeBadge(),
            ]
        );
    }

    #[Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if (($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) !== null && ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) !== '' && ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) !== '0') {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    #[Override]
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}

<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CSRFProtectionService
 *
 * CSRFProtectionService is a Service to provide simple
 * generation and validation of tokens for CSRF Protection.
 */
class CSRFProtectionService
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    /**
     * Saves a generated token into session and returns the generated token for csrf protection
     * The form must have a hidden input field where the token can be used
     * This hidden input field will be validated against the saved token (funtion: validateCSRFToken).
     */
    public function getCSRFTokenForForm(): string
    {
        $token = $this->generateToken(20);
        $this->requestStack->getSession()->set('_csrf_token', $token);

        return $token;
    }

    /**
     * Validates the submitted csrf token against the token saved in the session.
     *
     * @param bool   $invalidateToken token is only valid for one submit
     * @param string $fieldName       Set the name of the hidden input field default='_csrf_token'
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function validateCSRFToken(
        Request $request,
        bool $invalidateToken = false,
        string $fieldName = '_csrf_token'
    ): bool {
        $savedToken = $this->requestStack->getSession()->get('_csrf_token');
        $submittedToken = $request->request->get($fieldName);
        $result = ($savedToken == $submittedToken);

        // generate and save new token
        // token is only valid for one submit
        if ($invalidateToken) {
            $this->getCSRFTokenForForm();
        }

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function generateToken($length, $alphaNumeric = true): string
    {
        $numeric = '0123456789';
        $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';

        if ($alphaNumeric) {
            $chars = $numeric.$alpha;
        } else {
            $chars = $alpha;
        }

        for ($i = 0; $i < $length; ++$i) {
            $tmpStr = str_shuffle($chars);
            $token .= $tmpStr[0];
        }

        return $token;
    }
}

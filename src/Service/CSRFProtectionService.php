<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'CSRFProtectionService'
)]
readonly class CSRFProtectionService
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * Speichert ein generiertes Token in der Sitzung und gibt das generierte Token für CSRF Protection zurück
     * Das Formular muss ein Hidden Input Feld haben, in dem das Token verwendet werden kann.
     * Dieses Hidden Input Feld wird anhand des gespeicherten Tokens validiert (Funktion: validateCSRFToken).
     */
    public function getCSRFTokenForForm(): string
    {
        $token = $this->generateToken();
        $this->requestStack->getSession()->set('_csrf_token', $token);

        return $token;
    }

    /**
     * Überprüft das übermittelte CSRF-Token anhand des in der Sitzung gespeicherten Tokens.
     *
     * @param bool   $invalidateToken Token ist nur für eine Übermittlung gültig
     * @param string $fieldName       Legen Sie den Namen des Hidden Input Feld fest. default='_csrf_token'
     *
     * @SuppressWarnings(BooleanArgumentFlag)
     */
    public function validateCSRFToken(
        Request $request,
        bool $invalidateToken = false,
        string $fieldName = '_csrf_token',
    ): bool {
        $savedToken = $this->requestStack->getSession()->get('_csrf_token');
        $submittedToken = $request->request->get($fieldName);
        $result = ($savedToken == $submittedToken);

        // Neues Token generieren und speichern
        // Token ist nur für eine Übermittlung gültig
        if ($invalidateToken) {
            $this->getCSRFTokenForForm();
        }

        return $result;
    }

    /**
     * @SuppressWarnings(ElseExpression)
     * @SuppressWarnings(BooleanArgumentFlag)
     */
    private function generateToken(): string
    {
        $numeric = '0123456789';
        $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';

        $chars = $numeric . $alpha;

        for ($i = 0; $i < 20; ++$i) {
            $tmpStr = str_shuffle($chars);
            $token .= $tmpStr[0];
        }

        return $token;
    }
}

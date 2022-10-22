<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        MpdfService
 */
class MpdfService
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function getMpdf(): \Mpdf\Mpdf
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $config = [
            'mode' => $locale,
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 25,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 9,
            'margin_footer' => 9,
        ];

        return new \Mpdf\Mpdf($config);
    }
}

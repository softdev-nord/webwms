<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

interface PrintTransport
{
    public function print(Printer $printer, PrintJob $job): string;
}

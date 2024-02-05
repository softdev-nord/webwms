<?php

declare(strict_types=1);

namespace WebWMS\Exception;

/**
 * @package:    WebWMS\Exception
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        KernelBundleLoaderException
 */
class KernelBundleLoaderException extends WebWmsHttpException
{
    public function __construct(
        //        string $bundle,
        //        string $reason
    ) {
        parent::__construct(
            'Failed to load bundle "{{ bundle }}". Reason: {{ reason }}',
            //            ['bundle' => $bundle, 'reason' => $reason]
        );
    }

    public function getErrorCode(): string
    {
        return 'KERNEL_BUNDLE_LOADER_ERROR';
    }
}

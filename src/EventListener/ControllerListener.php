<?php

declare(strict_types=1);

namespace WebWMS\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;
use WebWMS\Form\Stock\StockInType;

/**
 * @package:    WebWMS\EventListener
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ControllerListener
 */
class ControllerListener extends AbstractController
{
    public function __construct(
        private Environment $twig
    ) {
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $this->twig->addGlobal('stockInForm', $this->createForm(StockInType::class)->createView());
    }
}

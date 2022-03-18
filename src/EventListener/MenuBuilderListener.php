<?php

namespace WebWMS\EventListener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;

final class MenuBuilderListener
{
    public function addMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $child = $menu->addChild('reports', [
            'label' => 'Daily and monthly reports',
        ])->setExtras([
            'icon' => 'fa fa-database', // html is also supported
        ]);
    }
}
<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Generator;

use WebWMS\Bundles\BundleGeneratorBundle\Entity\Bundle;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Generator
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        BundleGenerator
 */
class BundleGenerator extends Generator
{
    public function generateBundle(Bundle $bundle)
    {
        $dir = $bundle->getTargetDirectory();

        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($dir)));
            }
            $files = scandir($dir);
            if ($files != ['.', '..']) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
            }
            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($dir)));
            }
        }

        $parameters = [
            'namespace' => $bundle->getNamespace(),
            'bundle' => $bundle->getName(),
            'format' => $bundle->getConfigurationFormat(),
            'bundle_basename' => $bundle->getBasename(),
            'extension_alias' => $bundle->getExtensionAlias(),
        ];

        $this->renderFile('bundle/BaseBundle.php.twig', $dir.'/'.$bundle->getName().'.php', $parameters);
        if ($bundle->shouldGenerateDependencyInjectionDirectory()) {
            $this->renderFile('bundle/Extension.php.twig', $dir.'/DependencyInjection/'.$bundle->getName().'Extension.php', $parameters);
            $this->renderFile('bundle/Configuration.php.twig', $dir.'/DependencyInjection/Configuration.php', $parameters);
        }
        $this->renderFile('bundle/Service.php.twig', $dir.'/Service/'.$bundle->getName().'Service.php', $parameters);
        $this->renderFile('bundle/DataHandler.php.twig', $dir.'/Service/DataHandlers/'.$bundle->getName().'DataHandler.php', $parameters);
        $this->renderFile('bundle/DefaultController.php.twig', $dir.'/Controller/'.$bundle->getName().'Controller.php', $parameters);
        $this->renderFile('bundle/DefaultControllerTest.php.twig', $bundle->getTestsDirectory().'/Controller/'.$bundle->getName().'ControllerTest.php', $parameters);
        $this->renderFile('bundle/composer.json.twig', $dir.'/composer.json', $parameters);

        // render the services.yml/xml file
        $servicesFilename = $bundle->getServicesConfigurationFilename();
        $this->renderFile(
            sprintf('bundle/%s.twig', $servicesFilename),
            $dir.'/Resources/config/'.$servicesFilename,
            $parameters
        );

        if ($routingFilename = $bundle->getRoutingConfigurationFilename()) {
            $this->renderFile(
                sprintf('bundle/%s.twig', $routingFilename),
                $dir.'/Resources/config/'.$routingFilename,
                $parameters
            );
        }
    }
}

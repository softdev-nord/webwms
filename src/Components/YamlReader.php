<?php

declare(strict_types=1);

namespace WebWMS\Components;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use WebWMS\Components\Module\Module;
use WebWMS\Exception\ModuleConfigNotFoundException;

class YamlReader
{
    /**
     * load and validate yaml file - parse to array
     *
     * @param string $xmlFile
     * @return array<mixed>
     *
     * @throws ParseException
     */
    public function read(string $xmlFile): array
    {
        $content = [];
        try {
            $content = Yaml::parseFile($xmlFile);
        } catch (ParseException $exception) {
            printf('Unable to parse the YAML string: %s', $exception->getMessage());
        }

        return $content;
    }
}
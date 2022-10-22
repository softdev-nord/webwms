<?php

declare(strict_types=1);

namespace WebWMS\Interfaces;

use WebWMS\Entity\Template;

interface ITemplateRenderer
{
    /**
     * Every Service can tell which information are provided to render a template.
     */
    public function getRenderParams(Template $template, mixed $param);
}

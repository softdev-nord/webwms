<?php

declare(strict_types=1);

use TwigCsFixer\Config\Config;
use TwigCsFixer\File\Finder;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\TwigCsFixer;
use WebWMS\Quality\Twig\BlankLineBeforeTwigBlockRule;
use WebWMS\Quality\Twig\StructuralIndentRule;
use WebWMS\Quality\Twig\TagOnOwnLineRule;

$ruleset = new Ruleset();
$ruleset->addStandard(new TwigCsFixer());

$ruleset->addRule(new TagOnOwnLineRule());

$ruleset->addRule(new StructuralIndentRule(
    spacesPerLevel: 4,
));

$ruleset->addRule(new BlankLineBeforeTwigBlockRule());

$finder = new Finder();
$finder->in(__DIR__ . '/templates/v3');

$config = new Config();
$config->setRuleset($ruleset);
$config->setFinder($finder);

return $config;
<?php

$finder = (new PhpCsFixer\Finder())
    ->exclude('var')
    ->exclude('.docker')
    ->exclude('vendor')
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile('.php-cs-fixer.cache') // forward compatibility with 3.x line
;

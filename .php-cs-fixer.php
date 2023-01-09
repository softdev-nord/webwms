<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('.docker')
    ->exclude('vendor')
    ->exclude('node_modules')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder->append(['.php-cs-fixer.php']))
;

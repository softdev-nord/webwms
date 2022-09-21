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
    ->setFinder($finder->append(['.php-cs-fixer.php']))
;

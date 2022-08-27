<?php

return (new PhpCsFixer\Config())
    ->setRules([
       'binary_operator_spaces' => ['default' => 'single_space'],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->in(__DIR__)
    )
    ->setCacheFile('.php-cs-fixer.cache')
;

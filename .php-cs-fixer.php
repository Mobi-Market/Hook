<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__)
    ->name('*.php');

$config = new PhpCsFixer\Config();

return $config
    ->setFormat('checkstyle')
    ->setLineEnding("\n")
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR1'                  => true,
        '@PSR2'                  => true,
        '@PSR12:risky'           => true,
        '@PSR12'                 => true,
        '@PHP74Migration:risky'  => true,
        '@PHP74Migration'        => true,
        'no_trailing_whitespace' => true,
    ])
    ->setFinder($finder);

return $config;

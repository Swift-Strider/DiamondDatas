<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . "/src");

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PhpCsFixer:risky' => true,
])
    ->setRiskyAllowed(true)
    ->setFinder($finder);

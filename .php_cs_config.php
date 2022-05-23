<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/')
    ->exclude('public/_/library/')
    ->exclude('deploy/')
    ->exclude('node_modules/')
    ->exclude('var/')
;

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PSR1' => true,
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        'blank_line_before_statement' => false,
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same',
        ],
        'increment_style' => [
            'style' => 'post',
        ],
        'php_unit_test_class_requires_covers' => false,
        'single_quote' => false,
        'yoda_style' => [
            'always_move_variable' => false,
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder)
;

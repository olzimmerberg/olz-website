<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/')
    ->exclude('_/library/')
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
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        '@PHP82Migration' => true,
        'blank_line_before_statement' => false,
        'braces_position' => [
            'functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line',
        ],
        'increment_style' => [
            'style' => 'post',
        ],
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

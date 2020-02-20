<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('src/library/')
    ->in(__DIR__.'/')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        '@Symfony' => true,
        'blank_line_before_statement' => null,
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same',
        ],
        'increment_style' => [
            'style' => 'post',
        ],
        'single_quote' => null,
        'yoda_style' => [
            'always_move_variable' => false,
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder)
;

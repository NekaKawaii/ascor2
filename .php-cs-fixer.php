<?php

use PhpCsFixer\Config;

$config = new class () extends Config {
    public function __construct()
    {
        parent::__construct('platform');
    }

    /**
     * @inheritdoc
     */
    public function getRules(): array
    {
        return [
            '@PSR12' => true,
            'braces' => [
                'allow_single_line_closure' => true,
            ],
            'blank_line_before_statement' => [
                'statements' => ['continue', 'do', 'exit', 'goto', 'if', 'return', 'switch', 'throw', 'try']
            ],
            'yoda_style' => false,
            'binary_operator_spaces' => [
                'operators' => [
                    '=' => 'single_space',
                ]
            ],
            'array_indentation' => true
        ];
    }
};

$config->getFinder()
    ->in(__DIR__ . '/src')
    // ->in(__DIR__ . '/tests')
;

$config->setCacheFile(__DIR__ . '/.php_cs.cache');

return $config;

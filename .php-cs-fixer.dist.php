<?php

return (new PhpCsFixer\Config())
    ->setFinder(PhpCsFixer\Finder::create()->in('src/'))
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,

        'declare_strict_types' => true,
        'not_operator_with_successor_space' => true,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false], // Force non-Yoda style
        'blank_line_before_statement' => [
            'statements' => ['declare', 'do', 'for', 'foreach', 'if', 'switch', 'try', 'while']
        ],
        'concat_space' => false,
        'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
        'single_trait_insert_per_statement' => false,
        'simple_to_complex_string_variable' => true,
        'no_homoglyph_names' => true,
        'ordered_types' => ['sort_algorithm' => 'none', 'null_adjustment' => 'always_last'],
        'php_unit_method_casing' => ['case' => 'snake_case'],

        // PHPDoc
        'align_multiline_comment' => ['comment_type' => 'phpdocs_like'],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => ['order' => ['param', 'return', 'throws']],
        'phpdoc_separation' => [
            'groups' => [['param', 'return'], ['throws']],
            'skip_unlisted_annotations' => true
        ],
        'phpdoc_line_span' => ['const' => 'single', 'property' => 'single', 'method' => 'multi'],
        'phpdoc_types_order' => ['sort_algorithm' => 'none', 'null_adjustment' => 'always_last'],
        'comment_to_phpdoc' => ['ignored_tags' => ['todo']],
        'phpdoc_scalar' => true,
        'phpdoc_summary' => true,
    ])
;

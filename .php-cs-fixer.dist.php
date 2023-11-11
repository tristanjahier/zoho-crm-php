<?php

return (new PhpCsFixer\Config())
    ->setFinder(PhpCsFixer\Finder::create()->in('src/'))
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PSR12' => true,
        '@PhpCsFixer' => true,

        'declare_strict_types' => true,
        'not_operator_with_successor_space' => true,
        'yoda_style' => false,
        'blank_line_before_statement' => [
            'statements' => ['declare', 'do', 'for', 'foreach', 'if', 'switch', 'try', 'while']
        ],
        'concat_space' => false,
        'ordered_class_elements' => false,
        'ordered_imports' => ['sort_algorithm' => 'none', 'imports_order' => ['class', 'function', 'const']],
        'single_import_per_statement' => ['group_to_single_imports' => false],
        'global_namespace_import' => ['import_classes' => null, 'import_constants' => false, 'import_functions' => false],
        'single_trait_insert_per_statement' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'trailing_comma_in_multiline' => false,
        'simple_to_complex_string_variable' => true,
        'binary_operator_spaces' => ['operators' => ['=>' => null]],
        'no_homoglyph_names' => true,
        'increment_style' => false,
        'ordered_types' => ['sort_algorithm' => 'none', 'null_adjustment' => 'always_last'],

        // Comments
        'no_empty_comment' => false,

        // PHPUnit
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'php_unit_method_casing' => ['case' => 'snake_case'],

        // PHPDoc
        'align_multiline_comment' => ['comment_type' => 'phpdocs_like'],
        'phpdoc_to_comment' => false,
        'phpdoc_no_empty_return' => false,
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_align' => false,
        'phpdoc_order' => false,
        'phpdoc_separation' => false,
        'phpdoc_no_useless_inheritdoc' => false,
        'phpdoc_line_span' => ['const' => 'single', 'property' => 'single', 'method' => 'multi'],
        'phpdoc_types_order' => ['sort_algorithm' => 'none', 'null_adjustment' => 'always_last'],
        'phpdoc_tag_type' => ['tags' => ['inheritDoc' => 'annotation']],
        'comment_to_phpdoc' => ['ignored_tags' => ['todo']],
    ])
;

<?php

return (new PhpCsFixer\Config())
    ->setFinder(PhpCsFixer\Finder::create()->in('src/'))
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,

        // Overrides of base rule sets
        'concat_space' => false,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'single_trait_insert_per_statement' => false,

        // Extra
        'align_multiline_comment' => ['comment_type' => 'phpdocs_like'],
        'array_indentation' => true,
        'blank_line_before_statement' => [
            'statements' => ['declare', 'do', 'for', 'foreach', 'if', 'switch', 'try', 'while']
        ],
        'combine_consecutive_issets' => true,
        'comment_to_phpdoc' => ['ignored_tags' => ['todo']],
        'declare_strict_types' => true,
        'explicit_string_variable' => true,
        'method_chaining_indentation' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'no_extra_blank_lines' => true,
        'no_homoglyph_names' => true,
        'no_superfluous_elseif' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'not_operator_with_successor_space' => true,
        'ordered_types' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'php_unit_method_casing' => ['case' => 'snake_case'],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_line_span' => ['const' => 'single', 'method' => 'multi', 'property' => 'single'],
        'phpdoc_order' => ['order' => ['param', 'return', 'throws']],
        'phpdoc_scalar' => true,
        'phpdoc_separation' => ['groups' => [['param', 'return'], ['throws']], 'skip_unlisted_annotations' => true],
        'phpdoc_summary' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'return_assignment' => true,
        'self_static_accessor' => true,
        'simple_to_complex_string_variable' => true,
        'single_line_comment_style' => true,
        'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false], // Force non-Yoda style
    ])
;

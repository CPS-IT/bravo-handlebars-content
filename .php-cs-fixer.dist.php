<?php

declare(strict_types=1);

$config = new \PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->getFinder()
    ->in(__DIR__ . '/Classes')
    ->in(__DIR__ . '/Tests')
    ->name('*.php');

$config->setRules([
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    
    // Array syntax
    'array_syntax' => ['syntax' => 'short'],
    
    // Binary operators
    'binary_operator_spaces' => [
        'operators' => [
            '=>' => 'single_space',
            '=' => 'single_space',
        ],
    ],
    
    // Braces
    'braces' => [
        'allow_single_line_closure' => true,
        'position_after_functions_and_oop_constructs' => 'next',
        'position_after_control_structures' => 'same',
        'position_after_anonymous_constructs' => 'same',
    ],
    
    // Cast notation
    'cast_spaces' => ['space' => 'none'],
    'lowercase_cast' => true,
    'no_short_bool_cast' => true,
    
    // Class notation
    'class_attributes_separation' => [
        'elements' => [
            'const' => 'one',
            'method' => 'one',
            'property' => 'one',
        ],
    ],
    'class_definition' => [
        'single_line' => true,
        'single_item_single_line' => true,
    ],
    'final_internal_class' => false,
    'no_blank_lines_after_class_opening' => true,
    'no_null_property_initialization' => true,
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
            'construct',
            'destruct',
            'magic',
            'phpunit',
            'method_public',
            'method_protected',
            'method_private',
        ],
    ],
    'self_accessor' => true,
    'visibility_required' => ['elements' => ['property', 'method', 'const']],
    
    // Control structure
    'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    
    // Function notation
    'function_declaration' => ['closure_function_spacing' => 'one'],
    'function_typehint_space' => true,
    'lambda_not_used_import' => true,
    'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
    'native_function_casing' => true,
    'no_spaces_after_function_name' => true,
    'return_type_declaration' => ['space_before' => 'none'],
    
    // Import
    'fully_qualified_strict_types' => true,
    'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
    'no_leading_import_slash' => true,
    'no_unused_imports' => true,
    'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
    'single_import_per_statement' => true,
    'single_line_after_imports' => true,
    
    // Language construct
    'declare_equal_normalize' => ['space' => 'none'],
    'declare_strict_types' => true,
    'dir_constant' => true,
    'function_to_constant' => true,
    'is_null' => true,
    'modernize_types_casting' => true,
    
    // List notation
    'list_syntax' => ['syntax' => 'short'],
    
    // Namespace notation
    'blank_line_after_namespace' => true,
    'no_leading_namespace_whitespace' => true,
    
    // Operator
    'concat_space' => ['spacing' => 'one'],
    'increment_style' => ['style' => 'pre'],
    'new_with_braces' => true,
    'object_operator_without_whitespace' => true,
    'standardize_increment' => true,
    'standardize_not_equals' => true,
    'ternary_operator_spaces' => true,
    'ternary_to_null_coalescing' => true,
    'unary_operator_spaces' => true,
    
    // PHP tag
    'blank_line_after_opening_tag' => true,
    'echo_tag_syntax' => ['format' => 'long'],
    'full_opening_tag' => true,
    'no_closing_tag' => true,
    
    // PHPDoc
    'align_multiline_comment' => true,
    'general_phpdoc_annotation_remove' => ['annotations' => ['author', 'package', 'subpackage']],
    'no_blank_lines_after_phpdoc' => true,
    'no_empty_phpdoc' => true,
    'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'allow_unused_params' => true],
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_align' => false,
    'phpdoc_annotation_without_dot' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_line_span' => ['const' => 'single', 'property' => 'single'],
    'phpdoc_no_access' => true,
    'phpdoc_no_alias_tag' => true,
    'phpdoc_no_empty_return' => true,
    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_order' => true,
    'phpdoc_return_self_reference' => true,
    'phpdoc_scalar' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => false,
    'phpdoc_tag_type' => true,
    'phpdoc_to_comment' => false,
    'phpdoc_trim' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types' => true,
    'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => true,
    
    // Return notation
    'no_useless_return' => true,
    'return_assignment' => true,
    'simplified_null_return' => true,
    
    // Semicolon
    'multiline_whitespace_before_semicolons' => false,
    'no_empty_statement' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'semicolon_after_instruction' => true,
    'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
    
    // Strict
    'declare_strict_types' => true,
    'strict_comparison' => false,
    'strict_param' => false,
    
    // String notation
    'escape_implicit_backslashes' => true,
    'explicit_string_variable' => true,
    'heredoc_to_nowdoc' => true,
    'simple_to_complex_string_variable' => true,
    'single_quote' => true,
    
    // Whitespace
    'array_indentation' => true,
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'compact_nullable_typehint' => true,
    'heredoc_indentation' => true,
    'method_chaining_indentation' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'break', 'continue', 'extra', 'return', 'throw', 'use', 'parenthesis_brace_block',
            'square_brace_block', 'curly_brace_block',
        ],
    ],
    'no_spaces_around_offset' => true,
    'no_spaces_inside_parenthesis' => true,
    'no_trailing_whitespace' => true,
    'no_trailing_whitespace_in_comment' => true,
    'no_whitespace_in_blank_line' => true,
    'single_blank_line_at_eof' => true,
    'types_spaces' => ['space' => 'none'],
    
    // TYPO3 specific rules
    'no_alias_functions' => true,
    'no_mixed_echo_print' => ['use' => 'echo'],
    'pow_to_exponentiation' => true,
    'random_api_migration' => true,
    
    // Disable some rules that conflict with TYPO3 standards
    'multiline_comment_opening_closing' => false,
    'no_superfluous_phpdoc_tags' => false,
    'single_line_comment_style' => false,
]);

return $config;

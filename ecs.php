<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypeDeclarationFixer;
use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDefaultCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

/**
 * EasyCodingStandard configuration file
 */
return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/tests'
    ])

    // add a single rule
    ->withRules([
        BlankLineBeforeStatementFixer::class, // An empty line feed must precede any configured statement.
        CompactNullableTypeDeclarationFixer::class, // Remove extra spaces in a nullable type declaration.
        DeclareStrictTypesFixer::class, // Force strict types declaration in all files. Requires PHP >= 7.0.
        FopenFlagsFixer::class, // The flags in `fopen` calls must omit `t`, and `b` must be omitted or included consistently.
        ModernizeTypesCastingFixer::class, // Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
        NativeConstantInvocationFixer::class, // Add leading `\` before constant invocation of internal constant to speed up resolving. Constant name match is case-sensitive, except for `null`, `false` and `true`.
        NoUselessReturnFixer::class, // There should not be an empty `return` statement at the end of a function.
        NoUnusedImportsFixer::class, // Unused `use` statements must be removed.
        NullableTypeDeclarationForDefaultNullValueFixer::class, // Adds or removes `?` before single type declarations or `|null` at the end of union types when parameters have a default `null` value.
        OperatorLinebreakFixer::class, // Operators - when multiline - must always be at the beginning or at the end of the line.
        PhpdocOrderFixer::class, // Annotations in PHPDoc should be ordered in defined sequence.
        VoidReturnFixer::class, // Add `void` return type to functions with missing or empty return statements, but priority is given to `@return` annotations. Requires PHP >= 7.1.
        NoTrailingCommaInSinglelineFixer::class, // If a list of values separated by a comma is contained on a single line, then the last item MUST NOT have a trailing comma.
    ])
    // Concatenation should be spaced according to configuration.
    ->withConfiguredRule(
        ConcatSpaceFixer::class,
        [
            'spacing' => 'one'
        ]
    )
    // Configured annotations should be omitted from PHPDoc.
    ->withConfiguredRule(
        GeneralPhpdocAnnotationRemoveFixer::class,
        [
            'annotations' => ['copyright', 'category']
        ]
    )
    // Class, trait and interface elements must be separated with one or none blank line.
    ->withConfiguredRule(
        ClassAttributesSeparationFixer::class,
        [
            'elements' => ['property' => 'one', 'method' => 'one']
        ])
    // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.
    // Argument lists MAY be split across multiple lines, where each subsequent line is indented once.
    // When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
    ->withConfiguredRule(
        MethodArgumentSpaceFixer::class,
        [
            'on_multiline' => 'ensure_fully_multiline'
        ]
    )
    // Removes `@param`, `@return` and `@var` tags that don't provide any useful information.
    ->withConfiguredRule(
        NoSuperfluousPhpdocTagsFixer::class,
        [
            'allow_unused_params' => false,
        ]
    )
    ->withSkip([
        NativeConstantInvocationFixer::class,
        ExplicitStringVariableFixer::class,
        ExplicitIndirectVariableFixer::class,
        SingleQuoteFixer::class,
        PhpdocLineSpanFixer::class,
        RemoveUselessDefaultCommentFixer::class
    ])

    // add sets - group of rules
    ->withPreparedSets(
        psr12: true,
        comments: true,
        docblocks: true,
        namespaces: true,
        controlStructures: true,
        cleanCode: true,
    );
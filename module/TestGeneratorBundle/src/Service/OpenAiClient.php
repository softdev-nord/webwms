<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator\Service;

use Exception;
use OpenAI\Client;
use RuntimeException;
use OpenAI;

class OpenAiClient
{
    private const string MODEL = 'gpt-4-turbo'; // Alternative: ‘gpt-3.5-turbo’ for lower costs

    private Client $client;

    public function __construct(string $apiKey)
    {
        $this->client = OpenAI::client($apiKey);
    }

    public function generatePhpUnitTest(
        string $className,
        string $namespace,
        string $testCaseType,
        string $classCode
    ): string {
        $prompt = <<<PROMPT
You are an experienced PHP developer with a focus on PHPUnit.
Create a complete PHPUnit test class for the following class.
Use ‘{$testCaseType}’ as the base test class.

### **Requirements:**
- Test all public methods
- Use PHPUnit assertions (`assertEquals`, `assertSame`, `assertTrue`, etc.)
- If necessary, use suitable stubs or mocks
- Structure the tests according to best practices

### **Class to be tested:**
Namespace: {$namespace}
Class name: {$className}

{$classCode}
Generate only the code of the test class without additional comments.
PROMPT;

        try {
            $response = $this->client->chat()->create([
                'model' => self::MODEL,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an experienced PHP developer, specialised in PHPUnit.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);
        } catch (Exception $exception) {
            throw new RuntimeException(
                'Error when calling the OpenAI API: ' . $exception->getMessage()
            );
        }

        $responseContent = $response->choices[0]->message->content;

        if ($responseContent === '' || $responseContent === null) {
            throw new RuntimeException('OpenAI API returned an empty response.');
        }

        return trim($responseContent);
    }
}

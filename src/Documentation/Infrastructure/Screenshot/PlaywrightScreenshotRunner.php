<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Infrastructure\Screenshot;

use Playwright\Playwright;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use WebWMS\Documentation\Application\Screenshot\ScreenshotRunner;
use WebWMS\Documentation\Application\Screenshot\ScreenshotScenario;

final readonly class PlaywrightScreenshotRunner implements ScreenshotRunner
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function capture(ScreenshotScenario $scenario, array $resolvedParameters, string $locale, string $target, bool $headed): void
    {
        if (!class_exists(Playwright::class)) {
            throw new RuntimeException('Playwright PHP is not installed. Run composer install and vendor/bin/playwright-install --browsers.');
        }
        $baseUrl = rtrim((string) (getenv('HANDBOOK_SCREENSHOT_BASE_URL') ?: 'http://localhost'), '/');
        $email = (string) getenv('HANDBOOK_SCREENSHOT_EMAIL');
        $password = (string) getenv('HANDBOOK_SCREENSHOT_PASSWORD');
        if ($email === '' || $password === '') {
            throw new RuntimeException('HANDBOOK_SCREENSHOT_EMAIL and HANDBOOK_SCREENSHOT_PASSWORD must identify the dedicated documentation user.');
        }

        $routeParameters = $this->resolved($scenario->routeParameters, $resolvedParameters);
        $query = $this->resolved($scenario->query, $resolvedParameters);
        $path = $this->router->generate($scenario->route, $routeParameters, UrlGeneratorInterface::ABSOLUTE_PATH);
        if ($query !== []) {
            $path .= '?' . http_build_query($query);
        }

        $context = Playwright::chromium([
            'headless' => !$headed,
            'context' => [
                'viewport' => $scenario->viewport,
                'deviceScaleFactor' => 1,
                'colorScheme' => 'light',
                'locale' => $locale === 'en' ? 'en-US' : 'de-DE',
            ],
        ]);
        try {
            $page = $context->newPage();
            $page->goto($baseUrl . '/v3/login', ['waitUntil' => 'networkidle']);
            $page->locator('#email')->fill($email);
            $page->locator('#password')->fill($password);
            $page->locator('button[type="submit"]')->click();
            $page->waitForLoadState('networkidle');
            $page->goto($baseUrl . $path, ['waitUntil' => 'networkidle']);
            $page->waitForSelector($scenario->waitFor, ['state' => 'visible']);
            $page->evaluate($this->stabilisationScript($scenario->masks));
            $page->screenshot($target, ['fullPage' => $scenario->fullPage]);
        } finally {
            $context->close();
        }
    }

    /** @param array<string, string> $source @param array<string, string> $resolved @return array<string, string> */
    private function resolved(array $source, array $resolved): array
    {
        foreach ($source as $key => $value) {
            $source[$key] = $resolved[$key] ?? $value;
        }

        return $source;
    }

    /** @param list<string> $masks */
    private function stabilisationScript(array $masks): string
    {
        $selectors = json_encode($masks, JSON_THROW_ON_ERROR);

        return <<<JS
            async () => {
                await document.fonts.ready;
                const style = document.createElement('style');
                style.textContent = '*,*::before,*::after{animation:none!important;transition:none!important;caret-color:transparent!important}';
                document.head.appendChild(style);
                document.querySelectorAll('.toast,.sf-toolbar').forEach((element) => element.remove());
                for (const selector of {$selectors}) {
                    document.querySelectorAll(selector).forEach((element) => {
                        element.style.filter = 'blur(8px)';
                    });
                }
            }
            JS;
    }
}

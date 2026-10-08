<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Infrastructure\Screenshot;

use Playwright\PlaywrightFactory;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use WebWMS\Documentation\Application\Screenshot\ScreenshotRunner;
use WebWMS\Documentation\Application\Screenshot\ScreenshotRunnerUnavailable;
use WebWMS\Documentation\Application\Screenshot\ScreenshotScenario;

final readonly class PlaywrightScreenshotRunner implements ScreenshotRunner
{
    public function __construct(
        private RouterInterface $router,
        private string $handbookScreenshotBaseUrl,
        private string $handbookScreenshotEmail,
        private string $handbookScreenshotPassword,
        private PlaywrightProcessLogger $playwrightProcessLogger,
    ) {
    }

    public function capture(ScreenshotScenario $scenario, array $resolvedParameters, string $locale, string $target, bool $headed): void
    {
        if (!class_exists(PlaywrightFactory::class)) {
            throw new RuntimeException('Playwright PHP is not installed. Run composer install and vendor/bin/playwright-install --browsers.');
        }
        $baseUrl = rtrim($this->handbookScreenshotBaseUrl, '/');
        $baseUrl = preg_replace('#/v3$#', '', $baseUrl) ?? $baseUrl;
        $email = trim($this->handbookScreenshotEmail);
        $password = $this->handbookScreenshotPassword;
        if ($baseUrl === '') {
            throw new RuntimeException('HANDBOOK_SCREENSHOT_BASE_URL must contain the URL of the running application.');
        }
        if ($scenario->authenticated && ($email === '' || $password === '')) {
            throw new RuntimeException('HANDBOOK_SCREENSHOT_EMAIL and HANDBOOK_SCREENSHOT_PASSWORD must identify the dedicated documentation user.');
        }

        $routeParameters = $this->resolved($scenario->routeParameters, $resolvedParameters);
        $query = $this->resolved($scenario->query, $resolvedParameters);
        $path = $this->router->generate($scenario->route, $routeParameters, UrlGeneratorInterface::ABSOLUTE_PATH);
        if ($query !== []) {
            $path .= '?' . http_build_query($query);
        }

        $this->playwrightProcessLogger->reset();
        $client = null;
        $context = null;
        $page = null;
        $stage = 'starting Chromium';
        try {
            $client = PlaywrightFactory::create(logger: $this->playwrightProcessLogger);
            $browser = $client->chromium()->withHeadless(!$headed)->launch();
            $context = $browser->newContext([
                'viewport' => $scenario->viewport,
                'deviceScaleFactor' => 1,
                'colorScheme' => 'light',
                'locale' => $locale === 'en' ? 'en-US' : 'de-DE',
            ]);
            $page = $context->newPage();
            if ($scenario->authenticated) {
                $stage = 'opening the login page';
                $page->goto($baseUrl . '/v3/login', ['waitUntil' => 'networkidle']);
                $stage = 'waiting for the password login form';
                $page->waitForSelector('form[method="post"] #email', ['state' => 'visible']);
                $stage = 'entering the email address';
                $page->locator('form[method="post"] #email')->fill($email);
                $stage = 'entering the password';
                $page->locator('form[method="post"] #password')->fill($password);
                $stage = 'submitting the password login form';
                $page->locator('form[method="post"] button[type="submit"]')->click();
                $stage = 'waiting for authentication';
                $page->waitForLoadState('networkidle');
                if (str_ends_with((string) parse_url($page->url(), PHP_URL_PATH), '/v3/login')) {
                    throw new RuntimeException('Authentication remained on the login page. Verify the documentation user credentials and account status.');
                }
            }
            $stage = sprintf('opening scenario route "%s"', $scenario->route);
            $page->goto($baseUrl . $path, ['waitUntil' => 'networkidle']);
            $stage = sprintf('waiting for selector "%s"', $scenario->waitFor);
            $page->waitForSelector($scenario->waitFor, ['state' => 'visible']);
            $stage = 'stabilising the page';
            $page->evaluate($this->stabilisationScript($scenario->masks));
            $stage = 'writing the screenshot';
            $page->screenshot($target, ['fullPage' => $scenario->fullPage]);
        } catch (\Throwable $exception) {
            $browserState = '';
            if ($page !== null) {
                try {
                    $browserState = sprintf(' Current page: %s (%s).', $page->url(), $page->title());
                } catch (\Throwable) {
                }
            }
            throw new ScreenshotRunnerUnavailable(
                $this->failureMessage($exception, $baseUrl, $stage) . $browserState,
                previous: $exception,
            );
        } finally {
            try {
                $context?->close();
            } catch (\Throwable) {
            }
            try {
                $client?->close();
            } catch (\Throwable) {
            }
        }
    }

    private function failureMessage(\Throwable $exception, string $baseUrl, string $stage): string
    {
        $message = sprintf('Playwright failed while %s: %s', $stage, $exception->getMessage());
        $diagnostics = $this->playwrightProcessLogger->diagnostics();
        if ($diagnostics !== '') {
            return $message . PHP_EOL . $diagnostics;
        }
        if (str_contains($exception->getMessage(), 'ERR_NAME_NOT_RESOLVED')) {
            return $message . PHP_EOL . sprintf(
                'The host in HANDBOOK_SCREENSHOT_BASE_URL (%s) cannot be resolved inside the PHP container. '
                . 'For the provided Docker setup use "http://127.0.0.1" without a /v3 suffix.',
                $baseUrl,
            );
        }
        if (str_contains($exception->getMessage(), 'ERR_CONNECTION_REFUSED')) {
            return $message . PHP_EOL . sprintf(
                'No web server is reachable at HANDBOOK_SCREENSHOT_BASE_URL (%s) from inside the PHP container.',
                $baseUrl,
            );
        }
        if (str_contains($exception->getMessage(), 'browserType.launch') || str_contains($exception->getMessage(), 'Process exited')) {
            return $message . PHP_EOL . 'Verify Node.js 20+ with "node --version" and install Chromium with '
                . '"vendor/bin/playwright-install chromium". On a fresh Linux image, install the required '
                . 'system libraries with "vendor/bin/playwright-install --with-deps".';
        }

        return $message;
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

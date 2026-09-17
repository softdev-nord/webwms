<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Exception;
use RuntimeException;
use ScssPhp\ScssPhp\Compiler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ScssCompilerService
{
    private string $scssPath;

    private string $cssPath;

    private string $assetsPath;

    private string $publicAssetsPath;

    private Filesystem $filesystem;

    public function __construct(string $projectDir)
    {
        $this->scssPath = "$projectDir/assets/styles";
        $this->cssPath = "$projectDir/public/assets/css";
        $this->assetsPath = "$projectDir/assets";
        $this->publicAssetsPath = "$projectDir/public/assets";
        $this->filesystem = new Filesystem();
    }

    /**
     * Kompiliert eine einzelne SCSS-Datei.
     */
    public function compileSingle(string $scssFile): void
    {
        $scssCompiler = new Compiler();
        $scssFilePath = "$this->scssPath/$scssFile";
        $cssFilePath = "$this->cssPath/" . str_replace('.scss', '.css', $scssFile);

        if (!$this->filesystem->exists($this->scssPath)) {
            throw new RuntimeException("SCSS-Ordner existiert nicht: {$this->scssPath}");
        }

        if (!$this->filesystem->exists($scssFilePath)) {
            throw new RuntimeException("SCSS-Datei nicht gefunden: $scssFilePath");
        }

        // SCSS import-Pfade setzen
        $scssCompiler->setImportPaths($this->scssPath);
        $scssCompiler->setQuietDeps(true);

        try {
            $scssContent = file_get_contents($scssFilePath);

            if ($scssContent === false) {
                throw new RuntimeException("Fehler beim Lesen der SCSS-Datei: $scssFilePath");
            }

            $cssContent = $scssCompiler->compileString($scssContent)->getCss();
        } catch (Exception $e) {
            throw new RuntimeException("Fehler beim Kompilieren von $scssFile: " . $e->getMessage());
        }

        // Sicherstellen, dass das Zielverzeichnis existiert
        $this->filesystem->mkdir($this->cssPath);

        // Speichern der CSS-Datei
        $this->filesystem->dumpFile($cssFilePath, $cssContent);
    }

    /**
     * Kompiliert mehrere SCSS-Dateien.
     */
    public function compileMultiple(array $scssFiles): void
    {
        foreach ($scssFiles as $scssFile) {
            try {
                $this->compileSingle($scssFile);
            } catch (Exception $e) {
                echo "Fehler bei $scssFile: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Kopiert alle Unterordner aus `assets`, außer `styles` und scss, nach `public/assets`.
     */
    public function copyAssets(): void
    {
        $finder = new Finder();
        $finder->directories()->in($this->assetsPath)->depth('== 0')->exclude(['styles', 'scss']);

        foreach ($finder as $dir) {
            $source = $dir->getRealPath();
            $target = "$this->publicAssetsPath/{$dir->getBasename()}";

            if ($this->filesystem->exists($target)) {
                $this->filesystem->remove($target);
            }

            $this->filesystem->mirror($source, $target);
        }
    }
}

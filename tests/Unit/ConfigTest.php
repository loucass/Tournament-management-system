<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'env_');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        putenv('DB_HOST');
        putenv('TEST_PRIORITY_VAR');
    }

    public function testItLoadsValuesFromEnvFile(): void
    {
        file_put_contents($this->tempFile, "DB_HOST=db.example.com\nDB_DATABASE=tournament\n");

        Config::load($this->tempFile);

        self::assertSame('db.example.com', Config::get('DB_HOST'));
        self::assertSame('tournament', Config::get('DB_DATABASE'));
    }

    public function testItSkipsCommentsAndBlankLines(): void
    {
        file_put_contents(
            $this->tempFile,
            "# this is a comment\n\nDB_HOST=db.example.com\n"
        );

        Config::load($this->tempFile);

        self::assertSame('db.example.com', Config::get('DB_HOST'));
        self::assertNull(Config::get('this is a comment'));
    }

    public function testItParsesValuesContainingEqualsSign(): void
    {
        file_put_contents($this->tempFile, "DB_PASS=pa==ss;word\n");

        Config::load($this->tempFile);

        self::assertSame('pa==ss;word', Config::get('DB_PASS'));
    }

    public function testItReturnsDefaultWhenKeyIsMissing(): void
    {
        Config::load($this->tempFile);

        self::assertSame('fallback', Config::get('DOES_NOT_EXIST', 'fallback'));
    }

    public function testEnvFileTakesPriorityOverRealEnvironment(): void
    {
        putenv('TEST_PRIORITY_VAR=from-env');
        file_put_contents($this->tempFile, "TEST_PRIORITY_VAR=from-file\n");

        Config::load($this->tempFile);

        self::assertSame('from-file', Config::get('TEST_PRIORITY_VAR'));
    }

    public function testRealEnvironmentIsUsedWhenKeyMissingFromFile(): void
    {
        putenv('TEST_PRIORITY_VAR=from-env');
        file_put_contents($this->tempFile, "DB_HOST=db.example.com\n");

        Config::load($this->tempFile);

        self::assertSame('from-env', Config::get('TEST_PRIORITY_VAR'));
    }

    public function testMissingEnvFileFallsThroughToEnvironmentAndDefault(): void
    {
        putenv('DB_HOST=envhost');
        Config::load('/nonexistent/.env');

        self::assertSame('envhost', Config::get('DB_HOST'));
        self::assertSame(8080, Config::get('UNSET_KEY', 8080));
    }

    public function testAllReturnsLoadedConfiguration(): void
    {
        file_put_contents($this->tempFile, "A=1\nB=2\n");

        Config::load($this->tempFile);

        self::assertSame(['A' => '1', 'B' => '2'], Config::all());
    }
}
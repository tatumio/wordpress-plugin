<?php

namespace Metasyntactical\Composer\LicenseCheck;

use Composer\Util\Filesystem;
use Composer\Util\Silencer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class LicenseCheckPluginTest extends TestCase
{
    private $oldcwd;
    private $oldenv;
    private $testDir;
    private $composerHomeDir;
    private $composerExecutable;
    private $projectDir;

    public function setUp(): void
    {
        $this->oldcwd = getcwd();
        $this->testDir = self::getUniqueTmpDirectory();
        $this->composerHomeDir = $this->testDir.'/home';
        $this->composerExecutable = dirname(__DIR__).'/vendor/bin/composer';
        $this->projectDir = $this->testDir.'/project';
        self::ensureDirectoryExistsAndClear($this->composerHomeDir);
        self::ensureDirectoryExistsAndClear($this->projectDir);
        file_put_contents($this->composerHomeDir.'/composer.json', '{"notify-on-install": false}');

        chdir($this->projectDir);
    }

    public function tearDown(): void
    {
        chdir($this->oldcwd);

        $fs = new Filesystem();

        if ($this->testDir) {
            $fs->removeDirectory($this->testDir);
            $this->testDir = null;
        }

        if ($this->oldenv) {
            $fs->removeDirectory(getenv('COMPOSER_HOME'));
            $_SERVER['COMPOSER_HOME'] = $this->oldenv;
            putenv('COMPOSER_HOME='.$_SERVER['COMPOSER_HOME']);
            $this->oldenv = null;
        }
    }

    public function testLoadingOfPluginSucceeds()
    {
        $projectRoot = dirname(__DIR__);
        $composerJson =<<<_EOT
{
  "name": "metasyntactical/composer-plugin-license-check-test",
  "license": "MIT",
  "type": "project",
  "minimum-stability": "dev",
  "require": {
    "metasyntactical/composer-plugin-license-check": "dev-master"
  },
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "metasyntactical/composer-plugin-license-check",
        "description": "Plugin for Composer to restrict installation of packages to valid licenses via whitelist.",
        "license": "MIT",
        "type": "composer-plugin",
        "require": {
          "php": ">=7.1.0,<7.4.0",
          "composer-plugin-api": "^1.0"
        },
        "require-dev": {
          "composer/composer": "^1.4",
          "phpunit/phpunit": "^6.1"
        },
        "autoload": {
          "psr-4": {
            "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\": "src/"
          }
        },
        "autoload-dev": {
          "psr-4": {
            "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\": "tests/"
          }
        },
        "extra": {
          "class": "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\LicenseCheckPlugin"
        },
        "version": "dev-master",
        "dist": {
          "url": "{$projectRoot}",
          "type": "path"
        }
      }
    }
  ]
}
_EOT;
        file_put_contents($this->projectDir.'/composer.json', $composerJson);

        $this->oldenv = getenv('COMPOSER_HOME');
        $_SERVER['COMPOSER_HOME'] = $this->composerHomeDir;
        putenv('COMPOSER_HOME='.$_SERVER['COMPOSER_HOME']);

        $cmd = 'php '.escapeshellarg($this->composerExecutable).' --no-ansi -v install';
        $proc = new Process($cmd, $this->projectDir, null, null, 300);
        $exitcode = $proc->run();

        $errorOutput = $this->cleanOutput($proc->getErrorOutput());

        self::assertContains('The Metasyntactical LicenseCheck Plugin has been enabled.', $errorOutput);
        self::assertSame(0, (int) $exitcode);
    }

    public function testLicenseCheckCommand()
    {
        $projectRoot = dirname(__DIR__);
        $composerJson =<<<_EOT
{
  "name": "metasyntactical/composer-plugin-license-check-test",
  "license": "MIT",
  "type": "project",
  "minimum-stability": "stable",
  "require": {
    "metasyntactical/composer-plugin-license-check": "dev-master@dev",
    "sebastian/version": "^2.0",
    "psr/log": "^1.0"
  },
  "extra": {
    "metasyntactical/composer-plugin-license-check": {
      "whitelist": [
        "MIT",
        "BSD-3-Clause"
      ],
      "blacklist": [
        "MIT"
      ]
    }
  },
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "metasyntactical/composer-plugin-license-check",
        "description": "Plugin for Composer to restrict installation of packages to valid licenses via whitelist.",
        "license": "MIT",
        "type": "composer-plugin",
        "require": {
          "php": ">=7.1.0,<7.4.0",
          "composer-plugin-api": "^1.0"
        },
        "require-dev": {
          "composer/composer": "^1.4",
          "phpunit/phpunit": "^6.1"
        },
        "autoload": {
          "psr-4": {
            "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\": "src/"
          }
        },
        "autoload-dev": {
          "psr-4": {
            "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\": "tests/"
          }
        },
        "extra": {
          "class": "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\LicenseCheckPlugin"
        },
        "version": "dev-master",
        "dist": {
          "url": "{$projectRoot}",
          "type": "path"
        }
      }
    }
  ]
}
_EOT;
        file_put_contents($this->projectDir.'/composer.json', $composerJson);

        $this->oldenv = getenv('COMPOSER_HOME');
        $_SERVER['COMPOSER_HOME'] = $this->composerHomeDir;
        putenv('COMPOSER_HOME='.$_SERVER['COMPOSER_HOME']);

        $cmd = 'php '.escapeshellarg($this->composerExecutable).' --no-ansi --no-plugins install';
        $proc = new Process($cmd, $this->projectDir, null, null, 300);
        $exitcode = $proc->run();

        self::assertSame(0, (int) $exitcode);

        $cmd = 'php '.escapeshellarg($this->composerExecutable).' --no-ansi check-licenses';
        $proc = new Process($cmd, $this->projectDir, null, null, 300);
        $exitcode = $proc->run();

        self::assertContains('1.1.0       MIT           no', $this->cleanOutput($proc->getOutput()));
        self::assertContains('2.0.1       BSD-3-Clause  yes', $this->cleanOutput($proc->getOutput()));
        self::assertSame(1, (int) $exitcode);
    }

    public function testRequiringPackageWithDisallowedLicenseFails()
    {
        $projectRoot = dirname(__DIR__);
        $composerJson =<<<_EOT
{
  "name": "metasyntactical/composer-plugin-license-check-test",
  "license": "MIT",
  "type": "project",
  "minimum-stability": "stable",
  "require": {
    "metasyntactical/composer-plugin-license-check": "dev-master@dev",
    "psr/log": "^1.0"
  },
  "extra": {
    "metasyntactical/composer-plugin-license-check": {
      "whitelist": [
        "MIT"
      ]
    }
  },
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "metasyntactical/composer-plugin-license-check",
        "description": "Plugin for Composer to restrict installation of packages to valid licenses via whitelist.",
        "license": "MIT",
        "type": "composer-plugin",
        "require": {
          "php": ">=7.1.0,<7.4.0",
          "composer-plugin-api": "^1.0"
        },
        "require-dev": {
          "composer/composer": "^1.4",
          "phpunit/phpunit": "^6.1"
        },
        "autoload": {
          "psr-4": {
            "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\": "src/"
          }
        },
        "autoload-dev": {
          "psr-4": {
            "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\": "tests/"
          }
        },
        "extra": {
          "class": "Metasyntactical\\\\Composer\\\\LicenseCheck\\\\LicenseCheckPlugin"
        },
        "version": "dev-master",
        "dist": {
          "url": "{$projectRoot}",
          "type": "path"
        }
      }
    }
  ]
}
_EOT;
        file_put_contents($this->projectDir.'/composer.json', $composerJson);

        $this->oldenv = getenv('COMPOSER_HOME');
        $_SERVER['COMPOSER_HOME'] = $this->composerHomeDir;
        putenv('COMPOSER_HOME='.$_SERVER['COMPOSER_HOME']);

        $cmd = 'php '.escapeshellarg($this->composerExecutable).' --no-ansi --no-plugins install';
        $proc = new Process($cmd, $this->projectDir, null, null, 300);
        $exitcode = $proc->run();

        $this->oldenv = getenv('COMPOSER_HOME');
        $_SERVER['COMPOSER_HOME'] = $this->composerHomeDir;
        putenv('COMPOSER_HOME='.$_SERVER['COMPOSER_HOME']);

        $cmd = 'php '.escapeshellarg($this->composerExecutable).' --no-ansi require sebastian/version:^2.0';
        $proc = new Process($cmd, $this->projectDir, null, null, 300);
        $exitcode = $proc->run();

        self::assertContains(
            'License "BSD-3-Clause" of package "sebastian/version" is not allowed',
            $this->cleanOutput($proc->getErrorOutput())
        );
        self::assertSame(1, (int) $exitcode);
    }

    private static function getUniqueTmpDirectory()
    {
        $attempts = 5;
        $root = sys_get_temp_dir();

        do {
            $unique = $root . DIRECTORY_SEPARATOR . uniqid('composer-test-' . random_int(1000, 9000), false);

            if (!file_exists($unique) && Silencer::call('mkdir', $unique, 0777)) {
                return realpath($unique);
            }
        } while (--$attempts);

        throw new \RuntimeException('Failed to create a unique temporary directory.');
    }

    private static function ensureDirectoryExistsAndClear($directory)
    {
        $fs = new Filesystem();

        if (is_dir($directory)) {
            $fs->removeDirectory($directory);
        }

        mkdir($directory, 0777, true);
    }

    private function cleanOutput($output)
    {
        $processed = '';

        /** @noinspection ForeachInvariantsInspection */
        for ($i = 0, $maxLength = strlen($output); $i < $maxLength; $i++) {
            if ($output[$i] === "\x08") {
                $processed = substr($processed, 0, -1);
            } elseif ($output[$i] !== "\r") {
                $processed .= $output[$i];
            }
        }

        return $processed;
    }
}

<?php declare(strict_types=1);

namespace Metasyntactical\Composer\LicenseCheck\Command;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

final class CommandProvider
    implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            new CheckLicensesCommand(),
        ];
    }
}
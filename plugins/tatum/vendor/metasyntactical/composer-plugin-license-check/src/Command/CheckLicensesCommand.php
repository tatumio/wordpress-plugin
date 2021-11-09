<?php declare(strict_types=1);

namespace Metasyntactical\Composer\LicenseCheck\Command;

use Composer\Command\BaseCommand;
use Composer\Json\JsonFile;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PluginEvents;
use Composer\Repository\RepositoryInterface;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckLicensesCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('check-licenses')
            ->setDescription('Validate licenses of installed packages against specified white- and blacklists.')
            ->setDefinition([
                new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'Format of the output: text or json', 'text'),
                new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables search in require-dev packages.'),
            ])
            ->setHelp(<<<EOT
The check-licenses command displays detailed information about the licenses of
the installed packages and whether they are allowed or forbidden to be used in
the root project.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = $this->getComposer();

        $commandEvent = new CommandEvent(PluginEvents::COMMAND, 'check-licenses', $input, $output);
        $composer->getEventDispatcher()->dispatch($commandEvent->getName(), $commandEvent);

        $root = $composer->getPackage();
        $repo = $composer->getRepositoryManager()->getLocalRepository();

        if ($input->getOption('no-dev')) {
            $packages = $this->filterRequiredPackages($repo, $root);
        } else {
            $packages = $this->appendPackages($repo->getPackages(), []);
        }

        ksort($packages);
        $io = $this->getIO();

        $packagesInfo = $this->calculatePackagesInfo($root, $packages);
        $violationFound = false;

        switch ($format = $input->getOption('format')) {
            case 'text':
                $io->write('Name: <comment>'.$packagesInfo['name'].'</comment>');
                $io->write('Version: <comment>'.$packagesInfo['version'].'</comment>');
                $io->write('Licenses: <comment>'.(implode(', ', $packagesInfo['license']) ?: 'none').'</comment>');
                $io->write('Dependencies:');
                $io->write('');

                $table = new Table($output);
                $table->setStyle('compact');
                $table->getStyle()->setVerticalBorderChar('');
                $table->getStyle()->setCellRowContentFormat('%s  ');
                $table->setHeaders(['Name', 'Version', 'License', 'Allowed to Use?']);
                /** @noinspection ForeachSourceInspection */
                foreach ($packagesInfo['dependencies'] as $dependencyName => $dependency) {
                    $table->addRow([
                        $dependencyName,
                        $dependency['version'],
                        implode(', ', $dependency['license']) ?: 'none',
                        $dependency['allowed_to_use'] ? 'yes' : 'no',
                    ]);
                    $violationFound = $violationFound || !$dependency['allowed_to_use'];
                }
                $table->render();
                break;

            case 'json':
                foreach ($packagesInfo['dependencies'] as $dependency) {
                    $violationFound = $violationFound || !$dependency['allowed_to_use'];
                }
                $io->write(JsonFile::encode($packagesInfo));
                break;

            default:
                throw new RuntimeException(
                    sprintf('Unsupported format "%s".  See help for supported formats.', $format)
                );
        }

        return (int) $violationFound;
    }

    private function calculatePackagesInfo(PackageInterface $rootPackage, array $packages): array
    {
        $dependencies = [];
        foreach ($packages as $package) {
            $dependencies[$package->getPrettyName()] = $this->calculatePackageInfo($rootPackage, $package);
        }

        return [
            'name' => $rootPackage->getPrettyName(),
            'version' => $rootPackage->getFullPrettyVersion(),
            'license' => $rootPackage->getLicense(),
            'dependencies' => $dependencies,
        ];
    }

    private function calculatePackageInfo(PackageInterface $rootPackage, CompletePackageInterface $package): array
    {
        $allowedToUse = true;

        $extraConfigKey = 'metasyntactical/composer-plugin-license-check';
        $whitelist = [];
        $blacklist = [];
        if (array_key_exists($extraConfigKey, $rootPackage->getExtra())
            && is_array($rootPackage->getExtra()[$extraConfigKey])
        ) {
            if (array_key_exists('whitelist', $rootPackage->getExtra()[$extraConfigKey])
                && in_array(gettype($rootPackage->getExtra()[$extraConfigKey]['whitelist']), ['string', 'array'], true)
            ) {
                $whitelist = (array) $rootPackage->getExtra()[$extraConfigKey]['whitelist'];
            }
            if (array_key_exists('blacklist', $rootPackage->getExtra()[$extraConfigKey])
                && in_array(gettype($rootPackage->getExtra()[$extraConfigKey]['blacklist']), ['string', 'array'], true)
            ) {
                $blacklist = (array) $rootPackage->getExtra()[$extraConfigKey]['blacklist'];
            }
        }

        if ($allowedToUse && $blacklist) {
            $allowedToUse = !array_intersect($package->getLicense(), $blacklist);
        }
        if ($allowedToUse && $whitelist) {
            $allowedToUse = !!array_intersect($package->getLicense(), $whitelist);
        }

        if ($package->getName() === 'metasyntactical/composer-plugin-license-check') {
            $allowedToUse = true;
        }

        return [
            'version' => $package->getFullPrettyVersion(),
            'license' => $package->getLicense(),
            'allowed_to_use' => $allowedToUse,
        ];
    }

    private function filterRequiredPackages(
        RepositoryInterface $repo, PackageInterface $package, array $bucket = []
    ): array
    {
        $requires = array_keys($package->getRequires());

        $packageListNames = array_keys($bucket);
        $filteredPackages = array_filter(
            $repo->getPackages(),
            function (PackageInterface $package) use ($requires, $packageListNames) {
                return in_array($package->getName(), $requires, true)
                    && !in_array($package->getName(), $packageListNames, true);
            }
        );

        $bucket = $this->appendPackages($filteredPackages, $bucket);

        foreach ($filteredPackages as $filteredPackage) {
            $bucket = $this->filterRequiredPackages($repo, $filteredPackage, $bucket);
        }

        return $bucket;
    }

    public function appendPackages(array $packages, array $bucket): array
    {
        foreach ($packages as $package) {
            $bucket[$package->getName()] = $package;
        }

        return $bucket;
    }
}

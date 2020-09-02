<?php

namespace Elabee\Phpstorm\Swoole\Plugin;

use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Installer\PackageEvent;
use Composer\Package\PackageInterface;
use Symfony\Component\Yaml\Yaml;

class ComposerScript
{

    public static function postPackageUpdate(PackageEvent $event)
    {
        $package = self::getPackage($event->getOperation());
        if ($package && $package->getName() === 'swoole/ide-helper') {
            $version = $package->getPrettyVersion();

            /** @var \Elabee\Phpstorm\Swoole\Plugin\IdeaPluginXMLElement $xml */
            $plugin = __DIR__ . '/../META-INF/plugin.xml';
            $xml = simplexml_load_file(
              $plugin,
              IdeaPluginXMLElement::class
            );

            $xml->setVersion($version);
            $xml->setChangeNotes(
              "PHP stubs for <a href=\"https://github.com/swoole/swoole-src/releases/tag/v${version}\">Swoole ${version}</a>."
            );
            $xml->saveXML($plugin);

            $action = __DIR__ . '/../.github/workflows/php.yml';
            $yaml = Yaml::parseFile($action);
            foreach ($yaml['jobs']['build']['steps'] as $i => $step) {
                if (isset($step['id']) && $step['id'] === 'generate') {
                    $yaml['jobs']['build']['steps'][$i]['uses'] = "docker://phpswoole/swoole:${version}-php7.1";
                }
            }
            file_put_contents($action, Yaml::dump($yaml, PHP_INT_MAX, 2));
        }
    }

    private static function getPackage(OperationInterface $operation
    ): ?PackageInterface {
        if ($operation instanceof InstallOperation) {
            return $operation->getPackage();
        }

        if ($operation instanceof UpdateOperation) {
            return $operation->getTargetPackage();
        }

        return null;
    }

}

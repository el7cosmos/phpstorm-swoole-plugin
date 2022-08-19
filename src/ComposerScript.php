<?php

namespace Elabee\Phpstorm\Swoole\Plugin;

use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\OperationInterface;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Installer\PackageEvent;
use Composer\Package\PackageInterface;

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
        }
    }

    private static function getPackage(OperationInterface $operation
    ): ?PackageInterface {
        return match (true) {
            $operation instanceof InstallOperation => $operation->getPackage(),
            $operation instanceof UpdateOperation => $operation->getTargetPackage(),
            default => null,
        };
    }

}

<?php

declare(strict_types=1);

namespace Wrm\Events;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wrm\Events\Service\DestinationDataImportService\Slugger\Registry;
use Wrm\Events\Service\DestinationDataImportService\Slugger\SluggerType;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(SluggerType::class)->addTag('tx_events.slugger_type');
    $containerBuilder->addCompilerPass(new class() implements CompilerPassInterface {
        public function process(ContainerBuilder $container): void
        {
            $registry = $container->getDefinition(Registry::class);
            foreach (array_keys($container->findTaggedServiceIds('tx_events.slugger_type')) as $serviceId) {
                $registry->addMethodCall('add', [$container->getDefinition($serviceId)]);
            }
        }
    });
};

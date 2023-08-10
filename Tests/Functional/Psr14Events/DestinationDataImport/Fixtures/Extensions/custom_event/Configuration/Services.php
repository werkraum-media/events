<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use WerkraumMedia\CustomEvent\EventListener\EventImportListener;
use Wrm\Events\Service\DestinationDataImportService\Events\EventImportEvent;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('WerkraumMedia\\CustomEvent\\', '../Classes/*');
    $services->set(EventImportListener::class)
        ->tag('event.listener', [
            'event' => EventImportEvent::class,
        ])
    ;
};

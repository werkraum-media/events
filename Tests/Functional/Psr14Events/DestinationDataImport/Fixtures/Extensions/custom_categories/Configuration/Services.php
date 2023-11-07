<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use WerkraumMedia\CustomCategories\EventListener\CategoriesAssignListener;
use WerkraumMedia\Events\Service\DestinationDataImportService\Events\CategoriesAssignEvent;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('WerkraumMedia\\CustomCategories\\', '../Classes/');
    $services->set(CategoriesAssignListener::class)
        ->tag(
            'event.listener',
            [
                'event' => CategoriesAssignEvent::class,
            ]
        )
    ;
};

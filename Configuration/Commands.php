<?php

use Wrm\Events\Command\DestinationDataImportCommand;
use Wrm\Events\Command\RemoveAllCommand;
use Wrm\Events\Command\RemovePastCommand;
return [
    'events:destinationdataimport‚' => [
        'class' => DestinationDataImportCommand::class
    ],
    'events:removeAll' => [
        'class' => RemoveAllCommand::class
    ],
    'events:removePast' => [
        'class' => RemovePastCommand::class
    ],
];

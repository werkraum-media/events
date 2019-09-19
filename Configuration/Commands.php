<?php
return [
    'events:destinationdataimport‚' => [
        'class' => \Wrm\Events\Command\DestinationDataImportCommand::class
    ],
    'events:removeAll' => [
        'class' => \Wrm\Events\Command\RemoveAllCommand::class
    ],
    'events:removePast' => [
        'class' => \Wrm\Events\Command\RemovePastCommand::class
    ],
];

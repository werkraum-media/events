### Destination Data Event Import Extension

##### Start Symfony Command to import Events local
    
    TYPO3_CONTEXT=Development php vendor/bin/typo3 events:import
    
##### Start Symfony Command to import Events on Stage

    TYPO3_CONTEXT=Production/Staging /usr/local/bin/php7.1.6-cli typo3cms/stage.thueringer-staedte.de/current/vendor/bin/typo3 events:import

    
##### Clean category relations

    TRUNCATE TABLE tx_events_domain_model_event;
    TRUNCATE TABLE tx_events_domain_model_date;
    TRUNCATE TABLE tx_events_domain_model_organizer;
    DELETE FROM sys_category_record_mm WHERE tablenames = 'tx_events_domain_model_event';
    DELETE FROM sys_file_reference WHERE tablenames = 'tx_events_domain_model_event';
    DELETE FROM sys_file WHERE identifier LIKE '%/events/%';
    DELETE FROM sys_file_metadata alternative = 'DD Import';
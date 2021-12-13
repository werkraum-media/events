
##### Clean category relations

    TRUNCATE TABLE tx_events_domain_model_event;
    TRUNCATE TABLE tx_events_domain_model_date;
    TRUNCATE TABLE tx_events_domain_model_organizer;
    DELETE FROM sys_category_record_mm WHERE tablenames = 'tx_events_domain_model_event';
    DELETE FROM sys_file_reference WHERE tablenames = 'tx_events_domain_model_event';
    DELETE FROM sys_file WHERE identifier LIKE '%/events/%';
    DELETE FROM sys_file_metadata WHERE alternative = 'DD Import';

CREATE TABLE tx_events_domain_model_event (
    KEY dataHandler (l10n_parent, t3ver_oid, deleted, t3ver_wsid, t3ver_state),
);

CREATE TABLE tx_events_domain_model_organizer (
    KEY dataHandler (l10n_parent, sys_language_uid, deleted),
);

CREATE TABLE tx_events_domain_model_partner (
    KEY dataHandler (l10n_parent, sys_language_uid, deleted),
);

CREATE TABLE tx_events_domain_model_date (
    KEY event (event),
    KEY dataHandler (event, t3ver_wsid, pid),
);

CREATE TABLE tx_events_domain_model_location (
    KEY global_id (global_id),
);

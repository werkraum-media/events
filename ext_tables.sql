CREATE TABLE tx_events_domain_model_event (
    title varchar(255) DEFAULT '' NOT NULL,
    subtitle text,
    global_id varchar(255) DEFAULT '' NOT NULL,
    slug varchar(255) DEFAULT '' NOT NULL,
    highlight smallint(5) unsigned DEFAULT '0' NOT NULL,
    teaser text,
    details text,
    price_info text,
    web varchar(255) DEFAULT '' NOT NULL,
    ticket text,
    facebook varchar(255) DEFAULT '' NOT NULL,
    youtube varchar(255) DEFAULT '' NOT NULL,
    instagram varchar(255) DEFAULT '' NOT NULL,
    images int(11) unsigned NOT NULL default '0',
    categories int(11) DEFAULT '0' NOT NULL,
    features int(11) DEFAULT '0' NOT NULL,
    keywords text,
    pages text,
    dates int(11) unsigned DEFAULT '0' NOT NULL,
    organizer int(11) unsigned DEFAULT '0',
    location int(11) unsigned DEFAULT '0',
    partner text,
    region int(11) unsigned DEFAULT '0',
    references_events text,
    source_name varchar(255) DEFAULT '' NOT NULL,
    source_url varchar(255) DEFAULT '' NOT NULL,
    KEY dataHandler (l10n_parent, t3ver_oid, deleted, t3ver_wsid, t3ver_state)
);

CREATE TABLE tx_events_domain_model_organizer (
    name varchar(255) DEFAULT '' NOT NULL,
    street varchar(255) DEFAULT '' NOT NULL,
    district varchar(255) DEFAULT '' NOT NULL,
    city varchar(255) DEFAULT '' NOT NULL,
    zip varchar(255) DEFAULT '' NOT NULL,
    phone varchar(255) DEFAULT '' NOT NULL,
    web varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,

    KEY dataHandler (l10n_parent, sys_language_uid, deleted)
);

CREATE TABLE tx_events_domain_model_partner (
    title varchar(255) DEFAULT '' NOT NULL,
    link varchar(255) DEFAULT '' NOT NULL,
    images int(11) unsigned NOT NULL default '0',

    KEY dataHandler (l10n_parent, sys_language_uid, deleted)
);

CREATE TABLE tx_events_domain_model_date (
    event int(11) unsigned DEFAULT '0' NOT NULL,
    start int(11) DEFAULT NULL,
    end int(11) DEFAULT NULL,
    canceled varchar(255) DEFAULT 'no' NOT NULL,
    slug varchar(255) DEFAULT '' NOT NULL,
    KEY event (event),
    KEY dataHandler (event, t3ver_wsid, pid)
);

CREATE TABLE tx_events_domain_model_region (
    title varchar(255) DEFAULT '' NOT NULL,
);

CREATE TABLE tx_events_domain_model_date (
    event int(11) unsigned DEFAULT '0' NOT NULL,
    postponed_date int(11) unsigned DEFAULT '0' NOT NULL,
    canceled_link varchar(1024) DEFAULT '' NOT NULL,
);

CREATE TABLE tx_events_domain_model_import (
    title varchar(1024) DEFAULT '' NOT NULL,

    storage_pid int(11) unsigned DEFAULT '0' NOT NULL,
    files_folder varchar(1024) DEFAULT '' NOT NULL,

    categories_pid int(11) unsigned,
    category_parent int(11) unsigned,

    features_pid int(11) unsigned,
    features_parent int(11) unsigned,

    region int(11) unsigned DEFAULT '0' NOT NULL,

    rest_experience varchar(1024) DEFAULT '' NOT NULL,
    rest_search_query varchar(1024) DEFAULT '' NOT NULL,
);

CREATE TABLE tx_events_domain_model_location (
    global_id varchar(255) DEFAULT '' NOT NULL,
    name varchar(255) DEFAULT '' NOT NULL,
    street varchar(255) DEFAULT '' NOT NULL,
    city varchar(255) DEFAULT '' NOT NULL,
    zip varchar(255) DEFAULT '' NOT NULL,
    district varchar(255) DEFAULT '' NOT NULL,
    country varchar(255) DEFAULT '' NOT NULL,
    phone varchar(255) DEFAULT '' NOT NULL,
    latitude varchar(255) DEFAULT '' NOT NULL,
    longitude varchar(255) DEFAULT '' NOT NULL,
    children text,

    KEY global_id (global_id)
);

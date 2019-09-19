#
# Table structure for table 'tx_events_domain_model_event'
#
CREATE TABLE tx_events_domain_model_event (

	title varchar(255) DEFAULT '' NOT NULL,
	global_id varchar(255) DEFAULT '' NOT NULL,
	slug varchar(255) DEFAULT '' NOT NULL,
	highlight smallint(5) unsigned DEFAULT '0' NOT NULL,
	teaser text,
	details text,
	price_info text,
	street varchar(255) DEFAULT '' NOT NULL,
	district varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	zip varchar(255) DEFAULT '' NOT NULL,
	country varchar(255) DEFAULT '' NOT NULL,
	web varchar(255) DEFAULT '' NOT NULL,
	booking varchar(255) DEFAULT '' NOT NULL,
	ticket varchar(255) DEFAULT '' NOT NULL,
	facebook varchar(255) DEFAULT '' NOT NULL,
	youtube varchar(255) DEFAULT '' NOT NULL,
	latitude varchar(255) DEFAULT '' NOT NULL,
	longitude varchar(255) DEFAULT '' NOT NULL,
	images int(11) unsigned NOT NULL default '0',
	categories int(11) DEFAULT '0' NOT NULL,
	dates int(11) unsigned DEFAULT '0' NOT NULL,
	organizer int(11) unsigned DEFAULT '0',
	region int(11) unsigned DEFAULT '0',

	KEY dataHandler (l10n_parent, t3ver_oid, deleted, t3ver_wsid, t3ver_state)
);

#
# Table structure for table 'tx_events_domain_model_organizer'
#
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

#
# Table structure for table 'tx_events_domain_model_date'
#
CREATE TABLE tx_events_domain_model_date (

	event int(11) unsigned DEFAULT '0' NOT NULL,

	start datetime DEFAULT NULL,
	end datetime DEFAULT NULL,

	KEY event (event),
	KEY dataHandler (event, t3ver_wsid, pid)
);

#
# Table structure for table 'tx_events_domain_model_region'
#
CREATE TABLE tx_events_domain_model_region (

	title varchar(255) DEFAULT '' NOT NULL,

);

#
# Table structure for table 'tx_events_domain_model_date'
#
CREATE TABLE tx_events_domain_model_date (

	event int(11) unsigned DEFAULT '0' NOT NULL,

);

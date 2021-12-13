.. _start:

======
Events
======

.. only:: html

	:Classification:
		events

	:Description:
		Extension to manage Destination Data managed events

	:Author:
		Dirk Koritnik

	:Email:
		koritnik@werkraum-media.de

	:License:
		This document is published under the Open Content License
		available from http://www.opencontent.org/opl.shtml

	:Rendered:
		|today|

	The content of this document is related to TYPO3,
	a GNU/GPL CMS/Framework available from `www.typo3.org <http://www.typo3.org/>`_.

Table of Contents
=================

.. toctree::
	:maxdepth: 3
	:titlesonly:

	Commands
	Changelog

Clean category relations
========================

.. code-block:: sql

    TRUNCATE TABLE tx_events_domain_model_event;
    TRUNCATE TABLE tx_events_domain_model_date;
    TRUNCATE TABLE tx_events_domain_model_organizer;
    DELETE FROM sys_category_record_mm WHERE tablenames = 'tx_events_domain_model_event';
    DELETE FROM sys_file_reference WHERE tablenames = 'tx_events_domain_model_event';
    DELETE FROM sys_file WHERE identifier LIKE '%/events/%';
    DELETE FROM sys_file_metadata WHERE alternative = 'DD Import';

.. _settings:

Settings
========

Import
------

The import can be configured via ``module.tx_events_import`` and offers the following
options:

.. option:: module.tx_events_import.settings.repeatUntil

   Allows to define how long dates should be repeated if ``repeatUntil`` is missing in import data.
   The value will be passed to https://www.php.net/manual/en/datetime.modify.php.
   No stdWrap is applied.

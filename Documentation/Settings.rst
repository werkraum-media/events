.. _settings:

Settings
========

Frontend
--------

The frontend can be configured via TYPO3 Extbase defaults.

``stdWrap`` is applied to all options.

.. option:: settings.useMidnight

   Can be set to ``1`` (default) or ``0``.

   | ``0`` will show dates starting from now.
   | ``1`` will use midnight of configured start date.

.. option:: settings.upcoming

   Can be set to ``0`` (default) or ``1``.

   | ``0`` does not alter the behaviour.
   | ``1`` turns off the option ``useMidnight``, ``start`` and ``end``.
   | Only dates with a start date in the future will be shown.

Import
------

The import can be configured via ``module.tx_events_import`` and offers the following
options:

.. option:: module.tx_events_import.settings.repeatUntil

   Allows to define how long dates should be repeated if ``repeatUntil`` is missing in import data.
   The value will be passed to https://www.php.net/manual/en/datetime.modify.php.
   No stdWrap is applied.

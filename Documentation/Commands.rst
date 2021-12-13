.. _commands:

Commands
========

Proper documentation is available via built in help system.
This documentation is just there to keep existing Info from readme.

Access built in help:

.. code-block:: sh

   # Get overview of commands
   ./vendor/bin/typo3

   # Get detailed help of command
   ./vendor/bin/typo3 help events:destinationdataimport

Example execution:

.. code-block:: sh

    # Local
    TYPO3_CONTEXT=Development php vendor/bin/typo3 events:destinationdataimport

    # Stage
    TYPO3_CONTEXT=Production/Staging /usr/local/bin/php7.1.6-cli typo3cms/stage.thueringer-staedte.de/current/vendor/bin/typo3 events:destinationdataimport

``events:destinationdataimport``
--------------------------------

Also available as ``events:destinationdataimport‚`` note the extra comma at end.
This is done for backwards compatibility.

``events:removeAll``
--------------------------------


``events:removePast``
--------------------------------


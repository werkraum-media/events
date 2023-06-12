.. index:: single: Command
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

.. index:: single: Command; events:destinationdataimportviaconfiguration
.. _eventsDestinationdataimportviaconfiguration:

``events:destinationdataimportviaconfiguration``
------------------------------------------------

.. index:: single: Command; events:destinationdataimportviaallconfigurations
.. _eventsDestinationdataimportviaallconfigurations:

``events:destinationdataimportviaallconfigurations``
----------------------------------------------------

.. index:: single: Command; events:removeAll
.. _eventsRemoveAll:

``events:removeAll``
--------------------

.. index:: single: Command; events:removePast
.. _eventsRemovePast:

``events:removePast``
---------------------

Will remove all dates within the past.
Events with no more dates will be removed as well.
It also will remove all related files that no longer are in use.

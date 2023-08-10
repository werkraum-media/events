.. index:: single: import; destination.one
           single: destination.one
.. _importDestinationOne:

Import destination.one
======================

The extension provides out of the box to import events from destination.one.

The import can fetch events, dates, categories and images.
Multiple imports can be defined, e.g. one per experience.
Imports are configured as database entries "Import Configuration".

The import can be triggered via scheduler tasks as well as commands.
A single import as well as all imports at once can be triggered.

The import will:

* Add and update events

* Flush all dates for updated events and re-create all dates.

* Add and update all images for updated and added events.
  Existing image files won't be downloaded again, only information and position are updated.

The import will not:

* Remove outdated data. Use cleanup command :ref:`eventsRemovePast` instead.

.. _caching:

.. index:: single: caching

Caching
=======

The extension provides out of the box support for proper TYPO3 page caching.

Page Cache
----------

TYPO3 can cache the full page with a TTL (=time to live).
The TTL can be adjusted via configuration and code.
The extension uses the code to lower TTL if necessary.

The TTL of the extension is determined in the following way:

#. Upcoming midnight if midnight should be used.

#. Start of each shown date if upcoming should be used.

#. End of each shown date as fallback.

The corresponding code is ``Wrm\Events\Caching\PageCacheTimeout``.

That way the TTL of each page is not longer as the valid period for shown events,
leading to re-rendering of the page once an event might change.

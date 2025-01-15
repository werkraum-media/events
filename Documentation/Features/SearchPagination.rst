.. index:: single: Search
.. _searchPagination:

Search Pagination
=================

The extension supports pagination of search results.
Please properly configure the system. Add the following `TYPO3_CONF_VARS` configuration:

.. code-block:: php

   'FE' => [
       'cacheHash' => [
           'excludedParameters' => [
               '^events[search]',
           ],
       ],
   ],

Adopt the configuration to your own setup, e.g. change the `pluginNamespace` from `events` to whatever you've configured.
And ensure the involved plugins are excluded from caching (`USER_INT`).

The extension will assume `events[search]` as default namespace for search arguments.
Please make use of Services files and Dependency Injection to configure the custom
`AddSearchArgumentsToRouteArgumentsMiddleware` middleware with your own namespace.

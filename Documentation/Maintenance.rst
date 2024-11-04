Maintenance
===========

Migrate to SiteSets
-------------------

We currently leverage TypoScript for some configuration during import.
This is a bad idea, TypoScript is not intended for this usage.

We should migrate to Site Sets in the future.
The import should use the page uid for import records to resolve the site and fetch
corresponding settings.
That way the import can provide all the necessary information and can be passed down
to all classes.

Remove no longer needed TSConfig
--------------------------------

We currently have `Configuration/page.tsconfig` and `Configuration/TsConfig/`.
Both can be removed once we drop v12 support.

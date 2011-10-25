Fixturator
==========

Disclaimer
----------

This is currently very much a work in progress...

Extract data
------------

Application to extract fixture sets from pre-existing databases by introspecting foreign key relationships.

For example, you could extract all data relating to one User...

The application will::

 - Extract the data of the user specified.
 - Scan for many-to-one relationships, extract the data, recursive.
 - Scan for one-to-many relationships, extract the data, recursive.

Then::
 - Each fixture will be assigned a UUID in addition to storing its original ID.
 - Relationships will be redefined using the UUID. (i think)

Import data
-----------

The application will import data::

 - Overwriting data if it already exists (determined by a UUID or a combination of fields).
 - Insert if existing fixture cannot be located.

The aim is to NOT force a purge of the database everytime, but to merge and reset existing fixtures.

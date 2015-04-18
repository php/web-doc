# The PHP Manual builds

The PHP Manual is written in [DocBook][docbook] and built by [PhD][phd], and
these builds are rsynced to the mirrors for users to use.

## Mirror builds
The [rsync box][rsync.php.net] builds the manuals every night, at around 23:00 CST.
The mirrors then pick up these builds when they sync, which usually happens every hour.
When a mirror syncs depends on how its cron is set up.

## Doc server builds
The [docs development server][docs.php.net] builds the manual four times a day
(0:15 6:15 12:15 and 18:15 UTC). This takes place on the [euk2][euk2] server.
An easy way to see when each translation was last built, is to look at the
[doc downloads page with dates][download-docs]. Also note that several old
translations reside on this particular server, as it attempts to build every
translation (both active and inactive).

## CHM builds
The CHM version of the manual is built on a Windows machine and pulled on Fridays,
for distribution to mirrors. [Richard][rquadling] maintains these builds.

## Validation
Aside from running `php configure.php –with-lang=foo` (see [editing](editing.php))
for a language, another way to check if the docs validated is by looking at build
dates on the doc server. See "Doc server builds", above.

## Additional notes
- If a manual does not validate on Friday, it will not be pushed to the mirrors
  until it does validate (hopefully, the upcoming Friday).
- Only active translations are selectable/downloadable, and this is managed in
  [phpweb/includes/languages.inc][languages.inc]

## The humans who manage these
If there is a problem with the synced builds, it's wise to contact
[Derick][derick] or [Hannes][bjori].
If a problem exists on the development server ([docs.php.net][docs.php.net]),
then contact the documentation team.

[docbook]: http://www.docbook.org/
[phd]: http://doc.php.net/phd.php
[rsync.php.net]: https://wiki.php.net/systems/sc2
[docs.php.net]: http://docs.php.net
[euk2]: https://wiki.php.net/systems/euk2
[download-docs]: http://docs.php.net/download-docs.php?sizes=1
[fetch-chms]: http://svn.php.net/viewvc/phpdoc/doc-base/trunk/scripts/fetch-chms.php?view=markup
[languages.inc]: http://git.php.net/?p=web/php.git;a=blob;f=include/languages.inc
[rquadling]: http://people.php.net/rquadling
[derick]: http://people.php.net/derick
[bjori]: http://people.php.net/bjori

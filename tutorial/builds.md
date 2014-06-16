# The PHP Manual builds

The PHP Manual is written in DocBook and built by PhD, and these builds are rsynced to the mirrors for users to use.

## Mirror builds
The [rsync box](https://wiki.php.net/systems/sc1) builds the manuals each week, at 10:46 UTC on Fridays. 
The mirrors then pickup these builds when they sync, which usually happens every hour.  When mirrors sync 
depends on how their cron is setup.

## Doc server builds
The [documentation server](http://doc.php.net) builds and displays the manual four times daily (0:15 6:15 12:15
and 18:15 UTC). This takes place on the [euk2](https://wiki.php.net/systems/euk2) server. The simplest way to see
when each translation last built, see the [doc downloads page with dates](http://docs.php.net/download-docs.php?sizes=1).
Also note that several old translations reside on this server, as it attempts to build every translation
(both active and inactive).

## CHM builds
The CHM version of the manual is built on a Windows machine and are pulled weekly on Fridays. Richard maintains
these builds. Due to the security profile on the Windows machine, rsync is not used. Instead, a normal HTTP request
is used.

## Validation
Aside from running `php configure.php –with-lang=foo` for a language, another way to check if manuals validated is
by looking at build dates on the doc server. See this list of [downloadable docs](http://docs.php.net/download-docs.php?sizes=1)
for more information. These include the dates that the manual for each language successfully built.

## Additional notes
- If a manual does not validate on Friday, it will not be pushed to the mirrors until hopefully the upcoming Friday.
- Only active translations are selectable/downloadable, and this is managed in [phpweb/includes/languages.inc](http://git.php.net/?p=web/php.git;a=blob;f=include/languages.inc)

## The humans who manage these
If there is a problem with the synced builds, it's wise to contact [Derick](http://people.php.net/derick) or 
[Hannes](http://people.php.net/bjori). If a problem exists on the developmental server (docs.php.net),
then contact the documentation team.
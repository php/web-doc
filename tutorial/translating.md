# Translating documentation

**Watch out:** this chapter describes special parts of the whole editing process.
You will also have to follow other steps from the [editing manual sources](editing.php) section.

Translating documentation into other languages might look like a complicated
process, but in fact, it's rather simple.

Every file in SVN has a *revision number*. It is basically the current version of
the specified file. We use revisions to check if a file is synchronized with its
English counterpart: to find out if the translation is up-to-date. That's why every
file in your translation requires an EN-Revision comment with the following syntax:
```
<!-- EN-Revision: [some number] Maintainer: [username] Status: ready -->
```
The most important part of this comment is the revision number of the English file
that this translated file is based on. Let's see examples:

## Translating new file
Say you want to translate the documentation of the `in_array()` function, which
doesn't exist in your language yet. Open the file `phpdoc/en/reference/array/in-array.xml`
and copy the revision number. The English file's header might look like this:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- $Revision: 310394 $ -->
```

So our revision number is `310394`. Let's see how your translated file header
should look like if we assume that your SVN username is *johnsmith*:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 310394 Maintainer: johnsmith Status: ready -->
<!-- $Revision$ -->
```

`$Revision$` is an SVN keyword, which will be replaced with the number of current
revision when you commit your changes. The revision number that you have copied
from the English file was created this way.

The rule is simple: if your revision number is equal to the revision number of
the English file you've translated, then your translation is up-to-date.
Otherwise, it needs to be synced.

## Updating translation of existing file
Let's assume that you want to update the translation of `password_needs_rehash()`.
There are two simple ways to see which files require updating and what has to be
changed to sync with English version: using [Online Editor](https://edit.php.net)
or [doc.php.net tools](http://doc.php.net). The second way is described below.

Choose your language from the right sidebar and then use the "Outdated files" tool.
Filter files by directory or username (username used here comes from the `Mantainer`
variable in the header comment described above). Let's assume that the tool marked
`password-needs-rehash.xml` as outdated. Click on the filename and you will see
*diff* - list of changes between two versions of file: your version (current
number in EN-Revision in your translation) and newest version in the English source
tree. The example below should what the diff might look like:

```
--- phpdoc/en/trunk/reference/password/functions/password-needs-rehash.xml	2013/06/21 12:24:55	330609
+++ phpdoc/en/trunk/reference/password/functions/password-needs-rehash.xml	2014/03/24 20:23:27	333093
@@ -12,8 +12,8 @@
   <methodsynopsis>
    <type>boolean</type><methodname>password_needs_rehash</methodname>
    <methodparam><type>string</type><parameter>hash</parameter></methodparam>
-   <methodparam><type>string</type><parameter>algo</parameter></methodparam>
-   <methodparam choice="opt"><type>string</type><parameter>options</parameter></methodparam>
+   <methodparam><type>integer</type><parameter>algo</parameter></methodparam>
+   <methodparam choice="opt"><type>array</type><parameter>options</parameter></methodparam>
   </methodsynopsis>
   <para>
    This function checks to see if the supplied hash implements the algorithm
```

The first two lines indicate the compared revisions. The first was taken from the
EN-Revision number and the second is the current version of this file in English.

As you can see, there is a difference between two lines. The `types` for the
parameters `options` and `algo` in the synopsis had been changed from `string`,
to `integer` and `array` respectively. You have to perform these changes in your
translation to make it up-to-date. Open `phpdoc/{LANG}/reference/password/functions/password-needs-rehash.xml`
and change those lines to match the English version.

Then update the EN-Revision number in the header comment. You can also add your
credits using the CREDITS tag. Your file header might look like this initially:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 330609 Maintainer: someone Status: ready -->
<!-- $Revision: 123456$ -->
```
and after changes it should looke like this:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 333093 Maintainer: someone Status: ready -->
<!-- $Revision$ -->
<!-- CREDITS: johnsmith -->
```
The new EN-Revision number came from the diff shown above. If you want to add
yourself to a CREDITS tag that already exists, separate
usernames with a comma, i.e.: `<!-- CREDITS: george, johnsmith -->`.

Your translation is now up-to-date. It is quite a long process but it's simple
and logical when you get used to it.

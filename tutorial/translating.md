# Translating documentation

**Watch out:** this chapter describes special part of whole editing process. You have to follow other steps from
[editing manual sources](editing.md) section.

Translating documentation into other languages might look like a complicated process, but in fact, it's rather simple.
Every file in SVN has *revision*. It is basically current version of specified file. We use revisions to check if file
is synchronized with English version, so to find out if translation is up-to-date. That's why every file in your
translation requires EN-Revision comment with following syntax:
`<!-- EN-Revision: [some number] Maintainer: [username] Status: ready -->`
The most important part of this comment is revision of English file which translated version is based on. Let's see
examples:

## Translating new file
You want to translate documentation of `in_array()` function, which doesn't exists in your language yet. Open the file
`phpdoc/en/reference/array/in-array.xml` and copy number of revision. Sample header might look like this:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- $Revision: 310394 $ -->
```

So our number is `310394`. Let's see how your translated file header should look like if we assume that your SVN
username is *johnsmith*:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 310394 Maintainer: johnsmith Status: ready -->
<!-- $Revision$ -->
```

`$Revision` is a kind of macro which will be replaced with number of current revision when you commit your changes.
Revision number you have copied from english file was created this way.

The rule is simple: if your revision number is equal to revision number of english file you've translated, it means
that your translation is up-to-date. Otherwise, it needs to be synced.

## Updating translation of existing file
Let's assume you want to update translation of `password_needs_rehash()`. There are two simple ways
to see which files require update and what have to be changed to sync with English version: using
[Online Editor](https://edit.php.net) or [doc.php.net tools](http://doc.php.net). Second way is described below.

Choose your language from right sidebar and then use "Outdated files" tool. Filter files by directory or username
(username used here comes from `Mantainer` variable in comment described above). Let's assume that script marked
`password-needs-rehash.xml` as outdated. Click on filename and you will see *diff* - list of changes between two
versions of file: your version (current number in EN-Revision in your translation) and newest version in English
tree. This is sample diff:

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

First two lines indicate compared revisions. First was taken from your EN-Revision tag and second is current version
of this file in English. As you can see, there is a difference between two lines. Types of parameters `options` and
`algo` in function synopsis had been changed from `string` to `integer` and `array`. You have to perform this changes
in your translation to make it up-to-date. Open `phpdoc/{LANG}/reference/password/functions/password-needs-rehash.xml`
and change those lines to match English version.

Then update EN-Revision number in header. You can also add your credits using CREDITS tag. Your file header might look like this:
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
Numbers came from diff showed above. If you want to add yourself to credits tag which already exists, separate
usernames with coma, i.e.: `<!-- CREDITS: george, johnsmith -->`.

Finally, your translation is up-to-date. It is quite long process but it's simple and logical when you get used to.
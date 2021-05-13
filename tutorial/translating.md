# Translating documentation

**Watch out:** this chapter describes special parts of the whole editing process.
You will also have to follow other steps from the [editing manual sources](editing.php) section.

Translating documentation into other languages might look like a complicated
process, but in fact, it's rather simple.

Every file in Git has a *revision*. It is basically the current version of
the specified file. We use revisions to check if a file is synchronized with its
English counterpart: to find out if the translation is up-to-date. That's why every
file in your translation requires an `EN-Revision` comment with the following syntax:
```
<!-- EN-Revision: [some number] Maintainer: [username] Status: ready -->
```
The most important part of this comment is the revision number of the English file
that this translated file is based on. Let's see examples:

## Translating new file
Say you want to translate the documentation of the `in_array()` function, which
doesn't exist in your language yet.

If you want to translate the file `/reference/array/functions/in-array.xml`,
run the following command in cloned `doc-en` git repository and copy the output git hash value: 

```
git --no-pager log -n 1 --pretty=format:%H -- reference/array/functions/in-array.xml
68a9c82e06906a5c00e0199307d87dd3739f719b
```

So our English revision is `68a9c82e06906a5c00e0199307d87dd3739f719b`.
Let's see how your translated file header should look like if we assume that your username is *johnsmith*:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 68a9c82e06906a5c00e0199307d87dd3739f719b Maintainer: johnsmith Status: ready -->
```

The rule is simple: if your revision is equal to the revision of
the English file you've translated, then your translation is up-to-date.
Otherwise, it needs to be synced.

## Updating translation of existing file
Let's assume that you want to update the translation of `in_array()`.
There are two simple ways to see which files require updating and what has to be changed to sync with the English version:
using [Online Editor](https://edit.php.net) (required PHP account)
or using command line. The second way is described below.

Clone the [doc-base](https://github.com/php/doc-base) repository one level with 
[doc-en](https://github.com/php/doc-en) and your language repositories, so that the structure is as follows:

```
├── doc-base/
├── en/
└── {LANG}/
```

Then run the next script and open `revcheck.html` in any browser: 

```
php doc-base/scripts/revcheck.php {LANG} > revcheck.html
```

In "Outdated Files" section you can see actual English revision and yours.

To get diff between revisions, run the script:

```
git --no-pager diff 8b5940cadeb4f1c8492f4a7f70743a2be807cf39 68a9c82e06906a5c00e0199307d87dd3739f719b reference/array/functions/in-array.xml
```

where the first revision is yours, and the second one is the current English revision.

The example below should what the diff might look like:

```
--- a/reference/array/functions/in-array.xml
+++ b/reference/array/functions/in-array.xml
@@ -14,7 +14,7 @@
    <methodparam choice="opt"><type>bool</type><parameter>strict</parameter><initializer>&false;</initializer></methodparam>
   </methodsynopsis>
   <para>
-   Searches <parameter>haystack</parameter> for <parameter>needle</parameter> using loose comparison
+   Searches for <parameter>needle</parameter> in <parameter>haystack</parameter> using loose comparison
    unless <parameter>strict</parameter> is set.
   </para>
  </refsect1>
```

As you can see, there is a difference in the function description.
The line `Searches <parameter>haystack</parameter> for <parameter>needle</parameter> using loose comparison`
replaced with `Searches for <parameter>needle</parameter> in <parameter>haystack</parameter> using loose comparison`.

You have to perform these changes in your translation to make it up-to-date.
Open `reference/array/functions/in-array.xml` in the translation repository
and change this line to match the English version.

Then update the `EN-Revision` in the header comment. You can also add your
credits using the CREDITS tag. Your file header might look like this initially:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 8b5940cadeb4f1c8492f4a7f70743a2be807cf39 Maintainer: someone Status: ready -->
```
and after changes it should look like this:
```
<?xml version="1.0" encoding="utf-8"?>
<!-- EN-Revision: 68a9c82e06906a5c00e0199307d87dd3739f719b Maintainer: someone Status: ready -->
<!-- CREDITS: johnsmith -->
```
The new EN-Revision came from the diff shown above. If you want to add
yourself to a CREDITS tag that already exists, separate
usernames with a comma, i.e.: `<!-- CREDITS: george, johnsmith -->`.

Your translation is now up-to-date. It is quite a long process but it's simple
and logical when you get used to it.

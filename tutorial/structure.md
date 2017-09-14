# Manual sources structure

## Downloading the PHP Documentation Source
The PHP Manual sources are currently stored in our Subversion (SVN) repository.
You don't need SVN access to checkout (download) the files, but you do need it
if you want to send your changes to our server.

This tutorial assumes that you have basic terminal and Subversion knowledge, although
don't worry if you've never used Subversion as only basic commands are used, and our
setup is simple (e.g., no branches).

To checkout the documentation source, use a modified version of the following command
where you change `{LANG}` to a desired language, such as `en` for only English:

```
svn checkout https://svn.php.net/repository/phpdoc/modules/doc-{LANG} phpdoc-{LANG}
```

This command creates a directory named `phpdoc-{LANG}` because the source is 
the first 'svn checkout' argument, and the second argument defines the directory name 
that stores the checked out directories and files (this name can be anything you wish).
This directory will contain a directory with the sources of your chosen language,
named *{LANG}*, and also *doc-base* folder, which is home to some helpful tools.
The "en" language will always be present, as explained below.

The documentation source is stored under "en". To only edit the source files, and
not a translation, use:

```
svn checkout https://svn.php.net/repository/phpdoc/modules/doc-en phpdoc-en
```

The "modules" directory used by these examples defines a set of SVN external definitions 
that are configured to checkout multiple source directories. For example:

- *doc-en*: checks out "en" and "doc-base"
- *doc-fr*: checks out "en", "fr", and "doc-base"
- *doc-all*: checks out all languages (there are a lot!) along with "en" and "doc-base"

For example, to translate the manual into French, use "doc-fr" instead of "doc-en":

```
svn checkout https://svn.php.net/repository/phpdoc/modules/doc-fr phpdoc-fr
```

For additional information about language codes and available translations, see `http://doc.php.net/revcheck.php`.

## Files structure
**Note for translators:** if any of the source files don't exist in your translation, the English content will be used
during the building process. This means that you *must not* place untranslated files in your translation tree. Otherwise,
it will lead to a mess, confusion and may break some tools.

The structure of the manual sources is hopefully rather intuitive. The most
complicated part is the documentation for extensions, which is also the biggest
part of manual as all functions are grouped into extensions.

The documentation for extensions is located in `/phpdoc/{LANG}/reference/extension_name/`.  For example, 
the calendar extension documentation exists in  `/phpdoc/{LANG}/reference/calendar/`. There you'll find several files:
- *book.xml* - acts as the container for the extension and contains the preface. Other files (like examples.xml)
are included from here.
- *setup.xml* - includes setup, install and configuration documentation
- *constants.xml* - lists all constants that the extension declares, if any
- *configure.xml* - usually this information is in setup.xml, but if the file exists it is magically
included into setup.xml
- *examples.xml* - various examples
- *foo.xml* - example, foo can be anything specific to a topic. Just be sure to include via book.xml.

A procedural extension (like calendar) also has:
- *reference.xml* - container for the functions, rarely contains any info
- *functions/* - folder with one XML file per function that the extension declares

And OO extensions (such as imagick) contain: 
- *classname.xml* - container for the methods defined by the class, contains also basic info about it
- *classname/* - folder with one XML per method that the class declares

Note: *classname* is the lowercased name of the class, not a literal file or directory name.

There are some other important files, not related with extensions.
- *{LANG}/language-defs.ent* - contains local entities used by this language. Some common ones are
  the main part titles, but you should also put entities used only by this language's files here.
- *{LANG}/language-snippets.ent* - longer often used XML snippets translated to this language.
  Including common warnings, notes, etc.
- *{LANG}/translation.xml* - this file is used to store all central translation info, like a small
  intro text for translators and the persons list. This file is not present in the English tree.

The next chapter will discuss how to [edit the manual sources](editing.php).

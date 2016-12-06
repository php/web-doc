# Manual sources structure

## Downloading sources
The PHP Manual sources are currently stored in our Subversion (SVN) repository.
You don't need SVN access to checkout (download) the files, but you do need it
if you want to send your changes to our server.

This tutorial assumes that you have basic knowledge about SVN.
If not, you can read [Version Control with Subversion](http://svnbook.red-bean.com/).
In order to checkout the manual files, use the following command:

```
svn checkout https://svn.php.net/repository/phpdoc/modules/doc-{LANG} phpdoc
```

This command will create a directory named `phpdoc` (the name can be anything you wish).
This directory will contain a folder with the sources of your chosen language
(named *{LANG}*) and a *doc-base* folder, which is home to some helpful tools.

When checking-out manual sources, you can also supply *all* as *{LANG}* making your
URL `https://svn.php.net/repository/phpdoc/modules/doc-all`. This is kind of "magical" module,
which fetches every single language version of the manual, as well as `doc-base` folder
into your `phpdoc` dir.

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

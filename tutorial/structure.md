# Manual sources structure

## Downloading sources
PHP Manual sources are currently stored in Subversion (SVN) repository. You don't need SVN access to checkout (download)
them, but you need it if you want to send your changes to our server.

This tutorial assumes that you have basic knowledge about SVN. If not, you can read {TODO}. In order to checkout manual
files, use following command:

### For editors
`svn checkout https://svn.php.net/repository/phpdoc/modules/doc-en phpdoc`

### For translators
`svn checkout https://svn.php.net/repository/phpdoc/modules/doc-{LANG} phpdoc`

Both commands will create directory named phpdoc, however, the name can be anything you wish. This directory will
contain folder with sources of your language (named *{LANG}*) and *doc-base* with some helpful tools.

## Files structure
**Note for translators: ** if any of source files doesn't exists in translation, English file will be used
while building process. This means that you *cannot* place untranslated files in your translation tree. Otherwise,
it will lead to mess, confusion and may break some tools.

Structure of manual sources is rather intuitive. The most complicated part is documentation for extensions
(which is the biggest part of manual, because all functions are grouped into extensions).

The documentation for extensions is located in `/phpdoc/{LANG}/reference/extension_name/`.  For example, 
the calendar extension documentation exists in  `/phpdoc/{LANG}/reference/calendar/`. There you'll find several files:
- *book.xml* - acts as the container for the extension and contains the preface. Other files (like examples.xml)
are included from here.
- *setup.xml* - includes setup, install and configuration documentation
- *constants.xml* - lists all constants the extension declares, if any
- *configure.xml* - usually this information is in setup.xml, but if the file exists it is magically
included into setup.xml
- *examples.xml - various examples
- *foo.xml* - example, foo can be anything specific to a topic. Just be sure to include via book.xml.

A procedural extension (like calendar) also has:
- *reference.xml* - container for the functions, rarely contains any info
- *functions/* - folder with one XML file per function that the extension declares

And OO extensions (such as imagick) contain: 
- *classname.xml* - container for the methods defined by the class, contains also basic info about it
- *classname/* - folder with one XML per method that the class declares

Note: *classname* is the lowercased name of the class, not a literal file or directory name.

Next chapter will discuss how to [edit manual sources](editing.md).
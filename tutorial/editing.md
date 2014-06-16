# Editing manual sources

## Introduction
When editing or translating manual you have to remember some things:
- use only UTF-8 encoding
- follow [style guidelines](style.md)

## Editing existing documentation
Simply open the files and edit them.

## Adding new documentation
When adding new functions or methods, there are a couple of options. Either way, the generated (or copied) files
will need to be filled out.

### Option A: Generating files using docgen
This is preferred way to generate files for new functions or methods. The `docgen` script is found within 
the PHP documentation (phpdoc/scripts/docgen/) and uses Reflection to generate documentation (DocBook) files.
Fill in skeleton files before you commit them!

### Option B: Copy skeleton files
This involves copying the skeleton files into the correct location:
```
cp /phpdoc/RFC/skeletons/method.xml classname/methodname.xml   #for new methods
cp /phpdoc/RFC/skeletons/function.xml functions/functionname.xml #for new functions
```

Note: *classname*, *methodname* and *functionname* are lowercased names of the class, method or function, respectively,
not a literal file name.

Remember about extension [structure](structure.md) when copying those files.

## Translating documentation
Translation process has been described in [separate chapter](translating.md).

## Validating your changes
Every time you make changes to documentation sources (both English or translation) you have to validate your changes.
Proper script is distributed with documentation sources, so you already have it in *doc-base* directory. All you have
to do to validate changes is run configure.php:
```
$ cd phpdoc
$ php configure.php --with-lang={LANG}
```
If your language is English you can omit whole lang parameter and only execute `php configure.php`. When the above
outputs something like “All good. Saving .manual.xml… done.” then you know it validates. You can commit your
changes now.

## Commit changes
If you have access to SVN, you can commit modified files.

## Viewing changes online
Documentation is builded every Friday. It applies to all formats - online, offline HTML files and CHM. However,
there is a special mirror - http://docs.php.net/ - where manual is updated from sources every six hours. If any
errors occured, special message will be delivered to your lang's mailinglist. Read more about manual builds in 
[dedicated appendix](builds.md).

Last chapter contains [style guidelines](style.md) you are obliged to follow. Read them carefully.
# doc.php.net

This repository contains files of doc.php.net, the site for documentation
contributors to find useful guides, tools, statistics etc.

## Running a local version

This can be run using PHP's [built-in web server][webserver]
for local development. For the guide pages to work, you also
need a local clone of the `doc-base` repository.

To generate the images for translation status, the GD extension
is required.

To generate the translation status, you'll also need the English version
of the documentation and whatever languages you want to generate data
about.

```sh
# Leave empty to do all languages, must include 'en' if any listed
$ PHP_LANGS=""
$ git clone https://github.com/php/web-doc.git
$ git clone https://github.com/php/doc-base.git
$ php doc-base/languages.php --clone ${PHP_LANGS:---all}
$ SQLITE_DIR="${PWD}/web-doc/sqlite"
$ mkdir -p ${SQLITE_DIR}
$ php doc-base/scripts/translation/genrevdb.php ${SQLITE_DIR}/status.sqlite.tmp ${PHP_LANGS:-$(php doc-base/languages.php --all --list-ssv)}
$ cd web-doc
$ git clone https://github.com/php/web-shared.git shared
$ BASE_DOCS_PATH="${PWD}/../doc-base/docs" php -S localhost:8080 router.php
```

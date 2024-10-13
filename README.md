# doc.php.net

This repository contains files of doc.php.net, the site for documentation
contributors to find useful guides, tools, statistics etc.

## Running a local version

This can be run using PHP's [built-in web server][webserver]
for local development. For the guide pages to work, you also
need a local clone of the `doc-base` repository.

```sh
$ git clone https://github.com/php/web-doc.git
$ git clone https://github.com/php/doc-base.git
$ cd web-doc
$ git clone https://github.com/php/web-shared.git shared
$ BASE_DOCS_PATH="${PWD}/../doc-base/docs" php -S localhost:8080 router.php
```

The instructions below here are older and you may need to adapt them to
run the tools for showing translation information.

---

## Installation
Requirements:
- PHP 8
- GD extension (for generating charts)
- SQLite3

1. Unpack a copy of JpGraph 4.3.4 into `/include/jpgraph/` ([here](http://jpgraph.net/download/download.php?p=1))
2. Increase memory limit for PHP scripts to at least 32MB
3. Fill in two configuration files
- `build-ops.php.sample` (rename it to `build-ops.php`)
- `@GITDIR@` - absolute path to dir where scripts will clone GIT repos to and then use them for generating data
- `@SQLITEDIR@` - absolute path to `/sqlite/` directory
- `build-ops.sample` (rename it to `build-ops` [no ext])
- `@PHP@` - path to the PHP executable file
- `@DOCWEB@` - absolute path to `/www/` directory
- `@PHDDIR@` - absolute path to directory with PhD installed from Git master
- `@SCRIPTSDIR@`- absolute path to `/scripts/` directory
- `@SQLITEDIR@` - absolute path to `/sqlite/` directory
- `@SRCDIR@` - path to the directory with PHP source code in GIT repo
4. You need to run the scripts/populatedocs.sh to fetch all the required documentation for the site.
5. You need to run the scripts/generation.sh to generate the database and graphs (Time of generation: 2.5009639263153 s)
6. Configure the virtual host under Apache. Current suggested settings are:
```
<VirtualHost 127.0.0.1:80>
 ServerName doc.php.net
 ServerAdmin doc-web@lists.php.net

 DocumentRoot /path/to/docweb/www

 ErrorDocument 404 /error.php
 php_flag register_globals Off
 php_flag magic_quotes_gpc Off
 php_flag magic_quotes_runtime Off

 <Directory /path/to/docweb/www>
  Allowoverride FileInfo Options Limit
  Options -Indexes
 </Directory>
</VirtualHost>
```

## TODO
- add more cowbell

# doc.php.net
This repository contains files of doc.php.net, site created to help documentation contributors doing their job by
providing them useful tools, statistics etc. Site has been partly rewritten in March 2014. If you have suggestions,
please contact us on `php.doc.web` mailing list or contribute by i.e. sending pull request to this repo.

## Installation
Requirements:
- PHP 8
- GD extension (for generating charts)
- SQLite3

1. Unpack a copy of JpGraph 3.0.7 into `/include/jpgraph/` ([here](http://jpgraph.net/download/download.php?p=1))
2. Increase memory limit for PHP scripts to at least 32MB
3. Fill in two configuration files
	- `build-ops.php.sample` (rename it to `build-ops.php`)
		- `@GITDIR@` - absolute path to dir where scripts will clone SVN repos to and then use them for generating data
		- `@SQLITEDIR@` - absolute path to directory where the database will be written
	- `build-ops.sample` (rename it to `build-ops` [no ext])
		- `@PHP@` - path to the PHP executable file
		- `@GITDIR@` - absolute path to dir where scripts will clone SVN repos to and then use them and then use it for generating data
		- `@DOCWEB@` - absolute path to `/www/` directory
		- `@PHDDIR@` - absolute path to directory with PhD installed from Git master
		- `@SCRIPTSDIR@`- absolute path to `/scripts/` directory
		- `@SQLITEDIR@` - absolute path to directory where the database will be written
		- `@SRCDIR@` - path to the directory with PHP source code in SVN repo
4. You need to run the scripts/populatedocs.sh to fetch all the required documentation for the site.
6. You need to run the scripts/generation.sh to generate the database and graphs
7. Configure the virtual host under Apache. Current suggested settings are:
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

# doc.php.net
This repository contains files of doc.php.net, site created to help documentation contributors doing their job by
providing them useful tools, statistics etc. Site has been partly rewritten in March 2014. If you have suggestions,
please contact us on `php.doc.web` mailing list or contribute by i.e. sending pull request to this repo.

## Installation
Requirements:
- PHP 5
- GD extension (for generating charts)
- SQLite3

1. Unpack a copy of JpGraph 3.0.7 into `/include/jpgraph/` ([here](http://jpgraph.net/download/download.php?p=1))
2. You need to run the scripts/populatedocs.sh to fetch all the required documentation for the site.
3. Increase memory limit for PHP scripts to at least 32MB
4. Fill in two configuration files
	- `build-ops.sample.php` (rename it to `build-ops.php`)
		- `@SVNDIR@` - absolute path to dir where scripts will clone SVN repos to and then use them for generating data
		- `@DOCDIR@` - relative path to the directory with documentation in SVN repo
	- `build-ops-sample` (rename it to `build-ops` [no ext])
		- `@PHP@` - path to the PHP executable file
		- `@SVNDIR@` - absolute path to dir where scripts will clone SVN repos to and then use them and then use it for generating data
		- `@DOCDIR@` - relative path to the directory with documentation in SVN repo
		- `@DOCWEB@` - absolute path to directory with this website
		- `@PHDDIR@` - absolute path to directory with PhD installed from Git master
		- `@SCRIPTSDIR@`- absolute path to `/scripts/` directory
		- `@SRCDIR@` - path to the directory with PHP source code in SVN repo
5. Configure the virtual host under Apache. Current suggested settings are:
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
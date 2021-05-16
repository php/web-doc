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
- `@GITDIR@` - absolute path to dir where scripts will clone GIT repos to and then use them for generating data
- `@SQLITEDIR@` - absolute path to `/sqlite/` directory
- `build-ops.sample` (rename it to `build-ops` [no ext])
- `@PHP@` - path to the PHP executable file
- `@GITDIR@` - absolute path to dir where scripts will clone SVN repos to and then use them and then use it for generating data
- `@DOCWEB@` - absolute path to `/www/` directory
- `@PHDDIR@` - absolute path to directory with PhD installed from Git master
- `@SCRIPTSDIR@`- absolute path to `/scripts/` directory
- `@SQLITEDIR@` - absolute path to `/sqlite/` directory
- `@SRCDIR@` - path to the directory with PHP source code in GIT repo
4. You need to run the scripts/populatedocs.sh to fetch all the required documentation for the site.
6. You need to run the scripts/generation.sh to generate the database and graphs (Time of generation: 2.5009639263153 s)
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
  Options -Indexes +ExecCGI
  <Files gitweb.cgi>
   SetHandler cgi-script
  </Files>
 </Directory>
</VirtualHost>
```
8. Create /etc/httpd/conf/extra/gitweb.conf
```
<IfModule mod_alias.c>
  <IfModule mod_mime.c>
    <IfModule mod_cgi.c>
      Define ENABLE_GITWEB
    </IfModule>
    <IfModule mod_cgid.c>
      Define ENABLE_GITWEB
    </IfModule>
  </IfModule>
</IfModule>

<IfDefine ENABLE_GITWEB>
  Alias /gitweb /usr/share/gitweb

  <Directory /usr/share/gitweb>
    DirectoryIndex gitweb.cgi
    Options +FollowSymLinks +ExecCGI
    <Files gitweb.cgi>
      SetHandler cgi-script
    </Files>
    AddHandler cgi-script .cgi
    AllowOverride None
    SetEnv  GITWEB_CONFIG  /etc/gitweb.conf
  </Directory>
</IfDefine>
```
9. Add the following line to /etc/httpd/conf/httpd.conf (cgi.load)
```
LoadModule cgi_module /usr/lib/apache2/modules/mod_cgi.so
```
10. Next we need to make a gitweb config file. Open (or create if it does not exist) the file /etc/gitweb.conf and place this in it:
```
# path to git projects (<project>.git)
$projectroot = "/path/to/docweb/www";

# directory to use for temp files
$git_temp = "/tmp";

# Base URLs for links displayed in the web interface.
our @git_base_url_list = qw(git://git.php.net:);

$feature{'highlight'}{'default'} = [1];
$omit_owner = "true";
$projects_list_description_width = "36";
```
11. Restart apache
```
# systemctl restart apache2
```
12. Create symbolic links for gitweb
```
$ cd /path/to/docweb/www
$ mkdir doc
$ cd doc
```
repeat lines like these for other languages
```
$ ln -s ../../git/de/.git de.git
$ echo "German PHP documentation" > de.git/description
```
11. If there is no problem, open browser and go: http://doc.php.net/docweb/
12. Or: http://doc.php.net/revcheck.php

## TODO
- add more cowbell

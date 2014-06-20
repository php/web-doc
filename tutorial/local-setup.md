# Setting up Documentation environment
This appendix describes how to check out, build and view the PHP documentation locally.

Viewing results as a php.net mirror isn't a simple process, but it can be done.
The following is one route, and it assumes:

- PHP 5.3+ is available
- A web server, specifically (in this example) Apache
- The rsync tool is available
- A basic level of shell/terminal usage, or know that shell commands follow a `$` below
- A desire to build and display the documentation locally
- Your PHP Documentation path will be: `/tmp/svn/doc-en` (change this no doubt)
- Your local mirror path will be: `/home/sites/myphpnet/` (change this no doubt)
- Your local hostname will be: `http://mydocs.site/` (change this no doubt)
- You are using Linux or a Mac (@todo Make this guide Windows friendly)

If you're interested in simply setting up a local PHP mirror (and NOT build the documentation) then
simply follow the php.net [mirroring guidelines](http://php.net/mirroring) and ignore this document.

## Checkout the php documentation from SVN
Assumptions: This assumes using /tmp as the root directory, and checkout of the English language.
Adjust accordingly.

### Option A: Traditional
```
$ mkdir /tmp/svn
$ cd /tmp/svn
$ svn co https://svn.php.net/repository/phpdoc/modules/doc-en doc-en
$ cd /tmp/svn/doc-en
```

### Option B: Automated
```
$ cd /tmp
$ wget https://svn.php.net/repository/phpdoc/doc-base/trunk/scripts/create-phpdoc-setup.php
$ php create-phpdoc-setup.php -h
$ php create-phpdoc-setup.php -b /tmp/svn -l en
$ cd /tmp/svn/doc-en
```

## Validate the PHP Documentation XML
```
$ cd /tmp/svn/doc-en
$ php doc-base/configure.php
```

## Build the documentation
We use PhD to build the documentation. It takes the source that configure.php generates, and builds
and formats the PHP documentation. PhD offers several formats including HTML (multiple or single page),
PDF, Man, Epub, and others, including PHP which is what we use at php.net.

### Install PhD
#### Option A: Traditional
Requirements: PEAR, and an SSL enabled PHP version greater than 5.3.0.
```
$ pear install phpdocs/PhD
$ pear install phpdocs/PhD_Generic phpdocs/PhD_PHP
$ phd --help
```

#### Option B: Automated
The create-phpdoc-setup.php installs PhD.

### Use PhD to build the documentation
```
$ cd /tmp/svn/doc-en
$ php doc-base/configure.php
$ phd --docbook /tmp/svn/doc-en/doc-base/.manual.xml --package PHP --format php --output mydocs
$ cd /tmp/svn/doc-en/mydocs/php-web/
$ ls
```

Note: This builds the php.net version of the documentation, but does not contain
the files and includes used to run php.net. In other words, files like the php.net
headers and footers are not built by PhD and are instead stored in a separate git
module (web-php).

Alternative: The XHTML format is simple and does not require mirroring the php.net
website. The following builds manual pages as plain HTML files:
```
$ cd /tmp/svn/doc-en
$ phd --docbook /tmp/svn/doc-en/doc-base/.manual.xml --package PHP --format xhtml --output mydocs
$ cd /tmp/svn/doc-en/mydocs/php-chunked-xhtml/
$ open function.strlen.html
```

## Set up a local php.net mirror
### Download (rsync) the php.net files
```
$ rsync -avzC --timeout=600 --delete --delete-after --exclude='manual/**' --exclude='distributions/**' --exclude='extra/**' --exclude='backend/notes/**' rsync.php.net::phpweb /home/sites/myphpnet/
```

### Symlink (or move) the generated PHP documentation to your local php.net sources
```
$ ln -s /tmp/svn/doc-en/mydocs/php-web /home/sites/myphpnet/manual/en
```

### Configure Apache
The official PHP [mirroring documentation](http://php.net/mirroring) contains
a detailed example for doing this, but here's a simpler example. Open the Apache
configuration file (e.g., `apache.conf`, `httpd.conf`, `apache/conf.d/virtualhosts/myphp.site`, ...)
and add the virtual host

```
<VirtualHost *>
     <Directory /home/sites/myphpnet>
          Options -Indexes -MultiViews
     </Directory>

     ServerName myphp.site
     DocumentRoot /home/sites/myphpnet/

     # Set directory index
     DirectoryIndex index.php index.html

     # Handle errors with local error handler script
     ErrorDocument 401 /error.php
     ErrorDocument 403 /error.php
     ErrorDocument 404 /error.php

     # Add types not specified by Apache by default
     AddType application/octet-stream .chm .bz2 .tgz .msi
     AddType application/x-pilot .prc .pdb

     # Set mirror's preferred language here
     SetEnv MIRROR_LANGUAGE "en"

     RemoveHandler var

     # Turn spelling support off (which would break URL shortcuts)
     <IfModule mod_speling.c>
       CheckSpelling Off
     </IfModule>
</VirtualHost>
```

Now, adjust /etc/hosts by adding the following:
```
127.0.0.1    myphp.site
```
and restart Apache
```
$ sudo apachectl restart
```

## View the new site
Open [http://myphp.site/manual/en/](http://myphp.site/manual/en/) in your browser.

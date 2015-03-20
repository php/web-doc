# Setting up Documentation environment
This appendix describes how to check out, build and view the PHP documentation locally.

Viewing results as a php.net mirror isn't a simple process, but it can be done.
The following is one route, and it assumes:

- PHP 5.4+ is available
- Git control version system is available
- A basic level of shell/terminal usage, or know that shell commands follow a `$` below
- A desire to build and display the documentation locally
- Your PHP Documentation path will be: `/tmp/svn/doc-en` (change this no doubt)
- Your local mirror path will be: `/home/sites/myphpnet/` (change this no doubt)
- You are using Linux or a Mac (@todo Make this guide Windows friendly)
- You'll replace `path/to/phd/` with your actual path

If you're interested in simply setting up a local PHP mirror (and NOT build the documentation) then
please follow the php.net [mirroring guidelines](http://php.net/mirroring) and ignore this document.

## Checkout the php documentation from SVN
*Assumptions*: This assumes using /tmp as the root directory, and checkout of the English language.
Adjust accordingly.

```
$ mkdir /tmp/svn
$ cd /tmp/svn
$ svn co https://svn.php.net/repository/phpdoc/modules/doc-en doc-en
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
```
$ git clone http://git.php.net/repository/phd.git
$ php path/to/phd/render.php --help
```

### Use PhD to build the documentation
```
$ cd /tmp/svn/doc-en
$ php doc-base/configure.php
$ php path/to/phd/render.php --docbook /tmp/svn/doc-en/doc-base/.manual.xml --package PHP --format php --output mydocs
$ cd /tmp/svn/doc-en/mydocs/php-web/
```

**Note:** This builds the php.net version of the documentation, but does not contain
the files and includes used to run php.net. In other words, files like the php.net
headers and footers are not built by PhD and are instead stored in a separate git
module (web-php).

Alternative: The XHTML format is simple and does not require mirroring the php.net
website. The following builds manual pages as plain HTML files:
```
$ cd /tmp/svn/doc-en
$ php path/to/phd/render.php --docbook /tmp/svn/doc-en/doc-base/.manual.xml --package PHP --format xhtml --output mydocs
$ cd /tmp/svn/doc-en/mydocs/php-chunked-xhtml/
$ open function.strlen.html
```

## Set up a local php.net mirror
### Clone the php.net sources
```
$ git clone http://git.php.net/repository/web/php.git /home/sites/myphpnet/
```

### Symlink (or move) the generated PHP documentation to your local php.net sources
```
$ ln -s /tmp/svn/doc-en/mydocs/php-web /home/sites/myphpnet/manual/en
```

### Run a webserver
We are going to use PHP's built-in web server. Please open another terminal instance for this task.

```
$ cd /home/sites/myphpnet/
$ php -S 0.0.0.0:4000
```

## View the new site
Open [http://localhost:4000/manual/en/](http://localhost:4000/manual/en/) in your browser.

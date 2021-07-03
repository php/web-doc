# Setting up Documentation environment
This appendix describes how to check out, build and view the PHP documentation locally.

Viewing results as a php.net mirror isn't a simple process, but it can be done.
The following is one route, and it assumes:

- PHP 5.4+ is available
- Git version control system is available
- A basic level of shell/terminal usage, or know that shell commands follow a `$` below

If you're interested in simply setting up a local PHP mirror (and NOT build the documentation) then
please follow the php.net [mirroring guidelines](http://php.net/mirroring) and ignore this document.

## Checkout the php documentation from Git
**Assumptions**: This assumes using `/tmp` as the root directory, and checkout of the English language.
Adjust accordingly, including paths, especially if you are on Windows.

```
$ mkdir /tmp/git
$ cd /tmp/git
$ git clone https://github.com/php/doc-en.git doc-en/en
$ cd /tmp/git/doc-en
```

## Validate the PHP Documentation XML
```
$ cd /tmp/git/doc-en
$ php doc-base/configure.php
```

## Build the documentation
We use PhD to build the documentation. It takes the source that configure.php generates, and builds
and formats the PHP documentation. PhD offers several formats including HTML (multiple or single page),
PDF, Man, Epub, and others, including PHP which is what we use at php.net.

### Install PhD
```
$ cd /tmp
$ git clone https://github.com/php/phd.git
$ php phd/render.php --help
```

### Use PhD to build the documentation
```
$ cd /tmp/git/doc-en
$ php doc-base/configure.php
$ php /tmp/phd/render.php --docbook /tmp/git/doc-en/doc-base/.manual.xml --package PHP --format php
$ cd /tmp/git/doc-en/mydocs/php-web/
```

**Note:** This builds the php.net version of the documentation, but does not contain
the files and includes used to run php.net. In other words, files like the php.net
headers and footers are not built by PhD and are instead stored in a separate git
module (web-php).

Alternative: The XHTML format is simple and does not require mirroring the php.net
website. The following builds manual pages as plain HTML files:
```
$ cd /tmp/git/doc-en
$ php doc-base/configure.php
$ php /tmp/phd/render.php --docbook /tmp/git/doc-en/doc-base/.manual.xml --package PHP --format xhtml
$ cd /tmp/phd/output/php-chunked-xhtml/
$ open function.strlen.html
```

## Set up a local php.net mirror
### Clone the php.net sources
```
$ git clone https://github.com/php/web-php.git /home/sites/myphpnet/
```

### Symlink (or move) the generated PHP documentation to your local php.net sources
```
$ ln -s /tmp/phd/output/php-web /home/sites/myphpnet/manual/en
```

Symlinking can also be done on Windows. Just make sure you run `cmd` *as Administrator*.

```
$ cd \home\sites\myphpnet\manual\
$ rmdir /S en
$ mklink /D en \tmp\phd\output\web-php
```

### Run a webserver
We are going to use PHP's built-in web server. Please open another terminal instance for this task.

```
$ cd /home/sites/myphpnet/
$ php -S localhost:8080
```

## View the new site
Open [http://localhost:8080/manual/en/](http://localhost:8080/manual/en/) in your browser.

<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2004 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Authors: Georg Richter <georg@php.net>                               |
|          Gabor Hojsty <goba@php.net>                                 |
| Docweb port: Nuno Lopes <nlopess@php.net>                            |
|              Mehdi Achour <didou@php.net>                            |
|              Sean Coates <sean@php.net>                              |
+----------------------------------------------------------------------+
$Id$
*/

// user agent
define('DOCWEB_CRAWLER_USER_AGENT', 'DocWeb Link Crawler (http://doc.php.net)');

// for results
define('SUCCESS',             0);
define('UNKNOWN_HOST',        1);
define('FTP_CONNECT',         2);
define('FTP_LOGIN',           3);
define('FTP_NO_FILE',         4);
define('HTTP_CONNECT',        5);
define('HTTP_MOVED',          6);
define('HTTP_WRONG_HEADER',   7);
define('HTTP_INTERNAL_ERROR', 8);
define('HTTP_NOT_FOUND',      9);

// lookup
$urlResultLookup = array( // @@@ language-entity these
    SUCCESS             => '&docweb.checkent.result.success;',
    UNKNOWN_HOST        => '&docweb.checkent.result.unknown-host;',
    FTP_CONNECT         => '&docweb.checkent.result.ftp-connect;',
    FTP_LOGIN           => '&docweb.checkent.result.ftp-login;',
    FTP_NO_FILE         => '&docweb.checkent.result.ftp-no-file;',
    HTTP_CONNECT        => '&docweb.checkent.result.http-connect;',
    HTTP_MOVED          => '&docweb.checkent.result.http-moved;',
    HTTP_WRONG_HEADER   => '&docweb.checkent.result.http-wrong-header;',
    HTTP_INTERNAL_ERROR => '&docweb.checkent.result.http-internal-error;',
    HTTP_NOT_FOUND      => '&docweb.checkent.result.http-not-found;',
);
// display extra column (return value)
$urlResultExtraCol = array(
    SUCCESS             => FALSE,
    UNKNOWN_HOST        => FALSE,
    FTP_CONNECT         => FALSE,
    FTP_LOGIN           => FALSE,
    FTP_NO_FILE         => FALSE,
    HTTP_CONNECT        => FALSE,
    HTTP_MOVED          => TRUE,
    HTTP_WRONG_HEADER   => FALSE,
    HTTP_INTERNAL_ERROR => FALSE,
    HTTP_NOT_FOUND      => FALSE,
);

// Schemes currently supported
$schemes = array('http');
if (extension_loaded('openssl')) {
    $schemes[] = 'https';
}
if (function_exists('ftp_connect')) {
    $schemes[] = 'ftp';
}

// timeout
define('URL_CONNECT_TIMEOUT', 10);

// allow forking?
define('URL_ALLOW_FORK',    function_exists('pcntl_fork') && isset($_ENV['NUMFORKS']));
define('NUM_ALLOWED_FORKS', URL_ALLOW_FORK ? $_ENV['NUMFORKS'] : 0);

// SQLite DB file
define('URL_ENT_SQLITE_FILE', SQLITE_DIR . "checkent_{$entType}.sqlite");

/**
 * Opens a new SQLite connection
 *
 * @return resource SQLite connection
 */
function url_ent_sqlite_open()
{
    if (!($sqlite = sqlite_open(URL_ENT_SQLITE_FILE, 0666))) {
        echo "Error creating database.\n";
    }
    return $sqlite;
}

/**
 * Handles relative HTTP URLs
 *
 * @param string  $url    URL to handle
 * @param array   $parsed result of parse_url()
 * @return string fixed URL
 */
function fix_relative_url ($url, $parsed)
{
    if ($url{0} == '/') {
        return "{$parsed['scheme']}://{$parsed['host']}{$url}";
    }

    if (preg_match('@(?:f|ht)tps?://@S', $url)) {
        return $url;
    }

    /* try to be RFC 1808 compliant */
    $path = $parsed['path'] . $url;
    $old  = '';

    do {
        $old  = $path;
        $path = preg_replace('@[^/:?]+/\.\./|\./@S', '', $path);
    } while ($old != $path);

    return "{$parsed['scheme']}://{$parsed['host']}{$path}";
}

/**
 * Checks a URL (actually fetches the URL and returns the status)
 *
 * @param int    $num        sequence number of URL
 * @param string $entity_url URL to check
 * @return array
 */
function check_url ($num, $entity_url)
{
    static $old_host = '';

    // Get the parts of the URL
    $url    = parse_url($entity_url);
    $entity = $GLOBALS['entity_names'][$num];

    // sleep if accessing the same host more that once in a row
    if ($url['host'] == $old_host) {
        sleep(5);
    } else {
        $old_host = $url['host'];
    }

    // Try to find host
    if (gethostbyname($url['host']) == $url['host']) {
        return array(UNKNOWN_HOST, array($num));
    }

    switch($url['scheme']) {

        case 'http':
        case 'https':
            if (isset($url['path'])) {
                $url['path'] = $url['path'] . (isset($url['query']) ? '?' . $url['query'] : '');
            } else {
                $url['path'] = '/';
            }

            /* check if using secure http */
            if ($url['scheme'] == 'https') {
                $port   = 443;
                $scheme = 'ssl://';
            } else {
                $port   = 80;
                $scheme = '';
            }
            $port = isset($url['port']) ? $url['port'] : $port;

            $junk = '';
            if (!$fp = @fsockopen($scheme . $url['host'], $port, $junk, $junk, URL_CONNECT_TIMEOUT)) {
                return array(HTTP_CONNECT, array($num));

            } else {
                $query = "HEAD {$url['path']} HTTP/1.0\r\n"
                        ."Host: {$url['host']}\r\n"
                        ."User-agent: ". DOCWEB_CRAWLER_USER_AGENT ."\r\n"
                        ."Connection: close\r\n"
                        ."\r\n";
                fputs($fp, $query);

                $str = '';
                while (!feof($fp)) {
                    $str .= @fgets($fp, 2048);
                }
                fclose ($fp);

                if (preg_match('@HTTP/1.\d (\d+)(?: .+)?@S', $str, $match)) {
                    if ($match[1] != '200') {
                        switch ($match[1])
                        {
                            case '500' :
                            case '501' :
                                return array(HTTP_INTERNAL_ERROR, array($num));
                            break;

                            case '404' :
                                return array(HTTP_NOT_FOUND, array($num));
                            break;

                            case '301' :
                            case '302' :
                                if (preg_match('/Location: (.+)/', $str, $redir)) {
                                    return array(HTTP_MOVED, array($num, fix_relative_url($redir[1], $url)));
                                } else {
                                    return array(HTTP_WRONG_HEADER, array($num, $str));
                                }
                            break;

                            default :
                                return array(HTTP_WRONG_HEADER, array($num, $str));
                        }
                    } // error != 200
                } else {
                    return array(HTTP_WRONG_HEADER, array($num, $str));
                }
            }
            break;

        case 'ftp':
            if ($ftp = @ftp_connect($url['host'])) {

                if (@ftp_login($ftp, 'anonymous', 'IEUser@')) {
                    $flist = ftp_nlist($ftp, $url['path']);
                    if (!count($flist)) {
                        return array(FTP_NO_FILE, array($num));
                    }
                } else {
                    return array(FTP_LOGIN, array($num));
                }
                @ftp_quit($ftp);
            } else {
                return array(FTP_CONNECT, array($num));
            }
            break;
    }
    return array(SUCCESS, array($num));
}

/**
 * Stores the result of the check_url function
 *
 * @param resource $sqlite sqlite connection resource
 * @param int      $num    entity url number (sequence)
 * @param string   $name   entity name
 * @param string   $url    entity url
 * @param array    $result result of check_url()
 * @return void
 */
function url_store_result($sqlite, $num, $name, $url, $result)
{
    if (!$sqlite) {
       $sqlite = url_ent_sqlite_open();
    }
    $return_val = isset($result[1][1]) ? $result[1][1] : '';
    $sql = "
        INSERT
        INTO
            checked_urls (url_num, entity, url, check_result, return_val)
        VALUES
            (
                $num,
                '". sqlite_escape_string($name) ."',
                '". sqlite_escape_string($url) ."',
                {$result[0]},
                '". sqlite_escape_string($return_val) ."'
            )
    ";
    sqlite_query($sqlite, $sql);
}
?>

<?php
/**
 * +----------------------------------------------------------------------+
 * | PHP Documentation Site Source Code                                   |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 1997-2005 The PHP Group                                |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 3.0 of the PHP license,       |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available at through the world-wide-web at                           |
 * | http://www.php.net/license/3_0.txt.                                  |
 * | If you did not receive a copy of the PHP license and are unable to   |
 * | obtain it through the world-wide-web, please send a note to          |
 * | license@php.net so we can mail you a copy immediately.               |
 * +----------------------------------------------------------------------+
 * | Authors: Georg Richter <georg@php.net>                               |
 * |          Gabor Hojsty <goba@php.net>                                 |
 * | Docweb port: Nuno Lopes <nlopess@php.net>                            |
 * |              Mehdi Achour <didou@php.net>                            |
 * +----------------------------------------------------------------------+
 *   $Id$
 */

set_time_limit(0);
$inCli = true;
include '../include/init.inc.php';

define('DOCWEB_CRAWLER_USER_AGENT', 'DocWeb Link Crawler (http://doc.php.net)');

switch (isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : false) {
    case 'phpdoc':
        $filename = CVS_DIR . '/phpdoc-all/entities/global.ent';
        break;

    case 'peardoc':
        $filename = CVS_DIR . '/peardoc/global.ent';
        break;

    case 'smarty':
        $filename = CVS_DIR . '/smarty/docs/entities/global.ent';
        break;
    
    default:
        echo "Usage: {$_SERVER['argv'][0]} phpdoc|peardoc|smarty\n";
        die();
}


// Schemes currently supported
$schemes = array('http');
if (function_exists('ftp_connect')) {
    $schemes[] = 'ftp';
}

if (extension_loaded('openssl')) {
    $schemes[] = 'https';
}

// constants for errors
define('UNKNOWN_HOST',        1);
define('FTP_CONNECT',         2);
define('FTP_LOGIN',           3);
define('FTP_NO_FILE',         4);
define('HTTP_CONNECT',        5);
define('HTTP_MOVED',          6);
define('HTTP_WRONG_HEADER',   7);
define('HTTP_INTERNAL_ERROR', 8);
define('HTTP_NOT_FOUND',      9);


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
      
            if (!$fp = @fsockopen($scheme . $url['host'], $port)) {
                return array(HTTP_CONNECT, array($num));
      
            } else {
                fputs($fp, "HEAD {$url['path']} HTTP/1.0\r\nHost: {$url['host']}\r\nUser-agent: ". DOCWEB_CRAWLER_USER_AGENT ."\r\nConnection: close\r\n\r\n");
      
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
}


if (!$file = @file_get_contents($filename)) {
    // ouput the html
    echo "<?php include_once '../include/init.inc.php';
           echo site_header('docweb.common.header.checkent'); 
           echo '<h1>No entities found</h1>';
           echo site_footer(); ?>";
    exit;
}

$array = explode('<!-- Obsoletes -->', $file);

// Find entity names and URLs
$schemes_preg = '(?:' . join('|', $schemes) . ')';
preg_match_all("@<!ENTITY\s+(\S+)\s+([\"'])({$schemes_preg}://[^\\2]+)\\2\s*>@U", $array[0], $entities_found);

// These are the useful parts
$entity_names = $entities_found[1];
$entity_urls  = $entities_found[3];

$errors = array();
$numb = 0;
$entity_urls = array_slice($entity_urls, 0, 5);

// Walk through entities found
foreach ($entity_urls as $num => $entity_url) {

    ++$numb;
    $err = check_url($num, $entity_url);
    $errors[$err[0]][] = $err[1];

}

// ouput the html
echo "<?php include_once '../include/init.inc.php';
echo site_header('docweb.common.header.checkent'); 
?><p>Last check: " . date('r') . "<br/>
Supported Protocols: " . implode(', ', $schemes) . '</p><p>&nbsp;</p>';

if (isset($errors[UNKNOWN_HOST])) {
    echo '<h2>Unknown host (' . count($errors[UNKNOWN_HOST]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>URL</th>
  </tr>';

    foreach ($errors[UNKNOWN_HOST] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
       </tr>';
    }

    echo '</table>';
}

if (isset($errors[HTTP_CONNECT])) {
    echo '<h2>HTTP Failed to connect (' . count($errors[HTTP_CONNECT]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>URL</th>
  </tr>';

    foreach ($errors[HTTP_CONNECT] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
       </tr>';
    }

    echo '</table>';
}

if (isset($errors[FTP_CONNECT])) {
    echo '<h2>FTP Failed to connect (' . count($errors[FTP_CONNECT]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>URL</th>
  </tr>';

    foreach ($errors[FTP_CONNECT] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
       </tr>';
    }

    echo '</table>';
}

if (isset($errors[FTP_LOGIN])) {
    echo '<h2>FTP Cannot login (' . count($errors[FTP_LOGIN]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>URL</th>
  </tr>';


    foreach ($errors[FTP_LOGIN] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
       </tr>';
    }

    echo '</table>';
}

if (isset($errors[FTP_NO_FILE])) {
    echo '<h2>FTP File not found (' . count($errors[FTP_NO_FILE]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>URL</th>
  </tr>';


    foreach ($errors[FTP_NO_FILE] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
       </tr>';
    }

    echo '</table>';
}


if (isset($errors[HTTP_INTERNAL_ERROR])) {
    echo '<h2>HTTP Internal error (' . count($errors[HTTP_INTERNAL_ERROR]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>URL</th>
  </tr>';

    foreach ($errors[HTTP_INTERNAL_ERROR] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
       </tr>';
    }

    echo '</table>';
}

if (isset($errors[HTTP_NOT_FOUND])) {
    echo '<h2>HTTP 404 Not Found (' . count($errors[HTTP_NOT_FOUND]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>URL</th>
  </tr>';

    foreach ($errors[HTTP_NOT_FOUND] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
       </tr>';
    }

    echo '</table>';
}

if (isset($errors[HTTP_MOVED])) {
    echo '<h2>HTTP Moved files (' . count($errors[HTTP_MOVED]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>Entity URL</th>
    <th>Redirected to</th>
  </tr>';

    foreach ($errors[HTTP_MOVED] as $infos) {
        echo '<tr>
        <td>' . $entity_names[$infos[0]] . '</td>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_urls[$infos[0]] . '</a></td>
        <td><a href="' . $infos[1] . '">' . $infos[1] . '</a></td>
       </tr>';
    }

    echo '</table>';
}

if (isset($errors[HTTP_WRONG_HEADER])) {
    echo '<h2>HTTP Error (' . count($errors[HTTP_WRONG_HEADER]) . ')</h2>' .
    '<table>
  <tr class="blue">
    <th>Entity Name</th>
    <th>Unreconized header</th>
  </tr>';

    foreach ($errors[HTTP_WRONG_HEADER] as $infos) {
        echo '<tr>
        <td><a href="' . $entity_urls[$infos[0]] . '">' . $entity_names[$infos[0]] . '</a></td>
        <td><a href="' . $infos[1] . '">' . $infos[1] . '</a></td>
       </tr>';
    }
    echo '</table>';
}

if (!count($errors)) {
    echo '<p><b>No problems found!</b></p>';
}

echo '<p>Checked ' . $numb . ' urls</p>';
echo '<?php echo site_footer(); ?>';

?>

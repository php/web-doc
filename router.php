<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$requestUri = $_SERVER['SCRIPT_NAME'];
$matches = [];

if (is_file("www" . $requestUri)) {
    if (preg_match('#\.php$#', $requestUri)) {
        include "www" . $requestUri;
        return true;
    } elseif (preg_match('#\.png$#', $requestUri)) {
        header('Content-type: image/png');
        readfile("www" . $requestUri);
        return true;
    }
}
if (is_dir("www" . $requestUri) && is_file("www" . $requestUri . "index.php")) {
    include "www" . $requestUri . "index.php";
    return true;
}

if (preg_match('#^/guide(/.*)$#', $requestUri, $matches)) {
    $_GET['chapter'] = substr($matches[1], 1); # trim leading /
    include 'www/guide.php';
    return true;
}

return false;

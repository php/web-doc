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
 * | Authors: Nuno Lopes <nlopess@php.net>                                |
 * +----------------------------------------------------------------------+
 *
 * $Id$
 */


function verify_password($user, $pass) {

    if($user == 'cvsread') {
        return false;
    }

    if(!$fp = @fsockopen('cvs.php.net', 2401)) {
         return false;
    }

    $crypt = cvs_encrypt_pass($pass);

    fputs ($fp, "BEGIN AUTH REQUEST\n/repository\n".
                "$user\n$crypt\nEND AUTH REQUEST\n");

    fflush($fp);

    if (fgets($fp) == "I LOVE YOU\n") {
        fclose($fp);
        return true;
    }

    fclose($fp);
    return false;

}


function cvs_encrypt_pass($pass) {
    $chars = array('!' => 120, '"' => 53, '%' => 109, '&' => 72, "'" => 108,
                   '(' => 70, ')' => 64, '*' => 76, '+' => 67, ',' => 116,
                   '-' => 74, '.' => 68, '/' => 87, '0' => 111, '1' => 52,
                   '2' => 75, '3' => 119, '4' => 49, '5' => 34, '6' => 82,
                   '7' => 81, '8' => 95, '9' => 65, ':' => 112, ';' => 86,
                   '<' => 118, '=' => 110, '>' => 122, '?' => 105, 'A' => 57,
                   'B' => 83, 'C' => 43, 'D' => 46, 'E' => 102, 'F' => 40,
                   'G' => 89, 'H' => 38, 'I' => 103, 'J' => 45, 'K' => 50, 
                   'L' => 42, 'M' => 123, 'N' => 91, 'O' => 35, 'P' => 125,
                   'Q' => 55, 'R' => 54, 'S' => 66, 'T' => 124, 'U' => 126,
                   'V' => 59, 'W' => 47, 'X' => 92, 'Y' => 71, 'Z' => 115,
                   'a' => 121, 'b' => 117, 'c' => 104, 'd' => 101, 'e' => 100,
                   'f' => 69, 'g' => 73, 'h' => 99, 'i' => 63, 'j' => 94,
                   'k' => 93, 'l' => 39, 'm' => 37, 'n' => 61, 'o' => 48,
                   'p' => 58, 'q' => 113, 'r' => 32, 's' => 90, 't' => 44,
                   'u' => 98, 'v' => 60, 'w' => 51, 'x' => 33, 'y' => 97,
                   'z' => 62, '_' => 56);
	
    $crypt = 'A';
    $strlen = strlen($pass);

    for ($i=0; $i < $strlen; $i++) {
        $ch = substr($pass, $i, 1);
        $crypt .= chr($chars[$ch]);
    }

    return $crypt;
}
?>

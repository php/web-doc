<?php
/*
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
| Authors:          Vincent Gevers <vincent@php.net>                   |
+----------------------------------------------------------------------+
$Id$
*/

/*
 * This file contains the configuration
 * for the RFC system
 */

/**
 * RFC: how long a proposal must be in the "proposal" phase before
 * a "Call for Votes" can be called
 */
define('PROPOSAL_STATUS_PROPOSAL_TIMELINE', (60 * 60 * 24 * 2)); // 1 week

/**
 * RFC: how long the "Call for Votes" lasts
 */
define('PROPOSAL_STATUS_VOTE_TIMELINE', (60 * 60 * 24 * 2)); // 1 week

if (isset($_SERVER['PROPOSAL_MAIL_DOC_DEV'])) {
    /**
     * @ignore
     */
    define('PROPOSAL_MAIL_DOC_DEV', $_SERVER['PROPOSAL_MAIL_DOC_DEV']);
} else {
    /**
     * RFC: the address of the PEAR Developer email list
     *
     * Notices of changes will be sent to this address.
     *
     * To override default, set the value in $_SERVER['PROPOSAL_MAIL_PEAR_DEV']
     * before this file is included.
     */
    define('PROPOSAL_MAIL_DOC_DEV', 'PHP Documentation <doc-web@lists.php.net>');
}

if (isset($_SERVER['PROPOSAL_MAIL_DOC_GROUP'])) {
    /**
     * @ignore
     */
    define('PROPOSAL_MAIL_DOC_GROUP', $_SERVER['PROPOSAL_MAIL_DOC_GROUP']);
} else {
    /**
     * RFC: the address of the PEAR Group email list
     *
     * Notices of some changes get sent to this address.
     *
     * To override default, set the value in $_SERVER['PROPOSAL_MAIL_PEAR_GROUP']
     * before this file is included.
     */
    define('PROPOSAL_MAIL_DOC_GROUP', 'Documentation Group <doc-web@lists.php.net>');
}

if (isset($_SERVER['PROPOSAL_MAIL_FROM'])) {
    /**
     * @ignore
     */
    define('PROPOSAL_MAIL_FROM', $_SERVER['PROPOSAL_MAIL_FROM']);
} else {
    /**
     * RFC: the email address used as the From header
     *
     * To override default, set the value in $_SERVER['PROPOSAL_MAIL_FROM']
     * before this file is included.
     */
    define('PROPOSAL_MAIL_FROM', 'RFC System <doc-web@lists.php.net>');
}

/**
 * RFC: the string prepended to the subject lines of emails
 */
define('PROPOSAL_EMAIL_PREFIX', '[RFC]');

/**
 * RFC: the string put on the end of each email
 */
define('PROPOSAL_EMAIL_POSTFIX', "\n\n-- \nSent by RFC, the automatic proposal system at http://doc.php.net");

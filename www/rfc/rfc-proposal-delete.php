<?php

/**
 * Interface for deleting a proposal.
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @original  PEPr pearweb
 * @category  docweb
 * @package   RFC
 * @author    Vincent Gevers <vincent@php.net>
 * @author    Tobias Schlitt <toby@php.net>
 * @author    Daniel Convissor <danielc@php.net>
 * @copyright Copyright (c) 1997-2004 The PHP Group
 * @license   http://www.php.net/license/3_0.txt  PHP License
 * @version   $Id$
 */

/**
 * Obtain the common functions and classes.
 */
$path = realpath(dirname(__FILE__));
require_once $path . '/../../include/rfc/rfc.php';

auth();

if (!empty($_GET['isDeleted'])) {
    echo site_header('RFC :: Delete');
    echo "<h1>Delete Proposal</h1>\n";
    report_success('Proposal deleted successfully.');
    echo '<p>';
    print_link('rfc-overview.php', 'Back to RFC Home Page');
    echo "</p>\n";
    echo site_footer();
    exit;
}

if (!$proposal =& proposal::get($dbh, @$_GET['id'])) {
    echo site_header('RFC :: Delete :: Invalid Request');
    echo "<h1>Delete Proposal Error</h1>\n";
    report_error('The requested proposal does not exist.');
    echo site_footer();
    exit;
}

ob_start();

echo site_header('RFC :: Delete :: ' . htmlspecialchars($proposal->pkg_name));
echo '<h1>Delete Proposal ' . htmlspecialchars($proposal->pkg_name) . "</h1>\n";

if (!$proposal->mayEdit($docwebUser)) {
    report_error('You are not allowed to delete this proposal,'
                 . ' probably due to it having reached the "'
                 . $proposal->getStatus(true) . '" phase.'
                 . ' If this MUST be deleted, contact someone ELSE'
                 . ' who has admin karma.');
    echo site_footer();
    exit;
}

if ($proposal->compareStatus('>', 'proposal')) {
    if (is_admin()) {
        report_error('This proposal has reached the "'
                     . $proposal->getStatus(true) . '" phase.'
                     . ' Are you SURE you want to delete it?',
                     'warnings', 'WARNING:');
    }
}

$form =& new HTML_QuickForm('delete-proposal', 'post',
                            'rfc-proposal-delete.php?id=' . $proposal->id);

$form->addElement('checkbox', 'delete', 'Really delete proposal for ',
                  htmlspecialchars($proposal->pkg_category) . '::'
                  . htmlspecialchars($proposal->pkg_name));
$form->addElement('textarea', 'reason',
                  'Please tell us why you chose to delete this proposal ');
                                    
$form->addElement('submit', 'submit', 'Do it');

$form->addRule('delete', 'You have to check the box to delete!!', 'required',
               '', 'server');

if (isset($_POST['submit'])) {
    if ($form->validate()) {
        $proposal->delete($dbh);
        $proposal->sendActionEmail('proposal_delete', 'mixed', $docwebUser,                                                     $form->exportValue('reason'));
        ob_end_clean();
        header('Location: rfc-proposal-delete.php?id=' . $proposal->id . '&isDeleted=1');
    } else {
        $pepr_form = $form->toArray();
        report_error($pepr_form['errors']);
    }
}

ob_end_flush();
display_pepr_nav($proposal);

$form->display();

echo site_footer();

?>

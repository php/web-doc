<?php

/**
 * Displays a list of all proposals.
 *
 * The <var>$proposalStatiMap</var> array is defined in
 * pearweb/include/pepr/pepr.php.
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
require_once '../../include/lib_general.inc.php';
require_once '../../include/rfc/rfc.php';

if (isset($_GET['filter']) && isset($proposalStatiMap[$_GET['filter']])) {
    $selectStatus = $_GET['filter'];
} else {
    $selectStatus = '';
}

if ($selectStatus != '') {
    $order = ' pkg_category ASC, pkg_name ASC';
}

$proposals =& proposal::getAll($dbh, @$selectStatus, null, @$order);

echo site_header('RFC :: Proposals');

echo '<h1>Proposals</h1>' . "\n";

display_overview_nav();

$last_status = false;
$first_loop =  true;

$finishedCounter = 0;

foreach ($proposals as $proposal) {
    if ($proposal->getStatus() != $last_status) {
        if ($first_loop != true) {
            echo "</ul>\n";
        }
        echo '<h2 name="' . $proposal->getStatus() . '" id="';
        echo $proposal->getStatus() . '">';
        echo '&raquo; ' . htmlspecialchars($proposal->getStatus(true));
        echo "</h2>\n";
        echo "<ul>\n";
        $last_status = $proposal->getStatus();
        $first_loop = false;
    }
    $prpCat = $proposal->pkg_category;
    if ($selectStatus != '' && (!isset($lastChar) || $lastChar != $prpCat{0})) {
        $lastChar = $prpCat{0};
        echo '</ul>';
        echo "<h3>$lastChar</h3>";
        echo '<ul>';
    }
    if ($proposal->getStatus() == 'finished' && $selectStatus != 'finished') {
        if (++$finishedCounter == 10) {
            break;
        }
    }
    if (!isset($users[$proposal->user_handle])) {
        $users[$proposal->user_handle] = array('Test','name'=>'TestUser');// user::info($proposal->user_handle); // !!!
    }
    
    $already_voted = false;
    if (isset($_COOKIE['PEAR_USER']) && $proposal->getStatus(true) == "Called for Votes") {
        $proposal->getVotes($dbh);

        if (in_array($_COOKIE['PEAR_USER'], array_keys($proposal->votes))) {
            $already_voted = true;
        }
    }

    echo "<li>";
    
    if ($already_voted) {
        echo '(Already voted) ';
    }
    
    print_link('rfc-proposal-show.php?id=' . $proposal->id,
               htmlspecialchars($proposal->pkg_category) . ' :: '
               . htmlspecialchars($proposal->pkg_name)); 
    echo ' by ';
    print_link('/user/' . htmlspecialchars($proposal->user_handle),
               htmlspecialchars($users[$proposal->user_handle]['name']));
            $proposalStatus = $proposal->getStatus();
    switch ($proposalStatus) {
        case 'proposal':
            echo ' &nbsp;(<a href="rfc-comments-show.php?id=' . $proposal->id;
            echo '">Comments</a>)';
            break;
        case 'vote':
        case 'finished':
            $voteSums = ppVote::getSum($dbh, $proposal->id);
            echo ' &nbsp;(<a href="rfc-votes-show.php?id=' . $proposal->id;
            echo '">Vote</a> sum: <strong>' . $voteSums['all'] . '</strong>';
            echo '<small>, ' . $voteSums['conditional'];
            echo ' conditional';
            if ($proposalStatus == 'finished') {
                if ($voteSums['all'] >= 5) {
                    echo ', accepted';
                } else {
                    echo ', rejected';
                }
            }
            echo "</small>)\n";
    }
    echo "</li>\n";
}

echo "</ul>\n";

if ($selectStatus == '') {
    print_link('rfc-overview.php?filter=finished', 'All finished proposals');
}

echo site_footer();

?>

<?php

/**
 * Displays details about a specific vote.
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

$handle = htmlspecialchars(@$_GET['handle']);

if (!$proposal =& proposal::get($dbh, @$_GET['id']) || !$handle)
{
    echo site_header('RFC :: Vote Details :: Invalid Request');
    echo "<h1>Vote Details Error</h1>\n";
    report_error('The requested proposal or user does not exist.');
    echo site_footer();
    exit;
}

echo site_header('RFC :: Vote Details :: '
                . htmlspecialchars($proposal->pkg_name) . ' :: ' . $handle);

echo '<h1>Vote Details for ' . htmlspecialchars($proposal->pkg_name) . ' by ';
echo $handle . "</h1>\n";

display_pepr_nav($proposal);

?>

<table border="0" cellspacing="0" cellpadding="2" style="width: 75%">

 <tr>
  <th class="headrow">&raquo; Details</th>
 </tr>
 <tr>

<?php

$vote = ppVote::get($dbh, $proposal->id, $handle);

if (!$vote) {
    echo '<td class="textcell" valign="top">';
    echo 'This user has not voted on this proposal yet.';
} else {
    echo '<td class="ulcell" valign="top">';
    echo "<ul>\n";

    echo ' <li>Voter: ';
  //  echo user_link($handle); // !!! user_link() will return the full name 
  echo $handle;
    echo "</li>\n";

    echo ' <li>Vote: ';
    if ($vote->value > 0) {
        echo '+';
    }
    echo $vote->value;
    if ($vote->is_conditional) {
        echo ' (conditional)';
    } else {
        echo ' (not conditional)';
    }
    echo "</li>\n";

    echo ' <li>Reviews: ';
    echo htmlspecialchars(implode(', ', $vote->getReviews(true)));
    echo "</li>\n";

    ?>

   </ul>

  </td>
 </tr>

 <tr>
  <th class="headrow">&raquo; Comment</th>
 </tr>
 <tr>
  <td class="textcell" valign="top">

    <?php

    $vote->comment = trim($vote->comment);
    if ($vote->comment != '-') {
        echo nl2br(htmlentities($vote->comment));
    } else {
        echo '&nbsp;';
    }
}

echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";

echo site_footer();

?>

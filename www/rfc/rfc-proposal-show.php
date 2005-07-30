<?php

/**
 * Display details of a particular proposal.
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

if (isset($_GET['get']) AND !empty($_GET['get'])) {
    $get = basename($_GET['get']);
    
    if (file_exists($path . '/../../files/' . $get) && strlen($get) == 32 &&
        strpos($get, "..") === FALSE) {
        
        $hash = sqlite_escape_string($get);
        $sql = 'SELECT pkg_filehash FROM package_proposals WHERE pkg_filehash LIKE "%'.$hash.'%"';
        $res  = sqlite_query($dbh->connection, $sql);
        $files = sqlite_fetch_single($res);

        $file_tmp = explode('|', $files);
        foreach ($file_tmp as $file) {
            if (strpos($file, $hash)) {
                $file = substr($file, 0, -32);
                break;
            }
        }
        // Output the file for download
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type: octet-stream");
        header("Content-Length: " . filesize($path . '/../../files/' . $get));
        header("Content-Disposition: attachment; filename=".stripslashes($file));
        echo file_get_contents($path . '/../../files/' . $get);
        exit;
    } else {
        error_handler('File could not be found');
    }
    exit;
}


if (!$proposal =& proposal::get($dbh, @$_GET['id'])) {

    echo site_header('RFC :: Details :: Invalid Request');
    echo "<h1>Proposal for</h1>\n";
    report_error('The requested proposal does not exist.');
    echo site_footer();
    exit;
}

echo site_header('RFC :: Details :: ' . htmlspecialchars($proposal->pkg_name));
echo '<h1>Proposal for ' . htmlspecialchars($proposal->pkg_name) . "</h1>\n";

display_pepr_nav($proposal);

// Switching markup types
switch ($proposal->markup) {
    case 'wiki':
       require_once 'Text/Wiki.php';
       $wiki =& new Text_Wiki();
       $wiki->disableRule('wikilink');
       $describtion = $wiki->transform($proposal->pkg_describtion);
       break;
    case 'bbcode':
    default:
       require_once 'HTML/BBCodeParser.php';
       $bbparser = new HTML_BBCodeParser(array('filters' => 'Basic,Images,Links,Lists,Extended'));
       $describtion = $bbparser->qparse(nl2br(htmlentities($proposal->pkg_describtion)));
}

?>

<table border="0" cellspacing="0" cellpadding="2" style="width: 75%">

 <tr>
  <th class="headrow" style="width: 50%">&raquo; Metadata</th>
  <th class="headrow" style="width: 50%">&raquo; Status</th>
 </tr>
 <tr>
  <td class="ulcell" valign="top">
   <ul>
    <li>
     Category: <?php echo htmlspecialchars($proposal->pkg_category) ?>
    </li>
    <li>
     Proposer: <?php echo user_name($proposal->user_handle); ?>
    </li>
   </ul>
  </td>

  <td class="ulcell" valign="top">
   <ul>
    <li>
     Status: <?php echo $proposal->getStatus(true) ?>
    </li>

<?php

if ($proposal->status == 'finished') {
    $proposalVotesSum = ppVote::getSum($dbh, $proposal->id);

    echo '    <li>Result: ';
    if ($proposalVotesSum['all'] >= 5) {
        echo 'Accepted';
    } else {
        echo 'Rejected';
    }
    echo "</li>\n";

    echo '    <li>Sum of Votes: ';
    echo $proposalVotesSum['all'];
    echo ' (' . $proposalVotesSum['conditional'] . ' conditional)';
    echo "</li>\n";
} elseif ($proposal->status == 'vote') {
    // Cron job runs at 0 am
    $pepr_end = mktime(0, 0, 0, date('m', $proposal->vote_date),
                        date('d', $proposal->vote_date),
                        date('Y', $proposal->vote_date));

    if (date('H', $proposal->vote_date) > '03') {
        // add a day
        $pepr_end += 86400;
    }
    
    if ($proposal->longened_date) {
        $pepr_end += PROPOSAL_STATUS_VOTE_TIMELINE * 2;
    } else {
        $pepr_end += PROPOSAL_STATUS_VOTE_TIMELINE;
    }
    echo '    <li>Voting Will End: ';
    echo make_utc_date($pepr_end);
    echo "</li>\n";
}

?>
    
   </ul>
  </td>
 </tr>

 <tr>
  <th class="headrow" colspan="2">&raquo; Description</th>
 </tr>
 <tr>
  <td class="textcell" valign="top" colspan="2">
   <p>   
    <?php echo $describtion; ?>
   </p>
  </td>
 </tr>

 <tr>
  <th class="headrow" style="width: 50%">&raquo; Links</th>
  <th class="headrow" style="width: 50%">&raquo;Files</th>
 </tr>
 <tr>

  <td class="ulcell" valign="top">

<?php

$proposal->getLinks($dbh);
if (!empty($proposal->links)) {
    echo '   <ul>';
    foreach ($proposal->links as $link) {
        echo '    <li>';
        print_link(htmlspecialchars($link->url), $link->getType(true));
        echo "</li>\n";
    }
    echo '   </ul>';
}

?>

  </td>
  <td class="ulcell">
   <ul>
   
<?php

if (!empty($proposal->pkg_filehash)) {
    $list = explode("|", htmlspecialchars($proposal->pkg_filehash));
foreach ($list as $hash) {
    if ($hash == '')
        continue;
    
    $file = substr($hash, 0, -32);
    $hash = substr($hash, -32);
    echo '    <li>';
    print_link('rfc-proposal-show.php?get=' . $hash, htmlspecialchars(stripslashes($file)));
    echo "</li>\n";
}
}

?> 

   </ul>
  </td>
 </tr>

 <tr>
  <th class="headrow" style="width: 50%">&raquo; Timeline</th>
  <th class="headrow" style="width: 50%">&raquo; Changelog</th>
 </tr>
 <tr>
  <td class="ulcell" valign="top">
   <ul>
    <li>
     First Draft: <?php echo make_utc_date($proposal->draft_date, 'Y-m-d') ?>
    </li>

<?php

if ($proposal->proposal_date) {
    echo '    <li>';
    echo 'Proposal: ' . make_utc_date($proposal->proposal_date, 'Y-m-d');
    echo "</li>\n";
}

if ($proposal->vote_date) {
    echo '    <li>';
    echo 'Call for Votes: ' . make_utc_date($proposal->vote_date, 'Y-m-d');
    echo "</li>\n";
}

if ($proposal->longened_date) {
    echo '    <li>';
    echo 'Voting Extended: ' . make_utc_date($proposal->longened_date, 'Y-m-d');
    echo "</li>\n";
}

?>

   </ul>
  </td>
  <td class="ulcell" valign="top">

<?php

if ($changelog = @ppComment::getAll($proposal->id, 'package_proposal_changelog')) {
    echo "<ul>\n";
    foreach ($changelog as $comment) {
        if (!isset($userinfos[$comment->user_handle])) {
          $userinfo[$comment->user_handle] = array('name'=> user_name($comment->user_handle));
        }
        echo '<li><p style="margin: 0em 0em 0.3em 0em; font-size: 90%;">';
        echo htmlspecialchars($userinfo[$comment->user_handle]['name']);
        echo '<br />[' . make_utc_date($comment->timestamp) . ']</p>';

        switch ($proposal->markup) {
            case 'wiki':
                echo $wiki->transform($comment->comment);
                break;
            case 'bbcode':
            default:
                echo $bbparser->qparse(nl2br($comment->comment));
                break;
        }
        echo "</li>\n";
    }
    echo "</ul>\n";
}

echo "  </td>\n </tr>\n";
echo "</table>\n";

echo site_footer();

?>

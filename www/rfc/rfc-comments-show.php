<?php

/**
 * Displays and accepts comments for a given proposal.
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

if (!$proposal =& proposal::get($dbh, @$_GET['id'])) {
    echo site_header('RFC :: Comments :: Invalid Request');
    echo "<h1>Comments Error</h1>\n";
    report_error('The requested proposal does not exist.');
    echo site_footer();
    exit;
}

echo site_header('RFC :: Comments :: ' . htmlspecialchars($proposal->pkg_name));
echo '<h1>Comments for ' . htmlspecialchars($proposal->pkg_name) . "</h1>\n";

if (isset($_COOKIE['PEAR_USER']) &&
    $proposal->getStatus() == 'proposal')
{
    $form =& new HTML_QuickForm('comment', 'post',
                                'rfc-comments-show.php?id=' . $proposal->id);

    $form->addElement('textarea', 'comment', null,
                      array('cols' => 50,
                            'rows' => 8,
                            'id'   => 'comment_field'));

    $form->addElement('static', '', '',
            '<small>Your comment will also be sent to the'
            . ' <strong>phpdoc</strong> mailing list.<br />'
            . ' <strong>Please do not respond to other developers'
            . ' comments</strong>.<br />'
            . ' The author himself is responsible to reflect comments'
            . ' in an acceptable way.</small>');

    $form->addElement('submit', 'submit', 'Add New Comment');

    $form->applyFilter('comment', 'trim');
    $form->addRule('comment', 'A comment is required', 'required', null,
                   'server');

    if (isset($_POST['submit'])) {
        if ($form->validate()) {
            $values = $form->exportValues();
            $proposal->sendActionEmail('proposal_comment', 'user',
                                       $_COOKIE['PEAR_USER'],
                                       $values['comment']);
            $proposal->addComment($values['comment'],
                                  'package_proposal_comments');
            report_success('Your comment was successfully processed');
        } else {
            report_error($form->getElementError('comment'));
        }
    }
}

display_pepr_nav($proposal);

?>

<table border="0" cellspacing="0" cellpadding="2" style="width: 75%">

 <tr>
  <th class="headrow" colspan="2">&raquo; Submit Your Comment</th>
 </tr>
 <tr>
  <td class="textcell" valign="top" colspan="2">

<?php

if ($proposal->getStatus() == 'proposal') {
    if (isset($_COOKIE['PEAR_USER'])) {
        $formArray = $form->toArray();

        echo $form->getValidationScript();

        echo '<form ' . $formArray['attributes'] . ">\n";
        echo '<table class="form-holder" cellspacing="1">' . "\n";

        echo ' <caption class="form-caption">Comment on This';
        echo ' Proposal</caption>' . "\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left">';
        echo '   <label for="comment_field" accesskey="o">C<span';
        echo ' class="accesskey">o</span>mment:</label>';
        echo "  </th>\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][0]['html'] . "</td>\n";
        echo " </tr>\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left">&nbsp;</th>' . "\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][1]['html'] . "</td>\n";
        echo " </tr>\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left">&nbsp;</th>' . "\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][2]['html'] . "</td>\n";
        echo " </tr>\n";

        echo "</table>\n";
        echo "</form>\n";
    } else {
        echo 'Please log in to enter your comment. If you are not a registered';
        echo ' PHP Docer, you can comment by sending an email to '; // !!! 
        echo make_mailto_link('phpdoc@lists.php.net') . '.';
    }
} else {
    echo 'Comments are only accepted during the &quot;Proposal&quot; phase. ';
    echo 'This proposal is currently in the &quot;';
    echo $proposal->getStatus(true) . '&quot; phase.';
}

?>

  </td>
 </tr>

 <tr>
  <th class="headrow" style="width: 100%">&raquo; Comments</th>
 </tr>
 <tr>

<?php

$comments = ppComment::getAll($proposal->id, 'package_proposal_comments');
$userInfos = array();

if (is_array($comments) && (count($comments) > 0)) {
    echo '  <td class="ulcell" valign="top">' . "\n";
    echo '   <ul class="spaced">' . "\n";
    foreach ($comments as $comment) {
        if (empty($userInfos[$comment->user_handle])) {  // not used?
           // $userInfos[$comment->user_handle] = user::info($comment->user_handle); // !!!
	  // $userinfos[$comment->user_handle] = 'TestUser';
        }
        echo '<li><p style="margin: 0em 0em 0.3em 0em;">';
        echo make_link('/user/'.$comment->user_handle, $comment->user_handle);
        echo ' [' . make_utc_date($comment->timestamp) . ']</p>';
        echo nl2br(htmlentities(trim($comment->comment))) . "\n</li>";
    }
    echo "   </ul>\n";
} else {
    echo '  <td class="textcell" valign="top">';
    echo 'There are no comments.';
}

?>

  </td>
 </tr>
</table>

<?php

echo site_footer();

?>

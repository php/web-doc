<?php

/**
 * Displays and accepts votes for a given proposal.
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
    echo site_header('RFC :: Votes :: Invalid Request');
    echo "<h1>Proposal Votes Error</h1>\n";
    report_error('The requested proposal does not exist.');
    echo site_footer();
    exit;
}

echo site_header('RFC :: Votes :: ' . htmlspecialchars($proposal->pkg_name));
echo '<h1>Proposal Votes for ' . htmlspecialchars($proposal->pkg_name) . "</h1>\n";

if (isset($docwebUser) && $proposal->mayVote($dbh, $docwebUser)) {
    $form =& new HTML_QuickForm('vote', 'post',
                                'rfc-votes-show.php?id=' . $proposal->id);

    $form->setDefaults(array('value' => 1));
    $form->addElement('select', 'value', '',
                      array(1 => '+1',
                            0 => '0',
                            -1 => '-1'),
                      'id="vote_field"');
    $form->addElement('checkbox', 'conditional', '', '', null, 1);
    $form->addElement('textarea', 'comment', null,
                      array('cols' => 40,
                            'rows' => 3));
    $form->addElement('select', 'reviews', '', $proposalReviewsMap,
                      array('size' => count($proposalReviewsMap),
                            'multiple' => 'multiple'));
    $form->addElement('static', '', '',
                      '<small>Note that you can only vote once!<br />'
                      . 'For conditional votes, please leave a comment and'
                      . ' vote +1 (<i>e.g.</i>, &quot;I\'m +1 if you'
                      . ' change...&quot;).</small>');
    $form->addElement('submit', 'submit', 'Vote');

    $form->applyFilter('comment', 'trim');
    $form->addRule('value', 'Vote is a required field.', 'required',
                   null, 'server');
    $form->addRule('value', 'Vote must be +1, 0 or -1.', 'regex',
                   '/-1|0|1/', 'server');
    $form->addRule('reviews', 'Reviews is a required field.', 'required',
                    null, 'server');

    if (isset($_POST['submit'])) {
        if ($form->validate()) {
            $value = $form->getElement('value');
            $value = $value->getSelected();
            $voteData['value'] = (int)$value['0'];
            $is_conditional = $form->getElement('conditional');
            $voteData['is_conditional'] = ($is_conditional->getChecked()) ? 1 : 0;
            $comment = $form->getElement('comment');
            $voteData['comment'] = $comment->getValue();
            $reviews = $form->getElement('reviews');
            $voteData['reviews'] = $reviews->getSelected();
            $voteData['user_handle'] = $docwebUser;

            $errors = array();

            if ($voteData['is_conditional'] && empty($voteData['comment'])) {
                $errors[] = 'You have to apply a comment if your vote is'
                          . ' conditional!';
            }
            if ($voteData['is_conditional'] && ($voteData['value'] < 1)) {
                $errors[] = 'Conditional votes have to be formulated positively!'
                          . " Please select '+1' and change your text to a"
                          . " form like 'I am +1 on this if you change...'.";
            }
            foreach ($voteData['reviews'] as $value) {
                if (!array_key_exists($value, $proposalReviewsMap)) {
                    $errors[] = 'Reviews contains invalid data';
                }
            }

            if ($errors) {
                report_error($errors);
            } else {
                $proposal->addVote($dbh, new ppVote($voteData));
                $proposal->sendActionEmail('proposal_vote', 'user', $docwebUser);
                report_success('Your vote has been registered successfully');
                $form->freeze();
            }
        } else {
            $pepr_form = $form->toArray();
            report_error($pepr_form['errors']);
        }
    }
} else {
    $form = false;
}

display_pepr_nav($proposal);

?>

<table border="0" cellspacing="0" cellpadding="2" style="width: 75%">

<?php

if ($proposal->status == 'vote') {
    echo " <tr>\n";
    echo '  <th class="headrow">&raquo; Cast Your Vote</th>' . "\n";
    echo " </tr>\n";
    echo " <tr>\n";

    if ($form) {
        echo '  <td class="textcell" valign="top">' . "\n";
        
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
        echo '    <p>Voting Will End approximately ';
        echo make_utc_date($pepr_end);
        echo "</p>\n";

        $formArray = $form->toArray();

        echo $form->getValidationScript();

        echo '<form ' . $formArray['attributes'] . ">\n";
        echo '<table class="form-holder" cellspacing="1">' . "\n";

        echo ' <caption class="form-caption">Vote on This';
        echo ' Proposal</caption>' . "\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left">';
        echo '   <label for="vote_field" accesskey="o">V<span';
        echo ' class="accesskey">o</span>te:</label>';
        echo "  </th>\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][0]['html'] . ' ' . $formArray['elements'][0]['label'] . "</td>\n";
        echo " </tr>\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left">Conditional:</th>' . "\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][1]['html'] . "</td>\n";
        echo " </tr>\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left">Comment:</th>' . "\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][2]['html'] . "</td>\n";
        echo " </tr>\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left">Reviews:</th>' . "\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][3]['html'].$formArray['elements'][3]['label'] . "</td>\n";
        echo " </tr>\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left"></th>' . "\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][4]['html'] . "</td>\n";
        echo " </tr>\n";

        echo " <tr>\n";
        echo '  <th class="form-label_left"></th>' . "\n";
        echo '  <td class="form-input">';
        echo $formArray['elements'][5]['html'] . "</td>\n";
        echo " </tr>\n";

        echo "</table>\n";
        echo "</form>\n";
    } else {
        ?>

  <td class="ulcell" valign="top">
   <ul>
    <li>You must be a full-featured PEAR developer.</li>
    <li>You must be <a href="/login.php?return=<?php echo $_SERVER['REQUEST_URI']; ?>">logged in</a>.</li>
    <li>Only one vote can be cast.</li>
    <li>Proposers can not vote on their own package.</li>
   </ul>

        <?php
    }

    echo "  </td>\n";
    echo " </tr>\n";

}

echo " <tr>\n";
echo '  <th class="headrow">&raquo; Votes</th>' . "\n";
echo " </tr>\n";
echo " <tr>\n";

switch ($proposal->status) {
    case 'draft':
    case 'proposal':
        echo '  <td class="textcell" valign="top">';
        echo 'Voting has not started yet.';
        break;

    default:
        $proposal->getVotes($dbh);
        if (count($proposal->votes) == 0) {
            echo '  <td class="textcell" valign="top">';
            echo 'No votes have been cast yet.';
        } else {
            $users = array();
            $head  = true;

            echo '  <td class="ulcell" valign="top">' . "\n<ul>\n";

            foreach ($proposal->votes as $vote) {
                if (!isset($users[$vote->user_handle])) {
		   $users[$vote->user_handle] = array('name'=>user_name($vote->user_handle));
                }
                if ($vote->value > 0) {
                    $vote->value = '+' . $vote->value;
                }

                echo ' <li><strong>';
                print_link('rfc-vote-show.php?id=' . $proposal->id
                           . '&amp;handle='
                           . htmlspecialchars($vote->user_handle),
                           $vote->value);
                echo '</strong>';

                if ($vote->is_conditional) {
                    echo '^';
                } elseif ($vote->comment != '-') {
                    echo '*';
                }
                echo ' &nbsp;(';
                print_link('/user/' . htmlspecialchars($vote->user_handle),
                           htmlspecialchars($users[$vote->user_handle]['name']));
                echo ')&nbsp; ' . make_utc_date($vote->timestamp);
                echo "</li>\n";
            }

            $proposalVotesSum = ppVote::getSum($dbh, $proposal->id);

            echo "</ul>\n" . '<p style="padding-left: 1.2em;"><strong>';
            echo 'Sum: ' . $proposalVotesSum['all'] . '</strong> <small>(';
            echo $proposalVotesSum['conditional'];
            echo ' conditional)</small></p>' . "\n";
            echo '<p style="padding-left: 1.2em;"><small>^ Indicates';
            echo ' the vote is conditional.' . "\n";
            echo '<br />* Indicates';
            echo ' the vote contains a comment.</small></p>' . "\n";
        }
}

echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";

echo site_footer();

?>

<?php

/**
 * Establishes the procedures, objects and variables used throughout PEPr.
 *
 * The <var>$proposalStatiMap</var> and <var>$proposalTypeMap</var> arrays
 * are also defined here.
 *
 * NOTE: Proposal constants are defined in rfc-config.php.
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
 
require_once 'DB.php';
require_once 'DB/storage.php'; 
require_once dirname(__FILE__) . '/rfc-config.php';

define('PEAR_DATABASE_DSN', 'sqlite://./../../sqlite/rfc.sqlite');
define('DEVBOX', false);

if (empty($dbh)) {
    $options = array(
        'persistent' => false,
        'portability' => DB_PORTABILITY_ALL
    );
    $dbh =& DB::connect(PEAR_DATABASE_DSN, $options);
} 
    
    
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'error_handler');


// HACK(S)
// these can be added in lib_general.inc.php
// but they are only used here. replace the function with html
// can be always be done

function make_utc_date($ts = null, $format = 'Y-m-d H:i \U\T\C')
{
     if (!$ts) {
         $ts = time();
     }

     return gmdate($format, $ts);
}


/**
 * Returns a hyperlink to something 
 */
function make_link($url, $linktext = '', $target = '', $extras = '')
{     

    return sprintf('<a href="%s"%s%s>%s</a>',
             $url,
             ($target ? ' target="'.$target.'"' : ''),
             ($extras ? ' '.$extras : ''),
             ($linktext != '' ? $linktext : $url)
    );
}
    
# print_link

/** 
* Echos a hyperlink to something
*/

function print_link($url, $linktext = '', $target = '', $extras = '')
{
echo make_link($url, $linktext, $target, $extras);
}

function make_mailto_link($email, $linktext = '', $extras = '')
{
    $tmp = '';
    for ($i = 0, $l = strlen($email); $i<$l; $i++) {
        if ($i % 2) {
            $tmp .= '&#' . ord($email[$i]) . ';';
        } else {
            $tmp .= '&#x' . dechex(ord($email[$i])) . ';';
        }
    }

    return '<a ' . $extras . ' href="&#x6d;&#97;&#x69;&#108;&#x74;&#111;&#x3a;'
           . $tmp . '">' . ($linktext != '' ? $linktext : $tmp) . '</a>';
}




/**
 * Prints a tabbed navigation bar based on the parameter $items
 */
function print_tabbed_navigation($items)
{
   $page = basename($_SERVER['PHP_SELF']);
   $master_url = get_resource_url();

    echo '<div id="nav">' . "\n";
    foreach ($items as $title => $item) {
        echo '<a href="' . $master_url . 'rfc/' . $item['url'] // !!!
             . '" title="' . $item['title'] . '"';
        if (strpos($item['url'], $page) !== false) {
            echo ' class="active"';
        }
        echo ' >' . $title . "</a>\n";
    }
    echo '</div>';    
}


function report_success($in)
{
    echo '<div class="success">';
    echo htmlspecialchars($in);
    echo "</div>\n";
}

/**
 * Display errors or warnings as a <ul> inside a <div>
 *
 * Here's what happens depending on $in:
 *   + string: value is printed
 *   + array:  looped through and each value is printed.
 *             If array is empty, nothing is displayed.
 *             If a value contains a PEAR_Error object,
 *   + PEAR_Error: prints the value of getMessage() and getUserInfo()
 *                 if DEVBOX is true, otherwise prints data from getMessage().
 *
 * @param string|array|PEAR_Error $in  see long description
 * @param string $class  name of the HTML class for the <div> tag.
 *                        ("errors", "warnings")
 * @param string $head   string to be put above the message
 *
 * @return bool  true if errors were submitted, false if not
 */

function report_error($in, $class = 'errors', $head = 'ERROR:')
{
    if (PEAR::isError($in)) {
        if (DEVBOX == true) {
            $in = array($in->getMessage() . '... ' . $in->getUserInfo());
        } else {
            $in = array($in->getMessage());
        }
    } elseif (!is_array($in)) {
        $in = array($in);
    } elseif (!count($in)) {
        return false;
    }

    echo '<div class="' . $class . '">' . $head . '<ul>';
    foreach ($in as $msg) {
        if (PEAR::isError($msg)) {
            if (DEVBOX == true) {
                $msg = $msg->getMessage() . '... ' . $msg->getUserInfo();
            } else {
                $msg = $msg->getMessage();
            }
        }
        echo '<li>' . htmlspecialchars($msg) . "</li>\n";
    }
    echo "</ul></div>\n";
    return true;
}

/**
 * Forwards warnings to report_error()
 *
 * For use with PEAR_ERROR_CALLBACK to get messages to be formatted
 * as warnings rather than errors.
 *
 * @param string|array|PEAR_Error $in  see report_error() for more info
 *
 * @return bool  true if errors were submitted, false if not
 *
 * @see report_error()
 */
function report_warning($in)
{
    return report_error($in, 'warnings', 'WARNING:');
}

/**
 * Generates a complete PEAR web page with an error message in it then
 * calls exit
 *
 * For use with PEAR_ERROR_CALLBACK error handling mode to print fatal
 * errors and die.
 *
 * @param string|array|PEAR_Error $in  see report_error() for more info
 * @param string $title  string to be put above the message
 *
 * @return void
 *
 * @see report_error()
 */
function error_handler($errobj, $title = 'Error')
{
   // echo site_header($title);
    report_error($errobj);
    echo site_footer();
    exit;
}




/**
 * Load necessary classes.
 */

require_once 'HTML/QuickForm.php';
require_once 'Mail.php';

// HACK // !!!
$_COOKIE['PEAR_USER'] = 'TestUser';


/**
 * Prints the navigation tabs
 *
 * @param object $proposal  the current proposal object
 *
 * @return void
 */
function display_pepr_nav(&$proposal)
{
    if ($proposal == null) {
        $id = 0;
    } else {
        $id = $proposal->id;
    }

    $items = array(
        'Main'       => array('url'   => 'rfc-proposal-show.php?id=' . $id,
                              'title' => 'View proposal details'
                        ),
        'Comments'   => array('url'   => 'rfc-comments-show.php?id=' . $id,
                              'title' => 'View and/or enter comments'
                        ),
        'Votes'      => array('url'   => 'rfc-votes-show.php?id=' . $id,
                              'title' => 'View and/or enter votes'
                        ),
    );

    if ($proposal != null &&
        isset($_COOKIE['PEAR_USER']) &&
        $proposal->mayEdit($_COOKIE['PEAR_USER'])) // !!!
    {
        $items['Edit'] = array(
            'url'   => 'rfc-proposal-edit.php?id=' . $id,
            'title' => 'Edit this proposal'
        );
        $items['Delete'] = array(
            'url'   => 'rfc-proposal-delete.php?id=' . $id,
            'title' => 'Delete this proposal'
        );
    }

    print_tabbed_navigation($items);
}


function shorten_string($string)
{
    if (strlen($string) < 80) {
        return $string;
    }
    $string_new = substr($string, 0, 20);
    $string_new .= "..." . substr($string, (strlen($string) - 60));
    return $string_new;
}


global $proposalStatiMap;
$proposalStatiMap = array(
                          'draft'    => 'Draft',
                          'proposal' => 'Proposed',
                          'vote'     => 'Called for Votes',
                          'finished' => 'Finished'
                          );


class proposal {

    var $id;

    var $pkg_category;

    var $pkg_name;

    var $pkg_license;

    var $pkg_describtion;

    var $pkg_deps;

    var $draft_date;

    var $proposal_date;

    var $vote_date;

    var $longened_date;

    var $status = 'draft';

    var $user_handle;

    var $links;

    var $votes;

    var $markup;

    function proposal($dbhResArr)
    {
        $this->fromArray($dbhResArr);
    }

    function fromArray($dbhResArr)
    {
        if (!is_array($dbhResArr)) {
            return false;
        }
        foreach ($dbhResArr as $name => $value) {
            $this->$name = $value;
        }
        return true;
    }

    /**
     * Look up proposal information based on the proposal ID number
     *
     * @param object $dbh  the current DB object
     * @param int    $id   the ID number of the proposal being looked for
     *
     * @return object  a new proposal object.  false if the $id provided is
     *                 not numeric.  null if the $id doesn't refer to
     *                 an actual proposal.
     *
     * @access public
     */
    function &get(&$dbh, $id)
    {
        if (!is_numeric($id)) {
            $res = false;
            return $res;
        }
        $id  = (int)$id;
        $sql = "SELECT * FROM package_proposals WHERE id = ".$id;
        $res =& $dbh->getRow($sql, null, DB_FETCHMODE_ASSOC);
        
        if (DB::isError($res)) {
            return false;
        }
        if (!$res) {
            return $res;
        }
        return new proposal($res);
    }

    function &getAll(&$dbh, $status = null, $limit = null)
    {
        $sql = "SELECT * FROM package_proposals";
        if (!empty($status)) {
            $sql .= " WHERE status = '".$status."'";
        }
        $sql .= " ORDER BY status ASC, draft_date DESC";
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $result = array();
        while ($set = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $result[$set['id']] =& new proposal($set);
        }
        return $result;
    }

    function getLinks(&$dbh)
    {
        if (empty($this->id)) {
            return PEAR::raiseError("Not initialized");
        }
        $this->links = & ppLink::getAll($dbh, $this->id);
        return true;
    }

    function getVotes(&$dbh)
    {
        if (empty($this->id)) {
            return PEAR::raiseError("Not initialized");
        }
        $this->votes = & ppVote::getAll($dbh, $this->id);
        return true;
    }

    function store($dbh)
    {
        if (isset($this->id)) {
            $sql = "UPDATE package_proposals SET
                    pkg_category = ".$dbh->quoteSmart($this->pkg_category).",
                    pkg_name = ".$dbh->quoteSmart($this->pkg_name).",
                    pkg_describtion = ".$dbh->quoteSmart($this->pkg_describtion).",
                    pkg_filehash = ".$dbh->quoteSmart($this->pkg_filehash).",
                    draft_date = ".$dbh->quoteSmart($this->draft_date).",
                    proposal_date = ".$dbh->quoteSmart($this->proposal_date).",
                    vote_date = ".$dbh->quotesmart($this->vote_date).",
                    longened_date = ".$dbh->quoteSmart($this->longened_date).",
                    status = ".$dbh->quoteSmart($this->status).",
                    user_handle = ".$dbh->quoteSmart($this->user_handle).",
                    markup = ".$dbh->quoteSmart($this->markup)."
                    WHERE id = ".$this->id;
            $dbh->pushErrorHandling(PEAR_ERROR_RETURN);
            $res =& $dbh->query($sql);
            $dbh->popErrorHandling();
            if (DB::isError($res)) {
                if ($res->getCode() == DB_ERROR_CONSTRAINT) {
                    return PEAR::raiseError('A proposal with that Catetory -'
                            . ' Name combination already exists.',
                            $res->getCode(), null, null, $res->getUserInfo());
                } else {
                    return PEAR::raiseError($res->getMessage(),
                                            $res->getCode(), null, null,
                                            $res->getUserInfo());
                }
            }
        } else {
            $sql = "INSERT INTO package_proposals ( pkg_category, pkg_name, pkg_describtion,
                         pkg_filehash, draft_date, status, user_handle, markup) VALUES (
                        ".$dbh->quoteSmart($this->pkg_category).",
                        ".$dbh->quoteSmart($this->pkg_name).",
                        ".$dbh->quoteSmart($this->pkg_describtion).",
                        ".$dbh->quoteSmart($this->pkg_filehash).",
                        ".time().",
                        ".$dbh->quoteSmart($this->status).",
                        ".$dbh->quoteSmart($this->user_handle).",
                        ".$dbh->quoteSmart($this->markup).")";
            $dbh->pushErrorHandling(PEAR_ERROR_RETURN);
            $res =& $dbh->query($sql);
            $dbh->popErrorHandling();
            if (DB::isError($res)) {
                if ($res->getCode() == DB_ERROR_CONSTRAINT) {
                    return PEAR::raiseError('A proposal with that Catetory -'
                            . ' Name combination already exists.',
                            $res->getCode(), null, null, $res->getUserInfo());
                } else {
                    return PEAR::raiseError($res->getMessage(),
                                            $res->getCode(), null, null,
                                            $res->getUserInfo());
                }
            }
            $this->id = sqlite_last_insert_rowid($dbh->connection);
        }
	
	if(isset($this->links) && $this->links !== null) {
        ppLink::deleteAll($dbh, $this->id);
        foreach ($this->links as $link) {
            if (!empty($link->url)) {
                $res = $link->store($dbh, $this->id);
                if (DB::isError($res)) {
                    return $res;
                }
            }
        }
	}
        if (!empty($this->comment)) {
            $this->comment->store($dbh, $this->id);
            unset($this->comment);
        }
        return true;
    }

    function addVote($dbh, $vote)
    {
        if (!empty($this->votes[$vote->user_handle])) {
            return PEAR::raiseError("You already voted!");
        }
        $vote->pkg_propop_id = $this->id;
        $this->votes[$vote->user_handle] =& $vote;
        $vote->store($dbh, $this->id);
        return true;
    }

    function addComment($comment, $table = 'package_proposal_changelog')
    {
        $commentData = array("pkg_prop_id" => $this->id,
                             "user_handle" => $_COOKIE['PEAR_USER'],
                             "comment"     => $comment);
        $comment = new ppComment( $commentData, $table );
        $comment->store($this->id);
        return true;
    }

    function addLink($dbh, $link)
    {
        $link->pkg_prop_id = $this->id;
        $this->links[] =& $link;
        return true;
    }

    function isOwner($handle)
    {
        if (strtolower($this->user_handle) != strtolower($handle)) {
            return false;
        }
        return true;
    }

    function mayEdit($handle = '')
    { // !!!
        global $dbh, $karma;

return true; // !!!
        if (empty($karma)) {
            $karma =& new Damblan_Karma($dbh); // !!!
        }

        switch ($this->status) {
            case 'draft':
            case 'proposal':
                if ($this->isOwner($handle) || $karma->has($handle, 'pear.pepr.admin')) { // !!!
                    return true;
                }
              break;
            default:
                if (!$this->isOwner($handle) && $karma->has($handle, 'pear.pepr.admin')) { // !!!
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Determine if the current user can vote on the current proposal
     *
     * Rules:
     *   + Proposal must be in the "Called for Votes" phase.
     *   + User must be logged in.
     *   + User must be a full-featured PEAR developer.
     *   + Only one vote can be cast.
     *   + Proposers can't vote on their own package, though can for RFC's.
     *
     * @param object $dbh         the current DB object
     * @param string $userHandle  the user's handle
     *
     * @return bool
     *
     * @access public
     */
    function mayVote(&$dbh, $userHandle)
    {  // !!! needs karma fixing
        global $karma;
return true; // !!!
        if (empty($karma)) {
            $karma =& new Damblan_Karma($dbh);
        }

        if ($this->getStatus() == 'vote' &&
            $karma->has($userHandle, 'pear.dev') &&
            !ppVote::hasVoted($dbh, $userHandle, $this->id) &&
            (!$this->isOwner($userHandle) ||
             ($this->isOwner($userHandle) &&
              $this->pkg_category == 'RFC')))
        {
            return true;
        } else {
            return false;
        }
    }

    function getStatus($humanReadable = false)
    {
        if ($humanReadable) {
            return $GLOBALS['proposalStatiMap'][$this->status];
        }
        return $this->status;
    }

    /**
     * Answers the question "Is this proposal $operator than $status?"
     *
     * @param string $operator  the operator (<, <=, ==, >=, >, !=)
     * @param string $status    the status ('draft', 'vote', 'finished', etc)
     *
     * @return bool
     */
    function compareStatus($operator, $status)
    {
        $num = array(
            'draft'    => 1,
            'proposal' => 2,
            'vote'     => 3,
            'finished' => 4,
        );
        switch ($operator) {
            case '<':
                return ($num[$this->status] < $num[$status]);
            case '<=':
                return ($num[$this->status] <= $num[$status]);
            case '==':
                return ($num[$this->status] == $num[$status]);
            case '>=':
                return ($num[$this->status] >= $num[$status]);
            case '>':
                return ($num[$this->status] > $num[$status]);
            case '!=':
                return ($num[$this->status] != $num[$status]);
            default:
                PEAR::raiseError('Invalid $operator passed to compareStatus()');
        }
    }

    function isEditable()
    {
        switch ($this->status) {
        case 'draft':
        case 'proposal': return true;
        }
        return false;
    }

    function checkTimeline()
    {
        switch ($this->status) {
        case 'draft':
            return true;

        case 'proposal':
            if (($this->proposal_date + PROPOSAL_STATUS_PROPOSAL_TIMELINE) <
                time())
            {
                return true;
            }
            return (int)($this->proposal_date + PROPOSAL_STATUS_PROPOSAL_TIMELINE);

        case 'vote':
            if (!empty($this->longened_date)) {
                if (($this->longened_date + PROPOSAL_STATUS_VOTE_TIMELINE) >
                    time())
                {
                    return (int)($this->longened_date + PROPOSAL_STATUS_VOTE_TIMELINE);
                }
            } else {
                if (($this->vote_date + PROPOSAL_STATUS_VOTE_TIMELINE) >
                    time())
                {
                    return (int)($this->vote_date + PROPOSAL_STATUS_VOTE_TIMELINE);
                }
            }
            return false;
        }
    }

    function delete(&$dbh) 
    { // !!!
        if (empty($this->id)) {
            return PEAR::raiseError("Proposal does not exist!");
        } 
      return true; // !!! for testing
        $sql = "DELETE FROM package_proposals WHERE id = ".$this->id;
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $sql = "DELETE FROM package_proposal_votes WHERE pkg_prop_id = ".$this->id;
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $sql = "DELETE FROM package_proposal_links WHERE pkg_prop_id = ".$this->id;
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $sql = "DELETE FROM package_proposal_changelog WHERE pkg_prop_id = ".$this->id;
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $sql = "DELETE FROM package_proposal_comments WHERE pkg_prop_id = ".$this->id;
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        return true;
    }

    function sendActionEmail($event, $userType, $user_handle = null, $comment = '')
    { // !!!
        global $dbh, $karma;

        if (empty($karma)) {
         //   $karma =& new Damblan_Karma($dbh); // !!!
        }
	
	$master_url = get_resource_url();
	require dirname(__FILE__) . '/rfc-emails.php';
        $email = $proposalEmailTexts[$event];
        if (empty($email)) {
            return PEAR::raiseError("Email template for $event not found");
        }
        switch ($userType) {
        case 'admin':
            $prefix = "[ADMIN]";
            break;
        case 'mixed':
            if (/*$karma->has($user_handle, "pear.pepr.admin") && */($this->user_handle != $user_handle)) { // !!!
                $prefix = "[ADMIN]";
            } else {
                $prefix = "";
            }
            break;
        default:
            $prefix = "";
        }
        $prefix = PROPOSAL_EMAIL_PREFIX . $prefix . " ";
        $actorinfo = 'TestUser';//user::info($user_handle); // !!!
        $ownerinfo = 'TestOwner';//user::info($this->user_handle); // !!!
        $this->getVotes($dbh);
        $vote = @$this->votes[$user_handle];

        if (isset($vote)) {
            $vote->value = ($vote->value > 0) ? "+".$vote->value : $vote->value;
            if ($vote->is_conditional) {
                $vote_conditional = "\n\nThis vote is conditional. The condition is:\n\n".$vote->comment;
            } elseif ($vote->comment) {
                $comment = "\n\nComment:\n\n" . $vote->comment;
            }
// !!!
            $vote_url = $master_url . "rfc/rfc-vote-show.php?id=".$this->id."&handle=".$user_handle;
        }

        if ($event == 'change_status_finished') {
            $proposalVotesSum = ppVote::getSum($dbh, $this->id);
            
            $vote_result  = 'Sum of Votes: ' . $proposalVotesSum['all'];
            $vote_result .= ' (' . $proposalVotesSum['conditional']
                          . ' conditional)';

            if ($proposalVotesSum['all'] >= 5) {
                $vote_result .= "\nResult:       This proposal was accepted";
            } else {
                $vote_result .= "\nResult:       This proposal was rejected";
            }
        }
// !!!
        $proposal_url = $master_url . "rfc/rfc-proposal-show.php?id=".$this->id;
        $end_voting_time = (@$this->longened_date > 0) ? $this->longened_date + PROPOSAL_STATUS_VOTE_TIMELINE : @$this->vote_date + PROPOSAL_STATUS_VOTE_TIMELINE;

        if (!isset($user_handle)) {
            $email['to'] = $email['to']['pearweb'];
      //  } else if ($karma->has($user_handle, "pear.pepr.admin")) { // !!!
      //      $email['to'] = $email['to']['admin'];
        } else {
            $email['to'] = $email['to']['user'];
        }

        $email['subject'] = $prefix . $email['subject'];
        $replace = array(
                         "/\{pkg_category\}/",
                         "/\{pkg_name\}/",
                         "/\{owner_name\}/",
                         "/\{owner_email\}/",
                         "/\{owner_link\}/",
                         "/\{actor_name\}/",
                         "/\{actor_email\}/",
                         "/\{actor_link\}/",
                         "/\{proposal_url\}/",
                         "/\{end_voting_time\}/",
                         "/\{vote_value\}/",
                         "/\{vote_url\}/",
                         "/\{email_pear_dev\}/",
                         "/\{email_pear_group\}/",
                         "/\{comment\}/",
                         "/\{vote_result\}/",
                         "/\{vote_conditional\}/"
                         );
        $replacements = array(
                              $this->pkg_category,
                              $this->pkg_name,
                              (isset($ownerinfo['name'])) ? $ownerinfo['name'] : "",
                              (isset($ownerinfo['email'])) ? "<{$ownerinfo['email']}>" : '',
                              (isset($ownerinfo['handle'])) ? make_link($ownerinfo['handle']) : "",
                              (isset($actorinfo['name'])) ? $actorinfo['name'] : "",
                              (isset($actorinfo['email'])) ? $actorinfo['email'] : "",
                              (isset($actorinfo['handle'])) ? $master_url . "user/".$actorinfo['handle'] : "",
                              $proposal_url,
                              make_utc_date($end_voting_time),
                              (isset($vote)) ? $vote->value : 0,
                              (isset($vote)) ? $vote_url : "",
                              PROPOSAL_MAIL_DOC_DEV,
                              PROPOSAL_MAIL_DOC_GROUP,
                              (isset($comment)) ? $comment : '',
                              (isset($vote_result)) ? $vote_result : '',
                              (isset($vote_conditional)) ? $vote_conditional : ""
                              );

        $email = preg_replace($replace, $replacements, $email);
        $email['text'] .= PROPOSAL_EMAIL_POSTFIX;

        $to = explode(", ", $email['to']);
        $email['to'] = array_shift($to);
        $headers = "CC: ". implode(", ", $to) . "\n";
        $headers .= "From: " . PROPOSAL_MAIL_FROM . "\n";
        $headers .= "Reply-To: " . $actorinfo['email'] . "\n";
        $headers .= "X-Mailer: " . "RFC, Docweb Proposal System <http://docs.php.net/>" . "\n"; // !!!
        $headers .= "X-RFC-Category: " . $this->pkg_category . "\n";
        $headers .= "X-RFC-Name: " . $this->pkg_name . "\n";
        $headers .= "X-RFC-Status: " . $this->getStatus() . "\n";

        $res = mail($email['to'], $email['subject'], $email['text'],
                    $headers, '-f doc-web@lists.php.net'); // !!!

        if (!$res) {
            return PEAR::raiseError('Could not send notification email.');
        }
        return true;
    }
}



class ppComment {

    var $pkg_prop_id;

    var $user_handle;

    var $timestamp;

    var $comment;

    var $table;

    function ppComment($dbhResArr, $table = 'package_proposal_changelog')
    {
        foreach ($dbhResArr as $name => $value) {
            $this->$name = $value;
        }
        $this->table = $table;
    }

    function get($proposalId, $handle, $timestamp,
                 $table = 'package_proposal_changelog')
    {
        global $dbh;
        $sql = "SELECT *, timestamp FROM ".$table." WHERE pkg_prop_id = ".$proposalId."
	        AND user_handle='".$handle."' AND timestamp = ".$timestamp;
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $set = $res->fetchRow(DB_FETCHMODE_ASSOC);
        $comment =& new ppComment($set);
        return $comment;
    }

    function &getAll($proposalId, $table = 'package_proposal_changelog')
    {
        global $dbh;
        $sql = "SELECT *, timestamp FROM ".$table." WHERE pkg_prop_id = ".$proposalId." ORDER BY timestamp DESC";
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $comments = array();
        while ($set = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $comments[] =& new ppVote($set);
        }
        return $comments;
    }

    function store($proposalId)
    {
        global $dbh;
        if (empty($this->user_handle)) {
            return PEAR::raiseError("Not initialized");
        }
        $sql = "INSERT INTO ".$this->table." (pkg_prop_id, user_handle, comment, timestamp)
                    VALUES (".$proposalId.", ".$dbh->quoteSmart($this->user_handle).",
		     ".$dbh->quoteSmart($this->comment).", ".time().")";
        $res = $dbh->query($sql);
        return $res;
    }

    function delete()
    {
        global $dbh;
        if (empty($this->table) || empty($this->user_handle) || empty($this->pkg_prop_id) || empty($this->timestamp)) {
            return PEAR::raiseError("Inconsistant comment data. Can not delete comment.");
        }
        $sql = "DELETE FROM ".$this->table." WHERE user_handle = '".$this->user_handle."'
	 AND pkg_prop_id = ".$this->pkg_prop_id." AND timestamp = ".$this->timestamp;
        $res = $dbh->query($sql);
        return true;
    }
} // end class ppComment

global $proposalReviewsMap;
$proposalReviewsMap = array(
                            'cursory'   => 'Cursory source review',
                            'deep'      => 'Deep source review',
                            'test'      => 'Tested');

class ppVote {

    var $pkg_prop_id;

    var $user_handle;

    var $value;

    var $reviews;

    var $is_conditional;

    var $comment;

    var $timestamp;

    function ppVote($dbhResArr)
    {
        foreach ($dbhResArr as $name => $value) {
            $this->$name = $value;
        }
    }

    function get(&$dbh, $proposalId, $handle)
    {
        $sql = "SELECT *, timestamp FROM package_proposal_votes WHERE 
	pkg_prop_id = ".$proposalId." AND user_handle='".$handle."'";
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        if (!$res->numRows()) {
            return null;
        }
        $set = $res->fetchRow(DB_FETCHMODE_ASSOC);
        $set['reviews'] = unserialize($set['reviews']);
        $vote =& new ppVote($set);
        return $vote;
    }

    function &getAll(&$dbh, $proposalId)
    {
        $sql = "SELECT *, timestamp FROM package_proposal_votes WHERE
	 pkg_prop_id = ".$proposalId." ORDER BY timestamp ASC";
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $votes = array();
        while ($set = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $set['reviews'] = unserialize($set['reviews']);
            $votes[$set['user_handle']] =& new ppVote($set);
        }
        return $votes;
    }

    function store($dbh, $proposalId)
    {
        if (empty($this->user_handle)) {
            return PEAR::raiseError("Not initialized");
        }
        // $this->user_handle.time() is a hack, remove '.time()' after karma system works
        $sql = "INSERT INTO package_proposal_votes (pkg_prop_id, user_handle, value, is_conditional, comment, reviews, timestamp)
                    VALUES (".$proposalId.",
		     ".$dbh->quoteSmart($this->user_handle.time()).",
		     ".$this->value.", ".(int)$this->is_conditional.",
		     ".$dbh->quoteSmart($this->comment).",
		     ".$dbh->quoteSmart(serialize($this->reviews)).",
		     ".time().")";
        $res = $dbh->query($sql);
        return $res;
    }

    function getReviews($humanReadable = false)
    {
        if ($humanReadable) {
            foreach ($this->reviews as $review) {
                $res[] = $GLOBALS['proposalReviewsMap'][$review];
            }
            return $res;
        }
        return $this->reviews;
    }

    function getSum($dbh, $proposalId)
    {
        $sql = "SELECT SUM(value) FROM package_proposal_votes WHERE 
	pkg_prop_id = ".$proposalId." GROUP BY pkg_prop_id";
        $result = $dbh->getOne($sql);
        $res['all'] = (is_numeric($result)) ? $result : 0;
        $sql = "SELECT SUM(value) FROM package_proposal_votes WHERE 
	pkg_prop_id = ".$proposalId." AND is_conditional = 1 GROUP BY pkg_prop_id";
        $result = $dbh->getOne($sql);
        $res['conditional'] = (is_numeric($result)) ? $result : 0;
        return $res;
    }

    function getCount($dbh, $proposalId)
    {
        $sql = "SELECT COUNT(user_handle) FROM package_proposal_votes WHERE
	 pkg_prop_id = ".$proposalId." GROUP BY pkg_prop_id";
        $res = $dbh->getOne($sql);
        return (!empty($res)) ? $res: " 0";
    }

    function hasVoted($dbh, $userHandle, $proposalId)
    {
        $sql = "SELECT count(pkg_prop_id) as votecount FROM package_proposal_votes
                    WHERE pkg_prop_id = ".$proposalId." AND user_handle = '".$userHandle."'
                    GROUP BY pkg_prop_id";
        $votes = $dbh->query($sql);
        return (bool)($votes->numRows());
    }

}

global $proposalTypeMap;
$proposalTypeMap = array(
                         'pkg_patch'            => "Patch (.diff/.txt)",
                         'pkg_source'           => "Source file (.phps/.htm)",
                         'pkg_example'          => "Example (.php)",
                         'pkg_example_source'   => "Example source (.phps/.htm)",
                         'pkg_doc'              => "Documentation",
			 'pkg_site'             => "Website");

class ppLink {

    var $pkg_prop_id;

    var $type;

    var $url;

    function ppLink($dbhResArr)
    {
        foreach ($dbhResArr as $name => $value) {
            $this->$name = $value;
        }
    }

    function &getAll(&$dbh, $proposalId)
    {
        $sql = "SELECT * FROM package_proposal_links WHERE pkg_prop_id = ".$proposalId." ORDER BY type";
        $res = $dbh->query($sql);
        if (DB::isError($res)) {
            return $res;
        }
        $links = array();
        while ($set = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
            $links[] =& new ppLink($set);
        }
        return $links;
    }

    function deleteAll($dbh, $proposalId)
    {
        $sql = "DELETE FROM package_proposal_links WHERE pkg_prop_id = ".$proposalId;
        $res = $dbh->query($sql);
        return $res;
    }

    function store($dbh, $proposalId)
    {
        $sql = "INSERT INTO package_proposal_links (pkg_prop_id, type, url)
                    VALUES (".$proposalId.", ".$dbh->quoteSmart($this->type).", ".$dbh->quoteSmart($this->url).")";
        $res = $dbh->query($sql);
        return $res;
    }

    function getType($humanReadable = false)
    {
        if ($humanReadable) {
            return $GLOBALS['proposalTypeMap'][$this->type];
        }
        return $this->type;
    }
}


?>

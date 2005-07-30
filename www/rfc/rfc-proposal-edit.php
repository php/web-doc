<?php

/**
 * Interface for inputing/editing a proposal.
 *
 * The <var>$proposalTypeMap</var> array is defined in
 * docweb/include/rfc/rfc.php.
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
/**
 * Obtain code for Bulletin Board markup.
 */
require_once 'HTML/BBCodeParser.php';

ob_start();

if ($proposal =& proposal::get($dbh, @$_GET['id'])) {

    echo site_header('RFC :: Editor :: '
                    . htmlspecialchars($proposal->pkg_name));
    echo '<h1>Proposal Editor for ' . htmlspecialchars($proposal->pkg_name);
    echo ' (' . $proposal->getStatus(true) . ")</h1>\n";


if (isset($_GET['delete']) AND !empty($_GET['delete'])) {

if (strlen($_GET['delete']) >= 32 &&
        strpos($_GET['delete'], "..") === FALSE && (($docwebUser == $proposal->user_handle) || is_admin())) {
        
        $hash = substr($_GET['delete'], -32);
        $file = substr($_GET['delete'], 0, -32);
        if (!file_exists($path . '/../../files/' . $hash)) {
            report_error('File ' . htmlspecialchars(stripslashes($file)) . ' does not exists');
        } else {
        $proposal->pkg_filehash = str_replace('|' . $_GET['delete'], '', $proposal->pkg_filehash);
        
        $sql = "UPDATE package_proposals SET 
        pkg_filehash = ".$dbh->quoteSmart($proposal->pkg_filehash)."
         WHERE id = ".$proposal->id;
        $res = $dbh->query($sql);
        
        // this might need some more security
        unlink($path . '/../../files/' . $hash);
        
        report_success('File ' . htmlspecialchars(stripslashes($file)) . ' deleted!');
        }
}  
            

}





// !!!
    if (!$proposal->mayEdit($docwebUser) && empty($_GET['next_stage'])) {
        report_error('You are not allowed to edit this proposal,'
                     . ' probably due to it having reached the "'
                     . $proposal->getStatus(true) . '" phase.'
                     . ' If this MUST be edited, contact someone ELSE'
                     . ' who has admin karma.');
        site_footer();
        exit;
    }
    

    if ($proposal->compareStatus('>', 'proposal') && 
        is_admin() && empty($_GET['next_stage']))
    {
        report_error('This proposal has reached the "'
                     . $proposal->getStatus(true) . '" phase.'
                     . ' Are you SURE you want to edit it?',
                     'warnings', 'WARNING:');
    }

    $proposal->getLinks($dbh);
    $id = $proposal->id;
} else {
    echo site_header('RFC :: Editor :: New Proposal');
    echo '<h1>New Proposal</h1>' . "\n";
    $id = 0;
    $proposal = null;
}


$form =& new HTML_QuickForm('proposal_edit', 'post',
                            'rfc-proposal-edit.php?id=' . $id);
$form->setMaxFileSize(round((1024 * 1024)/10));
                            
$renderer =& $form->defaultRenderer();
$renderer->setElementTemplate('
 <tr>
  <th class="form-label_left">
   <!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required -->
   {label}
  </th>
  <td class="form-input">
   <!-- BEGIN error --><span style="color: #ff0000">{error}</span><br /><!-- END error -->
   {element}
  </td>
 </tr>
');

//$categories = category::listAll(); // !!!
$mapCategories['RFC'] = 'RFC';
$mapCategories['Patches'] = 'Patches';
//foreach ($categories as $categorie) {
//    $mapCategories[$categorie['name']] = $categorie['name'];
//}



$form->addElement('select', 'pkg_category', '<label for="pkg_category" accesskey="o">Categ<span class="accesskey">o</span>ry:</label>', $mapCategories, 'id="pkg_category"');

$categoryNewElements[] =& HTML_QuickForm::createElement('checkbox', 'pkg_category_new_do', '');
$categoryNewElements[] =& HTML_QuickForm::createElement('text', 'pkg_category_new_text', '');
$categoryNew = $form->addGroup($categoryNewElements, 'pkg_category_new', 'New category:', '(only use if really needed)<br />');

$form->addElement('text', 'pkg_name', 'Name:');

$form->addElement('textarea', 'pkg_describtion', 'Description:', array('rows' => 20, 'cols' => '75'));
$form->addElement('select', 'markup', 'Markup', array('bbcode' => 'BBCode', 'wiki' => 'Wiki'));

$helpLinks[] =& HTML_QuickForm::createElement('link', 'help_bbcode', '_blank', 'rfc-bbcode-help.php', 'You can use BBCode inside your description', array('target' => '_blank'));
$helpLinks[] =& HTML_QuickForm::createElement('link', 'help_wiki', '_blank', 'http://wiki.ciaweb.net/yawiki/index.php?area=Text_Wiki&page=WikiRules', 'or Wiki markup', array('target' => '_blank'));
$form->addGroup($helpLinks, 'markup_help', '', ' ');

$form->addElement('file', 'thefile', '');
$form->addElement('static', '', '', 'Upload a file or link to your files');

    $filecount = 0;
    if ($proposal) {
        $filecount = explode('|', $proposal->pkg_filehash);

        if (@$filecount[1] == '') { // failed, no files
            $filecount = 0;
        } else {
            $filecount = (count($filecount) -1);
        }

        if (isset($_POST['submit']) AND !empty($_FILES['thefile']['name'])) 
            $filecount++;
    }
    
$form->addElement('static', '', '', 'You have uploaded '.$filecount.' file(s). To delete, click on the file');

if (!empty($proposal->pkg_filehash)) {
    $list = explode("|", htmlspecialchars(stripslashes($proposal->pkg_filehash)));

    foreach ($list as $hash) {
        if ($hash == '')
            continue;
    
        $file = substr($hash, 0, -32);

        $form->addElement('static', '', '', '<li><a href="rfc-proposal-edit.php?id='.$id.'&amp;delete='.urlencode($hash).'">'.$file.'</a></li>');

    }
}



$max = (isset($proposal->links) && (count($proposal->links) > 2)) ? (count($proposal->links) + 1) : 3;
for ($i = 0; $i < $max; $i++) {
    unset($link);
    $link[0] = $form->createElement('select', 'type', '', $proposalTypeMap);
    $link[1] = $form->createElement('text', 'url', '');
    $label = ($i == 0) ? 'Links:': '';
    $links[$i] =& $form->addGroup($link, "link[$i]", $label, ' ');
}

$form->addElement('static', '', '', '<small>To add more links, fill out all link forms and hit save. To delete a link leave the URL field blank.</small>');



if ($proposal != null && ($proposal->getStatus() != 'draft')) {
    $form->addElement('static', '', '', '<strong>If you add any text to the Changelog comment textarea,<br />then a mail will be sent to phpdoc about this update.</strong>');
    $form->addElement('textarea', 'action_comment', 'Changelog comment:', array('cols' => 80, 'rows' => 10));
}


$form->addElement('submit', 'submit', 'Save');


if ($proposal != null) {
    $defaults = array('pkg_name'    => $proposal->pkg_name,
                      'pkg_describtion' => $proposal->pkg_describtion,
                      'markup'      => $proposal->markup);
    if (isset($mapCategories[$proposal->pkg_category])) {
        $defaults['pkg_category'] = $proposal->pkg_category;
    } else {
        $defaults['pkg_category_new']['pkg_category_new_text'] = $proposal->pkg_category;
        $defaults['pkg_category_new']['pkg_category_new_do'] = true;
    }
    if ((count($proposal->links) > 0)) {
        $i = 0;
        foreach ($proposal->links as $proposalLink) {
            $defaults['link'][$i]['type'] = $proposalLink->type;
            $defaults['link'][$i]['url'] = $proposalLink->url;
            $i++;
        }
    }

    $form->setDefaults($defaults);

    switch ($proposal->status) {
        case 'draft':
            $next_stage_text = "Change status to 'Proposal'";
            break;

        case 'proposal':
            $next_stage_text = "Change status to 'Call for votes'";
            break;

        case 'vote':
        default:
            if (is_admin() &&
	     ($proposal->user_handle != $docwebUser)) {
                $next_stage_text = 'Extend vote time';
            } else {
                $next_stage_text = '';
            }
            break;
    }

    $timeline = $proposal->checkTimeLine();
    
    if (!empty($next_stage_text)) {
        if (($timeline === true) || (is_admin() && 
            ($proposal->user_handle != $docwebUser))) {
            $form->addElement('checkbox', 'next_stage', $next_stage_text);
        } else {
            $form->addElement('static', 'next_stage', '',
                              'You can set &quot;' . @$next_stage_text
                              . '&quot; after '
                              . make_utc_date($timeline) . '.');
        }
    }
}


$form->applyFilter('pkg_name', 'trim');
$form->applyFilter('pkg_describtion', 'trim');

$form->addRule('pkg_category', 'You have to select a category!', 'required', '', 'server');
$form->addRule('pkg_name', 'You have to enter a name!', 'required', '', 'server');
$form->addRule('pkg_describtion', 'You have to enter a description!', 'required', '', 'server');


if (isset($_POST['submit'])) {
    if ($form->validate()) {
        $values = $form->exportValues();

        if (!empty($_FILES['thefile']['name'])) {
            // this will need some more security checks
            if ($_FILES['thefile']['size'] >= round((1024 * 1024)/10)) {
                report_error('The file should NOT be bigger then 100kb, please upload
                              it somewhere and link to it');
                unlink($_FILES['thefile']['tmp_name']);
            } else {
                $filehash = md5($_FILES['thefile']['name'] . time());
                move_uploaded_file($_FILES['thefile']['tmp_name'],
                                   $path . '/../../files/' . $filehash);                                  
                $proposal->pkg_filehash = $proposal->pkg_filehash . '|' . basename($_FILES['thefile']['name']) . $filehash;
 
            }
        } else {
            if (isset($proposal) && empty($proposal->pkg_filehash)) {
               $proposal->pkg_filehash = '';
            }
        }
        
        if (isset($values['pkg_category_new']['pkg_category_new_do'])) {
            $values['pkg_category'] = $values['pkg_category_new']['pkg_category_new_text'];
        }

        if (isset($values['next_stage'])) {
            switch ($proposal->status) {
                case 'draft':
                    if ($proposal->checkTimeLine()) {
                       $values['proposal_date'] = time();
                       $proposal->status = 'proposal';
                       $proposal->sendActionEmail('change_status_proposal', 'mixed', $docwebUser);
                    } else {
                       PEAR::raiseError('You can not change the status now.');
                    }
                    break;

                case 'proposal':
                    if ($proposal->checkTimeLine()) {
                       $values['vote_date'] = time();
                       $proposal->status = 'vote';
                       $proposal->sendActionEmail('change_status_vote', 'mixed', $docwebUser);
                    } else {
                       PEAR::raiseError('You can not change the status now.');
                    }
                    break;

                default:
                    if ($proposal->mayEdit($docwebUser)) {
                       $values['longened_date'] = time();
                       $proposal->status = 'vote';
                       $proposal->sendActionEmail('longened_timeline_admin', 'mixed', $docwebUser);
                    }
            }
        } else {
            if (isset($proposal) && $proposal->status != 'draft') {
                if (!empty($values['action_comment'])  || (is_admin() &&
		 ($proposal->user_handle != $docwebUser))) {
                    if (empty($values['action_comment'])) {
                        PEAR::raiseError('A changelog comment is required.');
                    }
                    $proposal->addComment($values['action_comment']);
                    $proposal->sendActionEmail('edit_proposal', 'mixed', $docwebUser, $values['action_comment']);
                }
            }
        }

        $linksData = $values['link'];

        if (isset($proposal)) {
            $proposal->fromArray($values);
        } else {
            $proposal = new proposal($values);
            $proposal->user_handle = $docwebUser;
        }

        unset($proposal->links);
        for ($i = 0; $i < count($linksData); $i++) {
            $linkData['type'] = $linksData[$i]['type'];
            $linkData['url']  = $linksData[$i]['url'];

            if ($linksData[$i]['url']) {
                $proposal->addLink($dbh, new ppLink($linkData));
            }
        }

        $proposal->store($dbh);

        if (isset($values['next_stage'])) {
            $nextStage = 1;
        }

        ob_end_clean();
        header('Location: rfc-proposal-edit.php?id='
                      . $proposal->id . '&saved=1&next_stage=' . @$nextStage);
    } else {
        $pepr_form = $form->toArray();
        report_error($pepr_form['errors']);
    }
}

ob_end_flush();

if (!empty($_GET['next_stage'])) {
    $form =& new HTML_QuickForm('no-form');
    $bbox = array();
    switch ($proposal->status) {
        case 'proposal':
            $bbox[] = 'The RFC has been proposed on phpdoc.'
                    . ' All further changes will produce an update email.';
            break;

        case 'vote':
            $bbox[] = 'The RFC has been called for votes on phpdoc.'
                    . ' No further changes are allowed.';
            break;
    }
    if (is_admin()) {
        $bbox[] = 'Your changes were recorded and necessary emails'
                . ' were sent.';
    }
    if ($bbox) {
        report_success(implode(' ', $bbox));
    }
} else {
    if (!empty($_GET['saved'])) {
         report_success('Changes saved successfully.');
    }
}

if ($id != 0) {
display_pepr_nav($proposal);
}

$form->display();

echo site_footer();

?>

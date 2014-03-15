<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">
<head>
 <title><?php echo $page_title; ?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $encoding; ?>" />
 <link rel="icon" href="/images/favicon/<?php echo $project; ?>/favicon.ico" />
 <link rel="stylesheet" type="text/css" href="/style/site.css" />
 <?php
  foreach ($styles as $style_file) {
     echo '<link rel="stylesheet" type="text/css" href="/style/' . $style_file . '" />' . "\n";
 }
 ?>
</head>
<body>
 <div id="header">
  <h1><?php echo $page_h1; ?></h1>
  <div id="logos">
   <?php echo $projects; ?>
  </div>
 </div>
 <div id="langs">
  <?php echo $languages; ?>
 </div>
 <div id="page">
  <div id="sidebar">
   <div class="sidebox">
    <dl>
     <dt>&docweb.common.header.currently-focused;</dt>
     <dd><?php echo $projdisplay; ?> | <?php echo $langdisplay; 
     if (LANGC != 'all') {
      echo '&nbsp;<span class="Flag"><img src="/images/flags/' . 
      LANGC .'.png" alt="'. LANGD . '" title="' . LANGD . '" /></span>';
     }
  ?></dd>
     <dt>&docweb.common.header.insite-context-nav;</dt>
     <dd><?php echo $locallinks; ?></dd>
     <dt>&docweb.common.header.offsite-context-nav;</dt>
     <dd><?php echo $extlinks; ?></dd>
     <?php if ($showBugs) { ?>
         <dt>&docweb.common.header.project-doc-bugs;</dt>
     <dd><a href="<?php echo $bugsLink; ?>"><?php echo $bugCount; ?> &docweb.common.header.open-bugs;</a></dd>
     <?php } ?>
    </dl>
   </div>
  </div>

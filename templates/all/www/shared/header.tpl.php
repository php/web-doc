<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="$lang" lang="$lang">
<head>
 <title><?=$page_title?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=<?=$encoding?>" />
 <meta http-equiv="Content-Script-Type" content="text/javascript" />
 <meta http-equiv="Content-Style-Type" content="text/css" />
 <meta http-equiv="Content-Language" content="<?=$lang?>" />
 <link rel="shortcut icon" href="/images/favicon/<?=$project?>/favicon.ico" />
 <style type="text/css">
  @import url(/style/site.css);
  <?php
  foreach ($styles as $style_file) {
      echo "@import url(/style/$style_file);\n";
  }
  ?>
 </style>
</head>
<body>
 <div id="header">
  <h1><?=$page_h1?></h1>
  <div id="logos">
   <?=$projects?>
  </div>
 </div>
 <div id="langs">
  <?=$languages?>
 </div>
 <div id="page">
  <div id="sidebar">
   <div class="sidebox">
    <dl>
     <dt>&docweb.common.header.currently-focused;</dt>
     <dd><?=$projdisplay?> | <?=$langdisplay?></dd>
     <dt>&docweb.common.header.insite-context-nav;</dt>
     <dd><?=$locallinks?></dd>
     <dt>&docweb.common.header.insite-context-nav;</dt>
     <dd><?=$extlinks?></dd>
    </dl>
   </div>
  </div>

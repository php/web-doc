<?php
/* $Id$ */

include '../include/lib_general.inc.php';

$red_status = isset($_SERVER["REDIRECT_STATUS"]) ? $_SERVER["REDIRECT_STATUS"] : '';
$title = 'Apache :: Error ' . $red_status;
echo site_header($title);
?>
<h2><?php echo $title; ?></h2>
<p>
<?php 
if( isset($_SERVER['REDIRECT_ERROR_NOTES']) ) {
  echo $_SERVER['REDIRECT_ERROR_NOTES']; 
} elseif( isset($_SERVER['REDIRECT_REDIRECT_ERROR_NOTES']) ) {
  echo $_SERVER['REDIRECT_REDIRECT_ERROR_NOTES'];
} else {
  echo 'No message available.';
}


?>
</p>
<?php
echo site_footer();
?>

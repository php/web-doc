<?php
    $red_status = isset($_SERVER["REDIRECT_STATUS"]) ? $_SERVER["REDIRECT_STATUS"] : '';
?>
<h2>&docweb.common.header.error; <?php echo $red_status; ?></h2>
<p>
<?php 
if (isset($_SERVER['REDIRECT_ERROR_NOTES'])) {
    echo $_SERVER['REDIRECT_ERROR_NOTES'];
} elseif (isset($_SERVER['REDIRECT_REDIRECT_ERROR_NOTES'])) {
    echo $_SERVER['REDIRECT_REDIRECT_ERROR_NOTES'];
} else {
    if ($red_status == 404) {
    ?>
        &docweb.error.404;:
    <?php
        echo htmlspecialchars($_SERVER['REQUEST_URI']);
    } else {
    ?>
        &docweb.error.nomesg;
    <?php        
    }
}
?>
</p>

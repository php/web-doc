 </div>
 <div id="footer">
  <p>
   <a href="<?php echo $master; ?>copyright.php">&docweb.common.footer.copyright;</a> 2004-2006 &docweb.common.footer.copyright-text;.
  </p>
  <?php 
    if(isset($cvs_version)) { 
        echo "<p>\n    $cvs_version\n  </p>\n  "; 
    } 
  ?><p>
   <a href="<?php echo $master; ?>credits.php">&docweb.common.footer.credits;</a>
   |
   <a href="<?php echo $master; ?>contact.php">&docweb.common.footer.contact;</a>
  </p>
 </div>
</body>
</html>

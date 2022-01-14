</section>
<footer>
  <nav class="fullscreen">
    <ul>
      <li><a href="//php.net/copyright">Copyright © 2001-2021 The PHP Group</a></li>
      <li><a href="//php.net/sites">Other PHP.net sites</a></li>
      <li><a href="//php.net/privacy">Privacy policy</a></li>
    </ul>
  </nav>
</footer>

<script src='//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>
<script src='//shared.php.net/js/external/mousetrap.min.js?filemtime=1421836947'></script>
<script src='//shared.php.net/js/common.js?filemtime=1421836947'></script>
<script src="//cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
  var clipboard = new ClipboardJS('.btn');
  clipboard.on('success', function (e) {
     console.log(e);
  });
  clipboard.on('error', function (e) {
     console.log(e);
  });
</script>
</div><!-- .wrap -->
</body>
</html>

<?php

require_once '../../include/lib_general.inc.php';
site_header('RFC :: BBCode Help');

/*
 * This file needs some image fixing
 */

?>

<table width="100%" cellspacing="0" cellpadding="2" border="0">
 <tr>
  <td class="bodyline" bgcolor="white">

   <table width="100%" cellspacing="0" cellpadding="10" border="0">
    <tr>
     <td>

<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0" align="center">
 <tr>
  <th class="thHead"><h1>BBCode Guide</h1></th>
 </tr>
 <tr>
  <td class="row1">
   <span class="gen"><b>Introduction</b></span><br />
   <span class="gen"><a href="#0" class="postlink">What is BBCode?</a></span><br />
   <br />
   <span class="gen"><b>Text Formatting</b></span><br />
   <span class="gen"><a href="#1" class="postlink">How to create bold, italic and underlined text</a></span><br />
   <span class="gen"><a href="#2" class="postlink">How to change the text colour or size</a></span><br />
   <span class="gen"><a href="#3" class="postlink">Can I combine formatting tags?</a></span><br />
   <br />
   <span class="gen"><b>Quoting and outputting fixed-width text</b></span><br />
   <span class="gen"><a href="#4" class="postlink">Quoting text in replies</a></span><br />
   <span class="gen"><a href="#5" class="postlink">Outputting code or fixed width data</a></span><br />
   <br />
   <span class="gen"><b>Generating lists</b></span><br />
   <span class="gen"><a href="#6" class="postlink">Creating an Un-ordered list</a></span><br />
   <span class="gen"><a href="#7" class="postlink">Creating an Ordered list</a></span><br />
   <br />
   <span class="gen"><b>Creating Links</b></span><br />
   <span class="gen"><a href="#8" class="postlink">Linking to another site</a></span><br />
   <br />
   <span class="gen"><b>Showing images in posts</b></span><br />
   <span class="gen"><a href="#9" class="postlink">Adding an image to a post</a></span><br />
   <br />
  </td>
 </tr>
 <tr>
  <td class="catBottom" height="28">&nbsp;</td>
 </tr>
</table>

<br clear="all" />

<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0" align="center">
 <tr>
  <td class="catHead" height="28" align="center"><h2>1</h2></td>
 </tr>
 <tr>
  <td class="row1" align="left" valign="top"><span class="postbody"><a name="0"></a><b>What is BBCode?</b></span><br /><span class="postbody">BBCode is a special implementation of HTML. Whether you can actually use BBCode in your posts on the forum is determined by the administrator. In addition, you can disable BBCode on a per post basis via the posting form. BBCode itself is similar in style to HTML: tags are enclosed in square braces [ and ] rather than &lt; and &gt; and it offers greater control over what and how something is displayed. Depending on the template you are using you may find adding BBCode to your posts is made much easier through a clickable interface above the message area on the posting form. Even with this you may find the following guide useful.<br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
</table>

<br clear="all" />
<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0" align="center">
 <tr>
  <td class="catHead" height="28" align="center"><h2>1</h2></td>
 </tr>
 <tr>
  <td class="row1" align="left" valign="top"><span class="postbody"><a name="1"></a><b>How to create bold, italic and underlined text</b></span><br /><span class="postbody">BBCode includes tags to allow you to quickly change the basic style of your text. This is achieved in the following ways: <ul><li>To make a piece of text bold enclose it in <b>[b][/b]</b>, eg. <br /><br /><b>[b]</b>Hello<b>[/b]</b><br /><br />will become <b>Hello</b></li><li>For underlining use <b>[u][/u]</b>, for example:<br /><br /><b>[u]</b>Good Morning<b>[/u]</b><br /><br />becomes <u>Good Morning</u></li><li>To italicise text use <b>[i][/i]</b>, eg.<br /><br />This is <b>[i]</b>Great!<b>[/i]</b><br /><br />would give This is <i>Great!</i></li></ul><br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
 <tr>
  <td class="row2" align="left" valign="top"><span class="postbody"><a name="2"></a><b>How to change the text colour or size</b></span><br /><span class="postbody">To alter the color or size of your text the following tags can be used. Keep in mind that how the output appears will depend on the viewers browser and system: <ul><li>Changing the colour of text is achieved by wrapping it in <b>[color=][/color]</b>. You can specify either a recognised colour name (eg. red, blue, yellow, etc.) or the hexadecimal triplet alternative, eg. #FFFFFF, #000000. For example, to create red text you could use:<br /><br /><b>[color=red]</b>Hello!<b>[/color]</b><br /><br />or<br /><br /><b>[color=#FF0000]</b>Hello!<b>[/color]</b><br /><br />will both output <span style="color:red">Hello!</span></li><li>Changing the text size is achieved in a similar way using <b>[size=][/size]</b>. This tag is dependent on the template you are using but the recommended format is a numerical value representing the text size in pixels, starting at 1 (so tiny you will not see it) through to 29 (very large). For example:<br /><br /><b>[size=9]</b>SMALL<b>[/size]</b><br /><br />will generally be <span style="font-size:9px">SMALL</span><br /><br />whereas:<br /><br /><b>[size=24]</b>HUGE!<b>[/size]</b><br /><br />will be <span style="font-size:24px">HUGE!</span></li></ul><br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
 <tr>
  <td class="row1" align="left" valign="top"><span class="postbody"><a name="3"></a><b>Can I combine formatting tags?</b></span><br /><span class="postbody">Yes, of course you can; for example to get someones attention you may write:<br /><br /><b>[size=18][color=red][b]</b>LOOK AT ME!<b>[/b][/color][/size]</b><br /><br />this would output <span style="color:red;font-size:18px"><b>LOOK AT ME!</b></span><br /><br />We don't recommend you output lots of text that looks like this, though! Remember that it is up to you, the poster, to ensure that tags are closed correctly. For example, the following is incorrect:<br /><br /><b>[b][u]</b>This is wrong<b>[/b][/u]</b><br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
</table>

<br clear="all" />
<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0" align="center">
 <tr>
  <td class="catHead" height="28" align="center"><h2>1</h2></td>
 </tr>
 <tr>
  <td class="row1" align="left" valign="top"><span class="postbody"><a name="4"></a><b>Quoting text in replies</b></span><br /><span class="postbody">There are two ways you can quote text: with a reference or without.<ul><li>When you utilise the Quote function to reply to a post on the board you should notice that the post text is added to the message window enclosed in a <b>[quote=""][/quote]</b> block. This method allows you to quote with a reference to a person or whatever else you choose to put. For example, to quote a piece of text Mr. Blobby wrote, you would enter:<br /><br /><b>[quote="Mr. Blobby"]</b>The text Mr. Blobby wrote would go here<b>[/quote]</b><br /><br />The resulting output will automatically add: Mr. Blobby wrote: before the actual text. Remember that you <b>must</b> include the quotation marks "" around the name you are quoting -- they are not optional.</li><li>The second method allows you to blindly quote something. To utilise this enclose the text in <b>[quote][/quote]</b> tags. When you view the message it will simply show: Quote: before the text itself.</li></ul><br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
 <tr>
  <td class="row2" align="left" valign="top"><span class="postbody"><a name="5"></a><b>Outputting code or fixed width data</b></span><br /><span class="postbody">If you want to output a piece of code or in fact anything that requires a fixed width with a Courier-type font, you should enclose the text in <b>[code][/code]</b> tags, eg.<br /><br /><b>[code]</b>echo "This is some code";<b>[/code]</b><br /><br />All formatting used within <b>[code][/code]</b> tags is retained when you later view it.<br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
</table>

<br clear="all" />
<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0" align="center">
 <tr>
  <td class="catHead" height="28" align="center"><h2>1</h2></td>
 </tr>
 <tr>
  <td class="row1" align="left" valign="top"><span class="postbody"><a name="6"></a><b>Creating an Un-ordered list</b></span><br /><span class="postbody">BBCode supports two types of lists, unordered and ordered. They are essentially the same as their HTML equivalents. An unordered list ouputs each item in your list sequentially one after the other indenting each with a bullet character. To create an unordered list you use <b>[list][/list]</b> and define each item within the list using <b>[*]</b>. For example, to list your favorite colours you could use:<br /><br /><b>[list]</b><br /><b>[*]</b>Red<br /><b>[*]</b>Blue<br /><b>[*]</b>Yellow<br /><b>[/list]</b><br /><br />This would generate the following list:<ul><li>Red</li><li>Blue</li><li>Yellow</li></ul><br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
 <tr>
  <td class="row2" align="left" valign="top"><span class="postbody"><a name="7"></a><b>Creating an Ordered list</b></span><br /><span class="postbody">The second type of list, an ordered list gives you control over what is output before each item. To create an ordered list you use <b>[list=1][/list]</b> to create a numbered list or alternatively <b>[list=a][/list]</b> for an alphabetical list. As with the unordered list items are specified using <b>[*]</b>. For example:<br /><br /><b>[list=1]</b><br /><b>[*]</b>Go to the shops<br /><b>[*]</b>Buy a new computer<br /><b>[*]</b>Swear at computer when it crashes<br /><b>[/list]</b><br /><br />will generate the following:<ol type="1"><li>Go to the shops</li><li>Buy a new computer</li><li>Swear at computer when it crashes</li></ol>Whereas for an alphabetical list you would use:<br /><br /><b>[list=a]</b><br /><b>[*]</b>The first possible answer<br /><b>[*]</b>The second possible answer<br /><b>[*]</b>The third possible answer<br /><b>[/list]</b><br /><br />giving<ol type="a"><li>The first possible answer</li><li>The second possible answer</li><li>The third possible answer</li></ol><br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
</table>

<br clear="all" />
<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0" align="center">
 <tr>
  <td class="catHead" height="28" align="center"><h2>1</h2></td>
 </tr>
 <tr>
  <td class="row1" align="left" valign="top"><span class="postbody"><a name="8"></a><b>Linking to another site</b></span><br /><span class="postbody">phpBB BBCode supports a number of ways of creating URIs, Uniform Resource Indicators better known as URLs.<ul><li>The first of these uses the <b>[url=][/url]</b> tag; whatever you type after the = sign will cause the contents of that tag to act as a URL. For example, to link to phpBB.com you could use:<br /><br /><b>[url=http://www.phpbb.com/]</b>Visit phpBB!<b>[/url]</b><br /><br />This would generate the following link, <a href="http://www.phpbb.com/" target="_blank">Visit phpBB!</a> You will notice the link opens in a new window so the user can continue browsing the forums if they wish.</li><li>If you want the URL itself displayed as the link you can do this by simply using:<br /><br /><b>[url]</b>http://www.phpbb.com/<b>[/url]</b><br /><br />This would generate the following link: <a href="http://www.phpbb.com/" target="_blank">http://www.phpbb.com/</a></li><li>Additionally phpBB features something called <i>Magic Links</i>which will turn any syntatically correct URL into a link without you needing to specify any tags or even the leading http://. For example typing www.phpbb.com into your message will automatically lead to <a href="http://www.phpbb.com/" target="_blank">www.phpbb.com</a> being output when you view the message.</li><li>The same thing applies equally to email addresses; you can either specify an address explicitly, like:<br /><br /><b>[email]</b>no.one@domain.adr<b>[/email]</b><br /><br />which will output <a href="emailto:no.one@domain.adr">no.one@domain.adr</a> or you can just type no.one@domain.adr into your message and it will be automatically converted when you view.</li></ul>As with all the BBCode tags you can wrap URLs around any of the other tags such as <b>[img][/img]</b> (see next entry), <b>[b][/b]</b>, etc. As with the formatting tags it is up to you to ensure the correct open and close order is following. For example:<br /><br /><b>[url=http://www.phpbb.com/][img]</b>http://www.phpbb.com/images/phplogo.gif<b>[/url][/img]</b><br /><br />is <u>not</u> correct which may lead to your post being deleted so take care.<br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
</table>

<br clear="all" />
<table class="forumline" width="100%" cellspacing="1" cellpadding="3" border="0" align="center">
 <tr>
  <td class="catHead" height="28" align="center"><h2>1</h2></td>
 </tr>
 <tr>
  <td class="row1" align="left" valign="top"><span class="postbody"><a name="9"></a><b>Adding an image to a post</b></span><br /><span class="postbody">phpBB BBCode incorporates a tag for including images in your posts. Two very important things to remember when using this tag are: many users do not appreciate lots of images being shown in posts and second, the image you display must already be available on the Internet (it cannot exist only on your computer, for example, unless you run a webserver!). There is currently no way of storing images locally with phpBB (all these issues are expected to be addressed in the next release of phpBB). To display an image, you must surround the URL pointing to the image with <b>[img][/img]</b> tags. For example:<br /><br /><b>[img]</b>http://www.phpbb.com/images/phplogo.gif<b>[/img]</b><br /><br />As noted in the URL section above you can wrap an image in a <b>[url][/url]</b> tag if you wish, eg.<br /><br /><b>[url=http://www.phpbb.com/][img]</b>http://www.phpbb.com/images/phplogo.gif<b>[/img][/url]</b><br /><br />would generate:<br /><br /><a href="http://www.phpbb.com/" target="_blank"><img src="gifs/pearsmall.gif" border="0" alt="" /></a><br /><br /><a class="postlink" href="#Top">Back to top</a></span></td>
 </tr>
 <tr>
  <td class="spaceRow" height="1"><img src="templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
 </tr>
</table>



<div align="center"><span class="copyright"><br /><br />
<!--
 We request you retain the full copyright notice below including the link to www.phpbb.com.
 This not only gives respect to the large amount of time given freely by the developers
 but also helps build interest, traffic and use of phpBB 2.0. If you cannot (for good
 reason) retain the full copyright we request you at least leave in place the
 Powered by phpBB 2.0.6 line, with phpBB linked to www.phpbb.com. If you refuse
 to include even this then support on our forums may be affected.

 The phpBB Group : 2002
// -->

     </td>
    </tr>
   </table>


   <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
     <td class="bgBottom" height="37" align="center">
     <p style="color: #444444; text-align: center; padding: 0; margin: 0; font-size: 10px; padding-top: 2px; ">Powered by <a href="http://www.phpbb.com" class="copyright">phpBB</a> &copy; 2001, 2003 <a href="/about.php" class="copyright">phpBB Group</a></td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</body>
</html>

<?php

site_footer();

?>

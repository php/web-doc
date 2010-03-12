<!--
vim: et ts=2 sw=2 ft=html fdm=marker
-->
<?php
function bugfix($number) { echo "Fixed bug "; bugl($number); }
function bugl($number)   { echo "<a href=\"http://bugs.php.net/$number\">#$number</a>"; }
?>
<p>For PhD documentation see the <a href="/phd/docs/">PhD: The definitive guide to PHP's DocBook Rendering System</a> section.</p>
<p>PhD releases:</p>

<ul id="releases">
  <li>
    <a href="/get/PhD-1.0.0.tgz">PhD 1.0.0</a> <span class="date">11. March 2010</span><!-- {{{ -->
    <ul class="fixes">
      <li>Use textual descriptions for VERBOSE_xxx messages (Richard Quadling)</li>
      <li>Added support for DBTimestamp Processing Instruction. (Moacir)</li>
      <li>Added support for edition, inlinemediaobject, exceptionname, firstterm, trademark and edition Docbook5 elements. (Hannes)</li>
      <li>Updated translations: (Kalle)
        <ul>
         <li>Danish</li>
         <li>Swedish</li>
        </ul>
      </li>
      <li>Disabled colored output on Windows. (Kalle)</li>
      <li>Stopped double encoding of entities in CHM TOC, Index and keyword lists (Richard Quadling)</li>
      <li><?php bugfix(45098); ?> - Named constants require long opt. (Hannes)</li>
      <li><?php bugfix(50668); ?> - Add xinclude processing in PhD. (Shahar Evron)</li>
      <li><?php bugfix(50799); ?> - No text mapping for screen. (Paul Jones)</li>
      <li><?php bugfix(51070); ?> - Double acronym tags in HTML output. (Moacir)</li>
      <li>
        <a href="/get/PhD_IDE-1.0.0.tgz">PhD_IDE 1.0.0</a> <span class="date">11. March 2010</span>
        <ul class="fixes">
          <li>Initial Release \o/</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_Generic-1.0.0.tgz">PhD_Generic 1.0.0</a> <span class="date">11. March 2010</span>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.0.0.tgz">PhD_PHP 1.0.0</a> <span class="date">11. March 2010</span>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.0.0.tgz">PhD_PEAR 1.0.0</a> <span class="date">11. March 2010</span>
      </li>
      <li>
        <a href="/get/PhD_GeSHi-1.0.0.tgz">PhD_GeSHi 1.0.0</a> <span class="date">11. March 2010</span>
      </li>
      <li>
        <a href="/get/PhD_GeSHi11x-1.0.0.tgz">PhD_GeSHi11x 1.0.0</a> <span class="date">11. March 2010</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.9.1.tgz">PhD 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added fallback option to find English files when unable to find translated files (Richard Quadling)</li>
      <li>Added VERBOSE_ERRORS, VERBOSE_INFO and VERBOSE_WARNINGS to group verbose levels (Richard Quadling)</li>
      <li>Added VERBOSE_MISSING_ATTRIBUTES verbose level (Richard Quadling)</li>
      <li>Separated PhD verbose messages into informational and warnings (Richard Quadling)</li>
      <li>PhD verbose warning messages are colored magenta (Richard Quadling)</li>
      <li>Added MediaManger-&gt;findFile() method to return full filename of required image (Richard Quadling)</li>
      <li>Added the --css option (Moacir)</li>
      <li>Added the --forceindex option (Christian)</li>
      <li>Fixed --noindex option which did not work properly (Christian)</li>
      <li>Fixed --verbose option which did not work properly (Moacir)</li>
      <li><?php bugfix(45071); ?> - Links to require/include(_once) not rendered (Moacir)</li>
      <li><?php bugfix(47406); ?> - Add support for external css (Moacir)</li>
      <li><?php bugfix(48264); ?> - No style for HTML version of php docs (Moacir)</li>
      <li><?php bugfix(49547); ?> - default of --color is true, not false (Richard Quadling)</li>
      <li><?php bugfix(49675); ?> - Missing links in SeeAlso (Moacir)</li>
      <li><?php bugfix(49743); ?> - Problem with functions both procedural and oo (Moacir)</li>
      <li><?php bugfix(49839); ?> - Patch to clean up peardoc output (Michael Gauthier)</li>
      <li>
        <a href="/get/PhD_Generic-0.9.1.tgz">PhD_Generic 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul class="fixes">
          <li>Added support for &lt;errortext&gt; (Hannes)</li>
          <li>Implemented PEAR request #2390: RSS feeds for PEAR Dcumentation Index (Christian)</li>
          <li>Removed the format php (Moacir)</li>
          <li>VERBOSE_MISSING_ATTRIBUTE message generated when missing one of the width/height attributes on imagedata (Richard Quadling)</li>
          <li>VERBOSE_MISSING_ATTRIBUTE message generated when missing alt attributes on mediaobject &gt; imagedata (Richard Quadling)</li>
          <li><?php bugfix(49925); ?> - imagedata now supports width and/or depth (becomes width and/or height) (Richard Quadling)</li>
        </ul><!-- }}} -->
      </li>
      <li>
        <a href="/get/PhD_PHP-0.9.1.tgz">PhD_PHP 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul class="fixes">
          <li>Added new output format TocFeed (Moacir)</li>
        </ul><!-- }}} -->
      </li>
      <li>
        <a href="/get/PhD_PEAR-0.9.1.tgz">PhD_PEAR 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul class="fixes">
          <li>Added Next/Prev and Image Zoom buttons to CHM build (Richard Quadling)</li>
          <li>Add title attribute to anchor tags so address can be seen in CHM files for external links (Richard Quadling)</li>
          <li>Implemented PEAR request #2390: RSS feeds for PEAR Dcumentation Index (Christian)</li>
        </ul><!-- }}} -->
      </li>
      <li>
        <a href="/get/PhD_GeSHi-0.9.1.tgz">PhD_GeSHi 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul class="fixes">
          <li>Initial Release (Christian)</li>
        </ul><!-- }}} -->
      </li>
      <li>
        <a href="/get/PhD_GeSHi11x-0.9.1.tgz">PhD_GeSHi11x 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul class="fixes">
          <li>Initial Release (Christian)</li>
        </ul><!-- }}} -->
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.9.0.tgz">PhD 0.9.0</a> <span class="date">09. September 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Use namespaces (Moacir)</li>
      <li>Use PEAR classname conventions (Christian)</li>
      <li>Add support for dbhtml Process Instructions (PI) (Moacir)</li>
      <li>Add the --package option (Moacir)</li>
      <li>Add the Generic Package (Moacir)</li>
      <li>Add the phpdotnet/phd namespace (Christian)</li>
      <li>Kill themes and add a concept of "packages" (Moacir)</li>
      <li>Rewrite indexer (Hannes, Rudy, Moacir)</li>
      <li>Rewrite program flow (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.8.tgz">PhD 0.4.8</a> <span class="date">28. August 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Add support for external troff highlighter in man pages (Christian)</li>
      <li>Add title attribute to anchor tags so address can be seen in CHM files for external links (Richard Q.)</li>
      <li>CVS-&gt;SVN langs migration (Philip)</li>
      <li><?php bugfix(49006); ?> (The manpage format groups function arguments incorrectly) (Moacir)</li>
      <li><?php bugfix(49005); ?> (Reference sign in function prototypes in the manpage rendering) (Moacir)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.7.tgz">PhD 0.4.7</a> <span class="date">07. May 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added support for &lt;token&gt; (Christian)</li>
      <li>Added support for &lt;simplesect&gt; (Richard Q, Nilgun)</li>
      <li>Improved support for &lt;tag&gt; classes (Christian)</li>
      <li>Improved support for &lt;variablelist&gt; (Nilgun)</li>
      <li>Updated translations:
        <ul>
          <li>Danish (Kalle)</li>
          <li>Swedish (Kalle)</li>
          <li>Turkish (Nilgun)</li>
        </ul>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.6.tgz">PhD 0.4.6</a> <span class="date">07. March 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added language support with unknownversion into phpdotnet theme. (Philip)</li>
      <li>phpbook/phpbook-xsl/version.xml is no longer used (Hannes)</li>
      <li>Fix
        <a href="http://pear.php.net/bugs/bug.php?id=15967">PEAR bug #15967</a>:
        Wrong ID passed to pearweb manualFooter() (Christian)
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.5.tgz">PhD 0.4.5</a> <span class="date">19. February 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Changed copyright year to 2007-2009. (Christian)</li>
      <li>Fixed PEAR chm manual title for french. (Laurent)</li>
      <li>Fixed PEAR chm navbar alignment. (Laurent)</li>
      <li><?php bugfix(47408); ?> (Same image directory used for each theme). (Christian)</li>
      <li><?php bugfix(47413); ?> (PEAR themes don't work when rendered together at once). (Christian)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.4.tgz">PhD 0.4.4</a> <span class="date">16. February 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Add support for &lt;package&gt; in pear theme. (Christian)</li>
      <li>Replace ereg_replace with preg_replace. (Richard Quadling)</li>
      <li>Implement request <?php bugl(47201); ?> (Allow custom source code highlighter (e.g. Geshi)). (Christian)</li>
      <li>Support &lt;uri&gt; and &lt;screenshot&gt;. (Christian)</li>
      <li>Copy images automatically using MediaManager. (Christian)</li>
      <li>Update polish phd translation. (Jarosław Głowacki)</li>
      <li>Generate syntactically correct php files for pearweb when description or title have a quote in it. (Christian)</li>
      <li>Improve object orientated version info support. (Hannes)</li>
      <li>Fixed HTML problems with empty paragraphs in examples and qandaset questions. (Christian)</li>
      <li>Fixed encoding issues in the Polish CHM builds. (Jarosław Głowacki)</li>
      <li><?php bugfix(47362); ?> (&lt;h1&gt; tag gets omitted in bightml). (Christian)</li>
      <li><?php bugfix(47196); ?> (improve render of initializer tag). (Laurent)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.3.tgz">PhD 0.4.3</a> <span class="date">17. January 2009</span><!-- {{{ -->
    <ul class="fixes">
      <li>Automatically add anchors for refsect roles. (Hannes)</li>
      <li>Added description for seealso links. (Hannes)</li>
      <li>Fixed a bug where prefaces had unlisted childrens. (Hannes)</li>
      <li>Compressed methodnames in classsynopsis again. (Hannes)</li>
      <li>Added Next/Prev and Image Zoom buttons to CHM build (Richard Quadling)</li>
      <li><?php bugfix(46941); ?> (FR: Find broken links). (Hannes)</li>
      <li><?php bugfix(46931); ?> (wrong order of the META tag in the HEAD element). (Chen Gang)</li>
      <li><?php bugfix(46924); ?> (parameter elements force incorrect line breaks). (Hannes)</li>
      <li><?php bugfix(46726); ?> (Incorrect HTML output). (Hannes)</li>
      <li><?php bugfix(46714); ?> (change deprecated ereg_replace(Since PHP 5.3.0) to preg_replace). (Chen Gang)</li>
      <li><?php bugfix(45570); ?> (PhD doesn't use the colspec "align" attribute in xhtml output). (Hannes)</li>
      <li><?php bugfix(45318); ?> (table colspec rendering issues). (Hannes)</li>
      <li><?php bugfix(44598); ?> (Much space on 'expected output' section). (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.2.tgz">PhD 0.4.2</a> <span class="date">18. December 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added support for phd:chunk="false" attribute on chunks. (Christian)</li>
      <li>Added Turkish support for CHM. (Nilgun)</li>
      <li>Added option (-L/--lang language) to use for CHM headers. (Hannes)</li>
      <li>Added support for orderedlist numeration. (Hannes)</li>
      <li>Added fallback to PHP_Compat getopt() on Windows. (Christian)</li>
      <li>Added anchors for tips, warnings, important and notes. (Nilgun)</li>
      <li>&lt;interfacename&gt; now automagically links to interfaces. (Hannes)</li>
      <li>Automatically use the phpdoc configure.php generated version information file if it exists. (Hannes)</li>
      <li>Removed default border for formal tables. (Philip)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.1.tgz">PhD 0.4.1</a> <span class="date">08. November 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li><?php bugfix(46413);?> Weird examples in Unix manual pages (Rudy)</li>
      <li>Added varlistentries to the CHM index. (Rudy)</li>
      <li>Language support (for autogenerated texts):
        <ul>
          <li>Turkish (by Nilgün Belma Bugüner)</li>
        </ul>
      </li>
      <li>Prevent prev/next errors in combination with chunking elements without ids (Christian)</li>
      <li>Support double nested chunking elements without ids (Christian)</li>
      <li>Make table captions render properly in peardoc (Christian)</li>
      <li>add support for &lt;arg&gt; and &lt;cmdsynopsis&gt; used in peardoc (Christian)</li>
      <li><?php bugfix(46415); ?>: Don't chunk first section on parent site when it has children. (Christian)</li>
      <li>Implement request <?php bugl(46411); ?>: Allow random chunking depths (Christian)</li>
      <li>Implement request <?php bugl(46412); ?>: Allow random TOC depths (Christian)</li>
      <li>Implement request <?php bugl(46493); ?>: Implement new pear api linking tag (Christian)</li>
      <li>Make pear chm theme work (Christian)</li>
      <li>Make html generated in pear themes nearly 100% valid XHTML (Christian)</li>
      <li>Make "Prev" links work correctly on last pages (e.g. last in chapter) (Christian)</li>
      <li>Change double quotes to single ones, gives speedup for pear builds (Brett)</li>
      <li>Docblock enhancements and Coding Standards fixes (Brett)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.0.tgz">PhD 0.4.0</a> <span class="date">20. October 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added PEAR XHTML theme. (Rudy, Christian)</li>
      <li>Added support for new elements (Christian)
        <ul>
          <li>glossentry</li>
          <li>glossdef</li>
          <li>glosslist</li>
          <li>important</li>
          <li>paramdef</li>
          <li>personblurb</li>
          <li>phrase</li>
          <li>prompt</li>
          <li>releaseinfo</li>
          <li>spanspec</li>
          <li>qandadiv</li>
        </ul>
      </li>
      <li>Improved &lt;email&gt; support (now creates mailto: links). (Christian)</li>
      <li>Chunks without xml:id are no longer chunked. (Christian)</li>
      <li><?php bugfix(46252); ?> (Class properties links to a function if theres one with the same name). (Hannes)</li>
      <li><?php bugfix(46094); ?> (and then from now on italic). (Hannes)</li>
      <li><?php bugfix(45071); ?> (Links to require/include(_once) not rendered). (Hannes)</li>
      <li>Fixed a bug where it was only possible to pass one parameter to phd.bat. (Kalle)</li>
      <li>Fixed a bug where it wasn't possible to have paths with spaces in when using phd.bat. (Kalle)</li>
      <li>Fixed xhtml validation issue for itemizedlist. (Christian)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.3.1.tgz">PhD 0.3.1</a> <span class="date">23. August 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added PDF output format. (Rudy)</li>
      <li>Added support for phpdoc:classref. (Hannes)</li>
      <li>Added support for phpdoc:varentry. (Hannes)</li>
      <li>Added support for the phpdoc howto. (Hannes)</li>
      <li>Renamed phpdoc:exception to phpdoc:exceptionref. (Hannes)</li>
      <li><?php bugfix(45627); ?> (Unix manpages using non-standard folder name). (Rudy)</li>
      <li><?php bugfix(45626); ?> (Unix manpages should be gzipped). (Rudy)</li>
      <li><?php bugfix(45618); ?> (Bad filenames in man pages). (Rudy)</li>
      <li>Fixed unclosed div element on set pages. (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.3.0.tgz">PhD 0.3.0</a> <span class="date">24. July 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added CHM output format. (Rudy)</li>
      <li>Added KDevelop (Index &amp; Table of contents) output theme. (Rudy)</li>
      <li>Added Man page output format. (Rudy)</li>
      <li>Added support for phpdoc:exception. (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.2.4.tgz">PhD 0.2.4</a> <span class="date">24. May 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li><?php bugfix(44906); ?> (Missing space after modifier in properties list). (Hannes)</li>
      <li><?php bugfix(44881); ?> (Class constants with $). (Hannes)</li>
      <li><?php bugfix(44786); ?> (Remove irrelevant version information). (Hannes)</li>
      <li><?php bugfix(44785); ?> (Separating &lt;title&gt; from &lt;note&gt;). (Hannes)</li>
      <li><?php bugfix(44776); ?> (Request for change in titles). (Hannes)</li>
      <li><?php bugfix(44750); ?> (Request for generic phd logger). (Hannes)</li>
      <li><?php bugfix(44690); ?> (Please restore manual main page). (Hannes)</li>
      <li>Added French support (for autogenerated texts). (Yannick)</li>
      <li>Added Danish support (for autogenerated texts). (Kalle Sommer)</li>
      <li>Added Swedish support (for autogenerated texts). (Kalle Sommer)</li>
      <li>Added experimental option (-c/--color) for color in verbose output. (Gwynne)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.2.3.tgz">PhD 0.2.3</a> <span class="date">31. March 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added initial hCalendar support for &lt;author&gt;. (Hannes)</li>
      <li>Added initial eRDF support for &lt;refentry&gt;. (Hannes)</li>
      <li>Added support for &lt;footnote&gt; and &lt;footnoteref&gt;. (Gwynne, Hannes)</li>
      <li>Added anchor generation for various elements. (Hannes)</li>
      <li>Added option (-s/--skip) to skip rendering of chunks. (Hannes)</li>
      <li>Added option (-o/--output) to specify output directory (FR<?php bugl(43193) ?>). (Richard)</li>
      <li>Added support for &lt;sect4&gt; titles . (Gwynne)</li>
      <li>Added an 'infdec' role to &lt;literal&gt; in XHTML. (Gwynne)</li>
      <li>Fixed couple of typos in PhD info messages. (Richard Q.)</li>
      <li>Reformatted package.xml for readability and consistency. (Gwynne)</li>
      <li>Merged README.RENDERER into README and updated README with current information. (Gwynne)</li>
      <li>Gwynne is a developer of PhD, unfortunately. (Gwynne)</li>
      <li>Made 'Example #' text localizeable. (Gwynne)</li>
      <li>Added Russion support (for autogenerated texts). (Tony)</li>
      <li>Updated translations:
        <ul>
          <li>Japanese (Masahiro)</li>
          <li>Brazilian Portuguese (Felipe)</li>
          <li>German (Mark)</li>
        </ul>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.2.2.tgz">PhD 0.2.2</a> <span class="date">30. January 2008</span><!-- {{{ -->
    <ul class="fixes">
      <li>Removed support for phnotify. (Hannes)</li>
      <li>Added index caching. (Edward Z.)</li>
      <li>Added option (-l/--list) to list the supported formats/themes.(Hannes)</li>
      <li>Added support for linkend in fieldsynopsis varnames. (Hannes)</li>
      <li>Added autogenerated "Edited by" text for &lt;editor&gt;. (Hannes)</li>
      <li>Added autogenerated "by" text for the first &lt;author&gt; element in &lt;authorgroup&gt;. (Hannes)</li>
      <li>Added missing closing "}" for classsynopsis. (Hannes)</li>
      <li>Fixed E_NOTICE on empty references. (Hannes)</li>
      <li>Fixed weird error message when no arguments are given, reported by Tony. (Hannes)</li>
      <li>Fixed typos in the argument descriptions (--help), reported by Tony. (Hannes)</li>
      <li><?php bugfix(43972); ?> (PhD doesn't warn on missing partial IDs). (Hannes)</li>
      <li><?php bugfix(43904); ?> (PhD doesn't detect &lt;section&gt; without xml:id). (Hannes)</li>
      <li><?php bugfix(43489); ?> (Class synopsis return types are not links). (Hannes)</li>
      <li><?php bugfix(43440); ?> (Wrong encoding on some parts of the page). (Hannes)</li>
      <li><?php bugfix(43428); ?> (Empty TOC). (Hannes)</li>
      <li><?php bugfix(43421); ?> (All intra-document hyperlinks broken in the Single HTML file form of manual). (Hannes)</li>
      <li><?php bugfix(43416); ?> (Function links do not render as links). (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
  <a href="/get/PhD-0.2.1.tgz">PhD 0.2.1</a> <span class="date">12. December 2007</span><!-- {{{ -->
    <ul class="fixes">
      <li>Multiple &lt;term&gt;s should be line seperated. (Hannes)</li>
      <li>Fixed autogenerated links to methods. (Edward Z.)</li>
      <li>Compressed methodnames in classsynopsis. (Edward Z.)</li>
      <li>Added HTML headers for the bightml theme. (Hannes)</li>
      <li>Removed warnings about missing translation files. (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.2.0.tgz">PhD 0.2.0</a> <span class="date">8. November 2007</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added partial rendering. (Hannes)</li>
      <li>Added various verbosity levels. (Hannes)</li>
      <li>Added getopt() parsing for few configuration options. (Hannes)</li>
      <li>Added support for errorcode, symbol and superscript elements (used by few translations). (Hannes)</li>
      <li>Suppressed the contrib element. (Hannes)</li>
      <li><?php bugfix(43192); ?> (Chunked HTML output difficult to use without TOC). (Edward Z. Yang)</li>
      <li><?php bugfix(43191); ?> (build.php fails to included necessary theme dependencies). (Edward Z. Yang, Richard Q)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.1.0.tgz">PhD 0.1.0</a> <span class="date">20. October 2007</span><!-- {{{ -->
    <ul class="fixes">
      <li>Added example numbering. (Hannes)</li>
      <li>Improved support for modifiers in fieldsynopsis. (Hannes)</li>
      <li>Remove () from refname when looking for version info, reported by Paul Reinheimer. (Hannes)</li>
      <li>Print notes inline with its title, reported by Philip Olson. (Hannes)</li>
      <li>Check if we have an open "{" before we print "}". (Hannes)</li>
      <li>Escape the version info. (Richard Q.)</li>
      <li>Fixed variablelist titles. (Hannes)</li>
      <li>Fixed table info titles. (Hannes)</li>
      <li>Fixed empty table cells, reported by Mark Wiesemann. (Hannes)</li>
      <li>Fixed table title markup, reported by Richard Q. (Hannes)</li>
      <li>Fixed non-closing b element for empty &lt;title /&gt;s, reported by Joshua Thompson and Philip Olson. (Hannes)</li>
      <li><?php bugfix(43013); ?> (Description rather then function name for right arrows on extension pages). (Richard Q.)</li>
      <li><?php bugfix(42906); ?> (docs.php.net bold instead of links). (Hannes)</li>
      <li><?php bugfix(42860); ?> (cannot render &lt;orgname&gt; element). (Hannes)</li>
      <li><?php bugfix(42845); ?> (Copyright page has no TOC). (Hannes)</li>
      <li>Language support (for autogenerated texts):
        <ul>
          <li>Bulgarian (by Kouber Saparev)</li>
          <li>Czech (by Jakub Vrana)</li>
          <li>German (by Oliver Albers)</li>
          <li>Italian (by Marco Cucinato)</li>
          <li>Japanese (by TAKAGI Masahiro)</li>
          <li>Polish (by Jaroslaw Glowacki)</li>
          <li>Brazilian Portuguese (by Diego Feitosa)</li>
          <li>Romanian (by Simion Onea)</li>
        </ul>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.1RC1.tgz">PhD 0.1RC1</a> <span class="date">1 October 2007</span>
    <ul class="fixes">
      <li>Initial release</li>
    </ul>
  </li>
</ul>


<?php
require '../include/init.inc.php';

// Changelog helpers
function bugfix($number)
{
    echo 'Fixed bug ';
    bugl($number);
}

function bugl($number)
{
    echo "<a href=\"http://bugs.php.net/$number\">#$number</a>";
}

site_header();
?>

<p>For PhD documentation see the <a href="/phd/docs/">PhD: The definitive guide to PHP's DocBook Rendering System</a> section.</p>
<p>PhD releases:</p>

<ul id="releases">

  <li>
    <a href="/get/PhD-1.1.9.tgz">PhD 1.1.9</a> <span class="date">6. March 2014</span><!-- {{{ -->
    <ul>
      <li>
        <a href="/get/PhD_Generic-1.1.9.tgz">PhD_Generic 1.1.9</a> <span class="date">6. March 2014</span>
        <ul>
          <li>Removed &lt;em&gt; wrapper around parameters. (Levi)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.9.tgz">PhD_PHP 1.1.9</a> <span class="date">6. March 2014</span>
        <ul>
          <li><?php bugfix(66644) ?> - Remove ()'s when rendering methodname in constructorsynopsis/destructorsynopsis (Peter)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.9.tgz">PhD_PEAR 1.1.9</a> <span class="date">6. March 2014</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.8.tgz">PhD 1.1.8</a> <span class="date">21. January 2014</span><!-- {{{ -->
    <ul>
      <li><?php bugfix(66400) ?> - Class synopsis missing space between type and method name (Peter)</li>
      <li>
        <a href="/get/PhD_Generic-1.1.8.tgz">PhD_Generic 1.1.8</a> <span class="date">21. January 2014</span>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.8.tgz">PhD_PHP 1.1.8</a> <span class="date">21. January 2014</span>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.8.tgz">PhD_PEAR 1.1.8</a> <span class="date">21. January 2014</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.7.tgz">PhD 1.1.7</a> <span class="date">1. January 2014</span><!-- {{{ -->
    <ul>
      <li><?php bugfix(64850) ?> - move placement of space between &lt;type&gt; and &lt;methodname&gt;. (Peter)</li>
      <li><?php bugfix(66316) ?> - HTML is malformed. (Peter)</li>
      <li>Fixed bug PhD generates garbled chm on PHP 5.4.0 or later. (Yoshinari)</li>
      <li>Change the unknown version reference to Git, not SVN. (Adam)</li>
      <li>
        <a href="/get/PhD_Generic-1.1.7.tgz">PhD_Generic 1.1.7</a> <span class="date">1. January 2014</span>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.7.tgz">PhD_PHP 1.1.7</a> <span class="date">1. January 2014</span>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.7.tgz">PhD_PEAR 1.1.7</a> <span class="date">1. January 2014</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.6.tgz">PhD 1.1.6</a> <span class="date">16. June 2012</span><!-- {{{ -->
    <ul>
      <li>Fixed indexing of content with markup. (Hannes)</li>
      <li>Added support for generate-changelog-for, extension-membership and related phpdoc PI. (Hannes)</li>
      <li>
        <a href="/get/PhD_Generic-1.1.6.tgz">PhD_Generic 1.1.6</a> <span class="date">16. June 2012</span>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.6.tgz">PhD_PHP 1.1.6</a> <span class="date">16. June 2012</span>
        <ul>
            <li>Added phd:args support for &lt;function;&gt; and &lt;methodname&gt;. (Hannes)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.6.tgz">PhD_PEAR 1.1.6</a> <span class="date">16. June 2012</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.5.tgz">PhD 1.1.5</a> <span class="date">7. June 2012</span><!-- {{{ -->
    <ul>
        <li>Show individual package version in --version. (Hannes)</li>
        <li><?php bugfix(54217) ?> - Warn about nonexisting parameters. (Moacir)</li>
        <li><?php bugfix(50725) ?> - Generate nav links at top of function index (Peter Cowburn)</li>
        <li><?php bugfix(47392) ?> - Option to specify filename for bightmls. (Hannes)</li>
        <a href="/get/PhD_Generic-1.1.5.tgz">PhD_Generic 1.1.5</a> <span class="date">7. June 2012</span>
        <ul>
        <li><?php bugfix(46772) ?> - Add class reference pages to the man files. (Hannes)</li>
        <li><?php bugfix(47650) ?> - Overwrite the TOC on changes. (Hannes)</li>
        <li>Fix invalid ID on multiple unknown refsect roles. (Hannes)</li>
        <li>Added support for "soft-deprecation-notice" attribute on refsynopsisdiv to collect alternate suggestions into the $setup phpweb array. (Hannes)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.5.tgz">PhD_PHP 1.1.5</a> <span class="date">7. June 2012</span>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.5.tgz">PhD_PEAR 1.1.5</a> <span class="date">7. June 2012</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.4.tgz">PhD 1.1.4</a> <span class="date">6. April 2012</span><!-- {{{ -->
    <ul>
      <li>
        <a href="/get/PhD_Generic-1.1.4.tgz">PhD_Generic 1.1.4</a> <span class="date">6. April 2012</span>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.4.tgz">PhD_PHP 1.1.4</a> <span class="date">6. April 2012</span>
        <ul>
          <li>Link callable types (Jakub)</li>
          <li>Link langauge constructs from &lt;function&gt; (Jakub)</li>
          <li>Show "next" link on the frontpage (Moacir)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.4.tgz">PhD_PEAR 1.1.4</a> <span class="date">6. April 2012</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.3.tgz">PhD 1.1.3</a> <span class="date">1. March 2012</span><!-- {{{ -->
    <ul>
      <li>Removed redundant align and valign attributes on table-related tags (Alexey Borzov)</li>
      <li>
        <a href="/get/PhD_Generic-1.1.3.tgz">PhD_Generic 1.1.3</a> <span class="date">1. March 2012</span>
        <ul>
          <li>Allow link to methodsynopsis (Jakub Vrana)</li>
          <li>Got rid of presentational tags: &lt;b&gt;, &lt;i&gt;, &lt;tt&gt; changed to &lt;strong&gt;, &lt;em&gt;, &lt;code&gt; (Alexey Borzov)</li>
          <li>Make presentational attributes on table related-tags (align, valign, width) output as inline styles (Alexey Borzov)</li>
          <li>Fixed ID generation for refsections (Alexey Borzov)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.3.tgz">PhD_PHP 1.1.3</a> <span class="date">1. March 2012</span>
        <ul>
          <li>Changed &lt;b&gt; tag to &lt;strong&gt; (Alexey Borzov)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.3.tgz">PhD_PEAR 1.1.3</a> <span class="date">1. March 2012</span>
        <ul>
          <li>Fixed #54208 - no attributes for table-related tags (Alexey Borzov)</li>
          <li>Allow linking to refsections (Alexey Borzov)</li>
          <li>Package now generates (almost) valid HTML5:<ul>
            <li>Got rid of presentational tags: &lt;b&gt;, &lt;i&gt;, &lt;tt&gt; changed to &lt;strong&gt;, &lt;em&gt;, &lt;code&gt; (Alexey Borzov)</li>
            <li>&lt;p&gt; tags are now properly closed before block level tags and reopened after them if needed (Alexey Borzov)</li>
          </ul></li>
        </ul>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.2.tgz">PhD 1.1.2</a> <span class="date">18. December 2011</span><!-- {{{ -->
    <ul>
      <li><?php bugfix(51853); ?> - Added phpdoc PI handler to handle manually added version information (Hannes)</li>
      <li><?php bugfix(49927); ?> - Added the possiblity of adding version information for classes</li>
      <li>
        <a href="/get/PhD_Generic-1.1.2.tgz">PhD_Generic 1.1.2</a> <span class="date">18. December 2011</span>
        <ul>
          <li>Added support for authorinitials, printhistory, revhistory, revision and revremark (Hannes)</li>
          <li>Added generate-index-for=(refentry,function,examples) support for the phpdoc PI handler</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.2.tgz">PhD_PHP 1.1.2</a> <span class="date">18. June 2011</span>
        <ul>
          <li>Use transliteration for the Windows CHM TOC and Index - Romanian only (Richard Q)</li>
          <li>Allow Windows CHM files to use the url() loaded content defined in the CSS files (Richard Q)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.2.tgz">PhD_PEAR 1.1.2</a> <span class="date">18. June 2011</span>
        <ul>
          <li>Use transliteration for the Windows CHM TOC and Index - Romanian only (Richard Q)</li>
        </ul>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.1.tgz">PhD 1.1.1</a> <span class="date">21. June 2011</span><!-- {{{ -->
    <ul>
      <li>Improved the performance of the indexer by ~ 75%. (Hannes)</li>
      <li>Added --quit option to quit after processing command line params. Useful when used with --saveconfig to just save the config (Richard Quadling)</li>
      <li>Output directory can now be nested (for example /rendering/PHP/en) (Richard Quadling)</li>
      <li>New translations:
        <ul>
          <li>Spanish (Pablo Bangueses)</li>
          <li>Serbian (Nikola Smolenski)</li>
        </ul>
      </li>
      <li>Added --packagedir option to use external packages. (Moacir)</li>
      <li>Added Format::getDebugTree() method to allow the current location in the tree to be reportable. (Richard Quadling)</li>
      <li><?php bugfix(52664); ?> - "Missing" example#1. (Hannes)</li>
      <li>
        <a href="/get/PhD_Generic-1.1.1.tgz">PhD_Generic 1.1.1</a> <span class="date">21. June 2011</span>
        <ul>
          <li><?php bugfix(54705); ?> - Tables in manpages not visible. (Hannes)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.1.tgz">PhD_PHP 1.1.1</a> <span class="date">21. June 2011</span>
        <ul>
          <li>Added support for local CSS files (Richard Quadling)</li>
          <li><?php bugfix(54436); ?> - gzip issues with -P PHP -f manpage (Moacir)</li>
          <li><?php bugfix(53536); ?> - Small display bug in attribute description for mysqli-&gt;insert_id. (Hannes)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_IDE-1.1.1.tgz">PhD_IDE 1.1.1</a> <span class="date">21. June 2011</span>
        <ul>
          <li>Added first version of PHPStub package format (Alexey Shein)</li>
          <li>Added changelog information (Philip)</li>
          <li>Added the description of parameters (Moacir)</li>
        </ul>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.1.0.tgz">PhD 1.1.0</a> <span class="date">08. March 2011</span><!-- {{{ -->
    <ul>
      <li>Added support for package cli options (Moacir)</li>
      <li>Do not disable color configuration settings when loading from phd.config.php on Windows when saved setting is enabled (Richard Quadling)</li>
      <li>Using saveconfig once no longer saves the config on every call to render (Richard Quadling)</li>
      <li>Restoring a saved config now correctly sets the error reporting level to the restored verbosity (Richard Quadling)</li>
      <li>
        <a href="/get/PhD_Generic-1.1.0.tgz">PhD_Generic 1.1.0</a> <span class="date">08. March 2011</span>
        <ul>
          <li>Allow xml:id on &lt;table&gt; (Richard Quadling)</li>
          <li>Add class="note" to &lt;note&gt;s generated &lt;blockquote&gt; (Hannes)</li>
          <li>Generate an ID for &lt;refsect1&gt; (Hannes)</li>
          <li>Generate IDS for all &lt;example&gt;s (Hannes)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.1.0.tgz">PhD_PHP 1.1.0</a> <span class="date">08. March 2011</span>
        <ul>
          <li>New output format Epub (Moacir)</li>
          <li>Prepare CHM rendering to use the new CSS rules (Richard Quadling)</li>
          <li>Display a message when loading an external stylesheet (Richard Quadling)</li>
          <li>Incorporate stylesheet names supplied at the command line into the CHM file (Richard Quadling)</li>
          <li>New output format EnhancedCHM - the same as CHM but with the User Notes (Requires ext/bz2) (Richard Quadling)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.1.0.tgz">PhD_PEAR 1.1.0</a> <span class="date">08. March 2011</span>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.0.1.tgz">PhD 1.0.1</a> <span class="date">10. August 2010</span><!-- {{{ -->
    <ul>
      <li>Now searches the include-path for custom package classes in the \phpdotnet\phd namespace (Paul M Jones)</li>
      <li>Added --ext command line option to specify filename extension (Paul M Jones)</li>
      <li>Added --saveconfig option to generate a config file to load (Hannes)</li>
      <li>Added 'package_dirs' config option to specify package directories to autoload from (Hannes)</li>
      <li>Corrected grammar in Danish translation + fixed encoding (Daniel Egeberg)</li>
      <li>Allow colored output on Windows, but not by default (Richard Quadling)</li>
      <li>Allow &lt;void/&gt; as return type for methodsynopsis tags rather than &lt;type&gt;void&lt;/type&gt; (Richard Quadling)</li>
      <li>Added support for &lt;sidebar&gt; (Richard Quadling)</li>
      <li>Now builds toc/* by default for the PHP package, and added --notoc option to use cached version (Philip)</li>
      <li>Changed the VERBOSE_DEFAULT error level to exclude VERBOSE_TOC_WRITING messages (Philip)</li>
      <li>Fixed encoding problems with the function iconv() in the CHM format (Moacir)</li>
      <li>Fixed the --lang option that was creating an infinite recursion (Moacir)</li>
      <li>
        <a href="/get/PhD_Generic-1.0.1.tgz">PhD_Generic 1.0.1</a> <span class="date">10. August 2010</span>
        <ul>
          <li>Several formatting fixes for the unix manual pages (Hannes)</li>
          <li>Added Generic Unix Manual pages output format (Hannes)</li>
          <li>Added format_option to bold options (Philip)</li>
          <li><?php bugfix(50666); ?> - Missing entries in table (man pages) (Hannes)</li>
          <li><?php bugfix(51301); ?> - Wrong escape sequence (man pages) (Hannes)</li>
          <li><?php bugfix(51346); ?> - Extra whitespace when using &lt;type&gt; and plural (Daniel Egeberg)</li>
          <li><?php bugfix(51514); ?> - Added tr, th and td mappings (Patch provided by Paul M Jones) (Richard Quadling)</li>
          <li><?php bugfix(51833); ?> - Multiple paragraphs in notes render incorrectly (Daniel Egeberg)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PHP-1.0.1.tgz">PhD_PHP 1.0.1</a> <span class="date">10. August 2010</span>
        <ul>
          <li>Added the Persian language for CHM builds (Philip)</li>
          <li>Made all &gt;refentry&lt; create a new Unix Manual Page (Hannes)</li>
          <li>Create Unix Manual Page for the predefined variables too (Hannes)</li>
          <li><?php bugfix(51750); ?> - Add ()'s when rendering methodname elements (Hannes)</li>
        </ul>
      </li>
      <li>
        <a href="/get/PhD_PEAR-1.0.1.tgz">PhD_PEAR 1.0.1</a> <span class="date">10. August 2010</span>
        <ul>
          <li>Added the Persian language for CHM builds (Philip)</li>
        </ul>
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-1.0.0.tgz">PhD 1.0.0</a> <span class="date">11. March 2010</span><!-- {{{ -->
    <ul>
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
        <ul>
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
    <ul>
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
        <ul>
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
        <ul>
          <li>Added new output format TocFeed (Moacir)</li>
        </ul><!-- }}} -->
      </li>
      <li>
        <a href="/get/PhD_PEAR-0.9.1.tgz">PhD_PEAR 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul>
          <li>Added Next/Prev and Image Zoom buttons to CHM build (Richard Quadling)</li>
          <li>Add title attribute to anchor tags so address can be seen in CHM files for external links (Richard Quadling)</li>
          <li>Implemented PEAR request #2390: RSS feeds for PEAR Dcumentation Index (Christian)</li>
        </ul><!-- }}} -->
      </li>
      <li>
        <a href="/get/PhD_GeSHi-0.9.1.tgz">PhD_GeSHi 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul>
          <li>Initial Release (Christian)</li>
        </ul><!-- }}} -->
      </li>
      <li>
        <a href="/get/PhD_GeSHi11x-0.9.1.tgz">PhD_GeSHi11x 0.9.1</a> <span class="date">21. December 2009</span><!-- {{{ -->
        <ul>
          <li>Initial Release (Christian)</li>
        </ul><!-- }}} -->
      </li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.9.0.tgz">PhD 0.9.0</a> <span class="date">09. September 2009</span><!-- {{{ -->
    <ul>
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
    <ul>
      <li>Add support for external troff highlighter in man pages (Christian)</li>
      <li>Add title attribute to anchor tags so address can be seen in CHM files for external links (Richard Q.)</li>
      <li>CVS-&gt;SVN langs migration (Philip)</li>
      <li><?php bugfix(49006); ?> (The manpage format groups function arguments incorrectly) (Moacir)</li>
      <li><?php bugfix(49005); ?> (Reference sign in function prototypes in the manpage rendering) (Moacir)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.7.tgz">PhD 0.4.7</a> <span class="date">07. May 2009</span><!-- {{{ -->
    <ul>
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
    <ul>
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
    <ul>
      <li>Changed copyright year to 2007-2009. (Christian)</li>
      <li>Fixed PEAR chm manual title for french. (Laurent)</li>
      <li>Fixed PEAR chm navbar alignment. (Laurent)</li>
      <li><?php bugfix(47408); ?> (Same image directory used for each theme). (Christian)</li>
      <li><?php bugfix(47413); ?> (PEAR themes don't work when rendered together at once). (Christian)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.4.4.tgz">PhD 0.4.4</a> <span class="date">16. February 2009</span><!-- {{{ -->
    <ul>
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
    <ul>
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
    <ul>
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
    <ul>
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
    <ul>
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
    <ul>
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
    <ul>
      <li>Added CHM output format. (Rudy)</li>
      <li>Added KDevelop (Index &amp; Table of contents) output theme. (Rudy)</li>
      <li>Added Man page output format. (Rudy)</li>
      <li>Added support for phpdoc:exception. (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.2.4.tgz">PhD 0.2.4</a> <span class="date">24. May 2008</span><!-- {{{ -->
    <ul>
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
    <ul>
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
    <ul>
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
    <ul>
      <li>Multiple &lt;term&gt;s should be line seperated. (Hannes)</li>
      <li>Fixed autogenerated links to methods. (Edward Z.)</li>
      <li>Compressed methodnames in classsynopsis. (Edward Z.)</li>
      <li>Added HTML headers for the bightml theme. (Hannes)</li>
      <li>Removed warnings about missing translation files. (Hannes)</li>
    </ul><!-- }}} -->
  </li>
  <li>
    <a href="/get/PhD-0.2.0.tgz">PhD 0.2.0</a> <span class="date">8. November 2007</span><!-- {{{ -->
    <ul>
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
    <ul>
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
    <ul>
      <li>Initial release</li>
    </ul>
  </li>
</ul>

<?php
site_footer();
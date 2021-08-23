<?php
/*
+----------------------------------------------------------------------+
| PHP Documentation Tools Site Source Code                             |
+----------------------------------------------------------------------+
| Copyright (c) 1997-2014 The PHP Group                                |
+----------------------------------------------------------------------+
| This source file is subject to version 3.0 of the PHP license,       |
| that is bundled with this package in the file LICENSE, and is        |
| available through the world-wide-web at the following url:           |
| http://www.php.net/license/3_0.txt.                                  |
| If you did not receive a copy of the PHP license and are unable to   |
| obtain it through the world-wide-web, please send a note to          |
| license@php.net so we can mail you a copy immediately.               |
+----------------------------------------------------------------------+
| Original Authors: Thomas Schöfbeck <tom at php dot net>              |
|                   Gabor Hojtsy    <goba at php dot net>              |
|                   Mark Kronsbein    <mk at php dot net>              |
|                   Jan Fabry     <cheezy at php dot net>              |
| SQLite version Authors:                                              |
|                   Nilgün Belma Bugüner <nilgun at php dot net>       |
|                   Mehdi Achour          <didou at php dot net>       |
|                   Maciej Sobaczewski    <sobak at php dot net>       |
+----------------------------------------------------------------------+
*/
error_reporting(E_ALL);
set_time_limit(0);

// include required files
include '../include/init.inc.php';
include '../include/lib_proj_lang.inc.php';

$DOCS = GIT_DIR;

// Test the languages:
$LANGS = array_keys($LANGUAGES);
$langc = count($LANGS);
for ($i = 0; $i < $langc; $i++) {
    if (!is_dir($DOCS . $LANGS[$i])) {
        echo "Error: the \"{$LANGS[$i]}\" lang doesn't exist in dir {$DOCS}, skipping..\n";
        unset($LANGS[$i]);
    }
}
if (count($LANGS) == 0) {
    echo "Error: No language to revcheck, exiting.\n";
    exit;
}

$CREATE = <<<SQL

CREATE TABLE descriptions (
  lang TEXT,
  intro TEXT,
  UNIQUE (lang)
);

CREATE TABLE translators (
  lang TEXT,
  nick TEXT,
  name TEXT,
  mail TEXT,
   vcs TEXT,
  files_uptodate INT,
  files_outdated INT,
  files_wip INT,
  files_sum INT,
  files_other INT,
  UNIQUE (lang, nick)
);

CREATE TABLE translated (
  id INT,
  lang TEXT,
  name TEXT,
  revision TEXT,
  size INT,
  maintainer TEXT,
  status TEXT,
  syncStatus TEXT,
  UNIQUE(lang, id, name)
);

CREATE INDEX translated_1 ON translated (lang, id, name);

CREATE TABLE dirs (
  id INT,
  path TEXT,
  UNIQUE (path)
);

CREATE INDEX dirs_1 ON dirs (path);

CREATE TABLE enfiles (
  id INT,
  name TEXT,
  revision TEXT,
  size INT,
  UNIQUE(id, name)
);

CREATE INDEX enfiles_1 ON enfiles (id, name);

CREATE TABLE Untranslated (
  id INT,
  lang TEXT,
  name TEXT,
  size INT,
  UNIQUE(lang, id, name)
);

CREATE INDEX Untrans_1 ON Untranslated (lang, id, name);

CREATE TABLE notinen (
  lang TEXT,
  path TEXT,
  name TEXT,
  size INT,
  UNIQUE(lang, path, name)
);

CREATE INDEX notinen_1 ON notinen (lang, path, name);

CREATE TABLE wip (
  id INT,
  lang TEXT,
  name TEXT,
  size INT,
  person TEXT
);

SQL;

$SQL_BUFF = "";

$enFiles = populateFileTree( 'en' );
captureGitValues( $gitData );
foreach ($LANGS as $lang){
    $trFiles[$lang] = populateFileTree( $lang );
}

class FileStatusInfo
{
    public $path;
    public $name;
    public $size;
    public $hash;
    public $syncStatus;
    public $maintainer;
    public $completion;
    public $credits;

    public function getKey()
    {
        return trim( $this->path . '/' . $this->name , '/' );
    }
}

class FileStatusEnum
{
    const Untranslated      = 'Untranslated';
    const RevTagProblem     = 'RevTagProblem';
    const TranslatedWip     = 'TranslatedWip';
    const TranslatedOk      = 'TranslatedOk';
    const TranslatedOld     = 'TranslatedOld';
    const TranslatedCritial = 'TranslatedCritial';
    const NotInEnTree       = 'NotInEnTree';
}

class TranslatorInfo
{
    public $name;
    public $email;
    public $nick;
    public $vcs;

    public $files_uptodate;
    public $files_outdated;
    public $files_wip;
    public $files_sum;
    public $files_other;

    public function __construct() {
        $this->files_uptodate = 0;
        $this->files_outdated = 0;
        $this->files_wip = 0;
        $this->files_sum = 0;
        $this->files_other = 0;
    }

    public static function getKey( $fileStatus ) {
        switch ( $fileStatus ) {
             case FileStatusEnum::RevTagProblem:
             case FileStatusEnum::TranslatedOld:
             case FileStatusEnum::TranslatedCritial:
             case FileStatusEnum::NotInEnTree:
                return "files_outdated";
                break;
            case FileStatusEnum::TranslatedWip:
                return "files_wip";
                break;
            case FileStatusEnum::TranslatedOk:
                return "files_uptodate";
                break;
            default:
                return "files_other";
        }
    }
}

// Get a multidimensional array with tag attributes
function parse_attr_string ( $tags_attrs ) {
    $tag_attrs_processed = array();

    foreach($tags_attrs as $attrib_list) {
        preg_match_all("!(.+)=\\s*([\"'])\\s*(.+)\\2!U", $attrib_list, $attribs);

        $attrib_array = array();
        foreach ($attribs[1] as $num => $attrname) {
            $attrib_array[trim($attrname)] = trim($attribs[3][$num]);
        }

        $tag_attrs_processed[] = $attrib_array;
    }

    return $tag_attrs_processed;
}

function computeTranslatorStatus( $lang, $enFiles, $trFiles )
{
    global $SQL_BUFF, $DOCS, $LANGUAGES;
    $translation_xml = $DOCS . $lang . "/translation.xml";
    $charset  = 'utf-8';

    if (!file_exists($translation_xml)) {
        return [];
    }

    $txml = join("", file($translation_xml));
    $txml = preg_replace("/\\s+/", " ", $txml);

    $intro = "No intro available for the {$LANGUAGES[$lang]} translation of the manual.";
    if ( preg_match("!<intro>(.+)</intro>!s", $txml, $match) )
        $intro = SQLite3::escapeString(@iconv($charset, 'UTF-8//IGNORE', trim($match[1])));

    $SQL_BUFF .= "INSERT INTO descriptions VALUES ('$lang', '$intro');\n";

    $pattern = "!<person(.+)/\\s?>!U";
    preg_match_all($pattern, $txml, $matches);
    $translators = parse_attr_string($matches[1]);

    $translatorInfos = [];
    $unknownInfo = new TranslatorInfo();
    $unknownInfo->nick = "unknown";
    $translatorInfos["unknown"] = $unknownInfo;

    foreach ($translators as $key => $translator)
    {
        $info = new TranslatorInfo();
        $info->name = $translator["name"];
        $info->email = $translator["email"];
        $info->nick = $translator["nick"];
        $info->vcs = isset($translator["vcs"]) ? $translator["vcs"] : '';

        $translatorInfos[$info->nick] = $info;
    }

    foreach( $enFiles as $key => $enFile ) {
        $info_exists = false;
        if (array_key_exists($enFile->getKey(), $trFiles)) {
            $trFile = $trFiles[$enFile->getKey()];
            $statusKey = TranslatorInfo::getKey($trFile->syncStatus);
            if (array_key_exists($trFile->maintainer, $translatorInfos)) {
                $translatorInfos[$trFile->maintainer]->$statusKey++;
                $translatorInfos[$trFile->maintainer]->files_sum++;
                $info_exists = true;
            }
        }
        if (!$info_exists) {
            $translatorInfos["unknown"]->$statusKey++;
            $translatorInfos["unknown"]->files_sum++;
        }
    }
    foreach ($translatorInfos as $key => $person)
    {
        if ($person->nick != "unknown" )
        {
            $nick   = SQLite3::escapeString($person->nick);
            $name   = SQLite3::escapeString(@iconv($charset, 'UTF-8//IGNORE', $person->name));
            $email  = SQLite3::escapeString($person->email);
            $vcs    = SQLite3::escapeString($person->vcs);

            $SQL_BUFF .= "INSERT INTO translators VALUES ('$lang',
            '$nick', '$name', '$email', '$vcs', $person->files_uptodate,
            $person->files_outdated, $person->files_wip,
            $person->files_sum, $person->files_other);\n";
        }
    }
}

function populateFileTree( $lang )
{
    global $DOCS;
    $dir = new \DirectoryIterator( $DOCS . $lang );
    if ( $dir === false )
    {
        print "$lang is not a directory.\n";
        exit;
    }
    $cwd = getcwd();
    $ret = array();
    chdir( $DOCS . $lang );
    populateFileTreeRecurse( $lang , "." , $ret );
    chdir( $cwd );
    return $ret;
}

function populateFileTreeRecurse( $lang , $path , & $output )
{
    global $DOCS, $SQL_BUFF;
    $dir = new DirectoryIterator( $path );
    if ( $dir === false )
    {
        print "$path is not a directory.\n";
        exit;
    }
    $todoPaths = [];
    $trimPath = ltrim( $path , "./");
    foreach( $dir as $entry )
    {
        $filename = $entry->getFilename();
        if ( $filename[0] == '.' )
            continue;
        if ( substr( $filename , 0 , 9 ) == "entities." )
            continue;
        if ( $entry->isDir() )
        {
            $todoPaths[] = $path . '/' . $entry->getFilename();
            continue;
        }
        if ( $entry->isFile() )
        {
            $ignoredFileNames = [
                'README.md',
                'translation.xml',
                'readme.first',
                'license.xml',
                'extensions.xml',
                'versions.xml',
                'book.developer.xml',
                'contributors.ent',
                'contributors.xml',
                'README',
                'DO_NOT_TRANSLATE',
                'rsusi.txt',
                'missing-ids.xml',
            ];

            $ignoredDirectories = [
                'chmonly',
            ];

            if(
                in_array($trimPath, $ignoredDirectories, true)
                || in_array($filename, $ignoredFileNames, true)
                || (strpos($filename, 'entities.') === 0)
                || !in_array(substr($filename, -3), ['xml','ent'], true)
                || (substr($filename, -13) === 'PHPEditBackup')
                || ($trimPath === 'appendices' && (in_array($filename, ['reserved.constants.xml', 'extensions.xml'], true)))
            ) continue;

            $file = new FileStatusInfo;
            $file->path = $trimPath;
            $file->name = $filename;
            $file->size = filesize( $path . '/' . $filename );
            $file->syncStatus = null;
            if ( $lang != 'en' )
            {
                parseRevisionTag( $entry->getPathname() , $file );
                $path_en = $DOCS . 'en/' . $trimPath . '/' . $filename;
                if( !is_file($path_en) ) //notinen
                {
                    $filesize = $file->size < 1024 ? 1 : floor( $file->size / 1024 );
                    $SQL_BUFF .= "INSERT INTO notinen VALUES ('$lang', '$trimPath', '$filename', $filesize);\n";
                 } else {
                    $output[ $file->getKey() ] = $file;
                 }
             } else {
                 $output[ $file->getKey() ] = $file;
             }
         }
    }
    sort( $todoPaths );
    foreach( $todoPaths as $path )
        populateFileTreeRecurse( $lang , $path , $output );
}

function parseRevisionTag( $filename , FileStatusInfo $file )
{
    $fp = fopen( $filename , "r" );
    $contents = fread( $fp , 1024 );
    fclose( $fp );

    // No match before the preg
    $match = array ();

    $regex = "'<!--\s*EN-Revision:\s*(.+)\s*Maintainer:\s*(.+)\s*Status:\s*(.+)\s*-->'U";
    if (preg_match ($regex , $contents , $match )) {
        $file->hash = trim( $match[1] );
        $file->maintainer = trim( $match[2] );
        $file->completion = trim( $match[3] );
    }
    if ( $file->hash == null or strlen( $file->hash ) != 40 or
         $file->maintainer == null or
         $file->completion == null )
         $file->syncStatus = FileStatusEnum::RevTagProblem;

    $regex = "/<!--\s*CREDITS:\s*(.+)\s*-->/U";
    $match = array();
    preg_match ( $regex , $contents , $match );
    if ( count( $match ) == 2 )
        $file->credits = str_replace( ' ' , '' , trim( $match[1] ) );
    else
        $file->credits = '';
}

function captureGitValues( & $output )
{
    global $DOCS;
    $cwd = getcwd();
    chdir( $DOCS . 'en' );
    $fp = popen( "git --no-pager log --name-only" , "r" );
    $hash = null;
    $date = null;
    $utct = new DateTimeZone( "UTC" );
    $skipThisCommit = false;
    while ( ( $line = fgets( $fp ) ) !== false )
    {
        if ( substr( $line , 0 , 7 ) == "commit " )
        {
            $hash = trim( substr( $line , 7 ) );
            continue;
        }
        if ( strpos( $line , 'Date:' ) === 0 )
        {
            $date = trim( substr( $line , 5 ) );
            continue;
        }
        if ( trim( $line ) == "" )
            continue;
        if ( substr( $line , 0 , 4 ) == '    ' )
        {
            if ( stristr( $line, '[skip-revcheck]' ) !== false )
            {
                $skipThisCommit = true;
            }
            continue;
        }
        if ( strpos( $line , ': ' ) > 0 )
            continue;
        $filename = trim( $line );
        if ( isset( $output[$filename] ) )
            continue;
        if ( $skipThisCommit )
            continue;
        $output[$filename]['hash'] = $hash;
    }
    pclose( $fp );
    chdir( $cwd );
}

/**
*   Script execution
**/

$time_start = microtime(true);

$path = null;
$id = 0;
asort( $enFiles );
foreach( $enFiles as $key => $en )
{
    if ( $path !== $en->path )
    {
        $id++;
        $path = $en->path;
        $path2 = $path == '' ? '/' : $path;
        $SQL_BUFF .= "INSERT INTO dirs VALUES ($id, '$path2');\n";
    }

    $size = $en->size < 1024 ? 1 : floor( $en->size / 1024 );
    $filename = $path . ($path == '' ? '' : '/') . $en->name;
    $en->hash = null;
    if ( isset( $gitData[ $filename ] ) )
    {
        $en->hash = $gitData[ $filename ]['hash'];
    }
    else
        print "Warn: No hash for en/$filename\n";

    $SQL_BUFF .= "INSERT INTO enfiles VALUES ($id, '$en->name', '$en->hash', $size);\n";

    foreach( $LANGS as $lang )
    {
        $trFile = isset( $trFiles[$lang][$filename] ) ? $trFiles[$lang][$filename] : null;
        if ( $trFile == null ) // Untranslated
        {
            $SQL_BUFF .= "INSERT INTO Untranslated VALUES ($id, '$lang',
            '$en->name', $size);\n";
        } else {
            if ( $en->hash == $trFile->hash ){
                $trFile->syncStatus = FileStatusEnum::TranslatedOk;
            } elseif ( strlen( $trFile->hash ) == 40 ) {
                $trFile->syncStatus = FileStatusEnum::TranslatedOld;
            }
            if ( $trFile->completion != null && $trFile->completion != "ready" )
                $trFile->syncStatus = FileStatusEnum::TranslatedWip;
            $SQL_BUFF .= "INSERT INTO translated VALUES ($id, '$lang',
            '$en->name', '$trFile->hash', $size, '$trFile->maintainer',
            '$trFile->completion', '$trFile->syncStatus');\n";

        }
    }
}

foreach( $LANGS as $lang ) {
    computeTranslatorStatus( $lang, $enFiles, $trFiles[$lang] );
}

$db_name = SQLITE_DIR . 'rev.php.sqlite';
$tmp_db = SQLITE_DIR . 'rev.php.tmp.sqlite';

// 1 - Drop the old database and create the new one
if (is_file($tmp_db)) {
    echo "Temporary database found: remove.\n";

    if (!@unlink($tmp_db)) {
        echo "Error: Can't remove temporary database\n";
        exit;
    }
}

// 2 - Create the new database
try {
    $db = new SQLite3($tmp_db);
    /* Didn't throw exception at some point? */
    if (!$db) {
        throw Exception("Cant open $tmp_db");
    }

} catch(Exception $e) {
    echo $e->getMessage();
    echo "Could not open $tmp_db";
    exit;
}

$db->exec($CREATE);

// 3 - Fill in the description table while cleaning the langs
// without revision.xml file
// 4 - Recurse in the manual seeking for files and fill $SQL_BUFF




// 5 - Query $SQL_BUFF and exit
$db->exec('BEGIN TRANSACTION');
$db->exec($SQL_BUFF);
$db->exec('COMMIT');
$db->close();

echo "Copying temporary database to final database\n";

copy($tmp_db, $db_name);
@unlink($tmp_db);

$time = microtime(true) - $time_start;

echo "Time of generation: $time s\n";
echo "End\n";

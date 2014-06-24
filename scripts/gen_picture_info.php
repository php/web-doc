<?php
$time_start = microtime(true);

include '../include/jpgraph/src/jpgraph.php';
include '../include/jpgraph/src/jpgraph_pie.php';
include '../include/jpgraph/src/jpgraph_pie3d.php';

include '../include/init.inc.php';
include '../include/lib_revcheck.inc.php';
include '../include/lib_proj_lang.inc.php';

$idx = new SQLite3(SQLITE_DIR . 'rev.php.sqlite');

$available_langs = revcheck_available_languages($idx);

$langs = array_keys($LANGUAGES);
foreach ($langs as $lang) {
    if (!in_array($lang, $available_langs)) {
        echo "Documentation for $lang language does not exist.\n";
    } else {
        generate_image($lang, $idx);
        echo "Generated images/revcheck/info_revcheck_php_$lang.png\n";
    }
}

$time = round(microtime(true) - $time_start, 3);
echo "Graphs generated in {$time}s\n";

function generate_image($lang, $idx) {
    global $LANGUAGES;

    $up_to_date = get_stats($idx, $lang, 'uptodate');
    $up_to_date = $up_to_date[0];
    //
    $critical = @get_stats($idx, $lang, 'critical');
    $critical = $critical[0];
    //
    $old = @get_stats($idx, $lang, 'old');
    $old = $old[0];
    //
    $missing = get_stats($idx, $lang, 'notrans');
    $missing = $missing[0];
    //
    $no_tag = @get_stats($idx, $lang, 'norev');
    $no_tag = $no_tag[0];

    $data = array(
        $up_to_date,
        $critical,
        $old,
        $missing,
        $no_tag
    );

    $percent = array();
    $total = array_sum($data); // Total ammount in EN manual (to calculate percentage values)
    $total_files_lang = $total - $missing; // Total ammount of files in translation

    foreach ($data as $value) {
        $percent[] = round($value * 100 / $total);
    }

    $legend = array($percent[0] . '%% up to date ('.$up_to_date.')', $percent[1] . '%% critical ('.$critical.')', $percent[2] . '%% old ('.$old.')', $percent[3] . '%% missing ('.$missing.')', $percent[4] . '%% without EN-Revision ('.$no_tag.')');
    $title = 'Details for '.$LANGUAGES[$lang].' PHP Manual';

    $graph = new PieGraph(530,300);
    $graph->SetShadow();
    
    $graph->title->Set($title);
    $graph->title->Align('left');
    $graph->title->SetFont(FF_FONT1,FS_BOLD);

    $graph->legend->Pos(0.02,0.18,"right","center");

    $graph->subtitle->Set('(Total: '.$total_files_lang.' files)');
    $graph->subtitle->Align('left');
    $graph->subtitle->SetColor('darkred');

    $t1 = new Text(date('m/d/Y')); 
    $t1->SetPos(522,294);
    $t1->SetFont(FF_FONT1,FS_NORMAL);
    $t1->Align("right", 'bottom'); 
    $t1->SetColor("black");
    $graph->AddText($t1);

    $p1 = new PiePlot3D($data);
    $p1->SetSliceColors(array("#68d888", "#ff6347", "#eee8aa", "#dcdcdc", "#f4a460"));
    if ($total_files_lang != $up_to_date) {
       $p1->ExplodeAll();
    }
    $p1->SetCenter(0.35,0.55);
    $p1->value->Show(false);

    $p1->SetLegends($legend);

    $graph->Add($p1);
    $graph->Stroke("../www/images/revcheck/info_revcheck_php_$lang.png");
}
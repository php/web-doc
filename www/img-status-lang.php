<?php

require_once __DIR__ . '/../include/jpgraph/src/jpgraph.php';
require_once __DIR__ . '/../include/jpgraph/src/jpgraph_pie.php';
require_once __DIR__ . '/../include/jpgraph/src/jpgraph_pie3d.php';

require_once __DIR__ . '/../include/init.inc.php';
require_once __DIR__ . '/../include/lib_revcheck.inc.php';
require_once __DIR__ . '/../include/lib_proj_lang.inc.php';

$idx = new SQLite3(SQLITE_DIR . 'rev.php.sqlite');

$available_langs = revcheck_available_languages($idx);

$langs = array_keys($LANGUAGES);
foreach ($langs as $lang) {
    if (!in_array($lang, $available_langs)) {
        die("Information for $lang language does not exist.");
    } else {
        generate_image($lang, $idx);
    }
}

function generate_image($lang, $idx) {
    global $LANGUAGES;

    $up_to_date = get_stats($idx, $lang, 'uptodate');
    $up_to_date = $up_to_date[0];
    //
    $outdated = @get_stats($idx, $lang, 'outdated');
    $outdated = $outdated[0];
    //
    $missing = get_stats($idx, $lang, 'notrans');
    $missing = $missing[0];
    //
    $no_tag = @get_stats($idx, $lang, 'norev');
    $no_tag = $no_tag[0];

    $data = array(
        $up_to_date,
        $outdated,
        $missing,
        $no_tag
    );

    $percent = array();
    $total = array_sum($data); // Total ammount in EN manual (to calculate percentage values)
    $total_files_lang = $total - $missing; // Total ammount of files in translation

    foreach ($data as $value) {
        $percent[] = round($value * 100 / $total);
    }

    $legend = array($percent[0] . '%% up to date ('.$up_to_date.')', $percent[1] . '%% outdated ('.$outdated.')', $percent[2] . '%% missing ('.$missing.')', $percent[3] . '%% without EN-Revision ('.$no_tag.')');
    $title = 'Details for '.$LANGUAGES[$lang].' PHP Manual';

    $graph = new PieGraph(680,300);
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
    $p1->SetSliceColors(array("#68d888", "#ff6347", "#dcdcdc", "#f4a460"));
    if ($total_files_lang != $up_to_date) {
       $p1->ExplodeAll();
    }
    $p1->SetCenter(0.35,0.55);
    $p1->value->Show(false);

    $p1->SetLegends($legend);

    $graph->Add($p1);
    $graph->Stroke();
}

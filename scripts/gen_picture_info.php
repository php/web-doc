<?php
include '../include/jpgraph/src/jpgraph.php';
include '../include/jpgraph/src/jpgraph_pie.php';
include '../include/jpgraph/src/jpgraph_pie3d.php';

include '../include/init.inc.php';
include '../include/lib_revcheck.inc.php';
include '../include/lib_proj_lang.inc.php';

$langs = array_keys($LANGUAGES);
foreach ($langs as $lang) {
    if ($lang === 'en') {
        continue;
    }
    if (!generate_image($lang)) {
        echo "Documentation for $lang language does not exist.\n";
    } else {
        echo "Generated images/revcheck/info_revcheck_php_" . $lang . ".png\n";
    }
}

function generate_image($lang) {
    global $LANGUAGES;

    $idx = sqlite_open(SQLITE_DIR . 'rev.php.sqlite');
    $Total_files = @get_nb_LANG_files($idx);
    if (!isset($Total_files[$lang]) ) {
        return FALSE;
    }

    $Total_files_lang = $Total_files[$lang];
    //
    $up_to_date = @get_nb_LANG_files_Translated($idx, $lang);
    $up_to_date = ($up_to_date['total'] == '') ? 0 : $up_to_date['total'];
    //
    $critical = @get_stats_critical($idx, $lang);
    $critical = $critical[0];
    //
    $old = @get_stats_old($idx, $lang);
    $old = $old[0];
    //
    $missing = sizeof(@get_missfiles($idx, $lang));
    //
    $no_tag = @get_stats_notag($idx, $lang);
    $no_tag = $no_tag[0];
    //

    $data = array($up_to_date,$critical,$old,$missing,$no_tag);
    $percent = array();
    $total = 0;
    $total = array_sum($data);

    foreach ($data as $value) {
        $percent[] = round($value * 100 / $total);
    }
    
    $noExplode = ($Total_files_lang == $up_to_date) ? 1 : 0;

    $legend = array($percent[0] . '%% up to date ('.$up_to_date.')', $percent[1] . '%% critical ('.$critical.')', $percent[2] . '%% old ('.$old.')', $percent[3] . '%% missing ('.$missing.')', $percent[4] . '%% without revtag ('.$no_tag.')');
    $title = 'Details for '.$LANGUAGES[$lang].' PHP Manual';

    $graph = new PieGraph(530,300);
    $graph->SetShadow();
    
    $graph->title->Set($title);
    $graph->title->Align('left');
    $graph->title->SetFont(FF_FONT1,FS_BOLD);

    $graph->legend->Pos(0.02,0.18,"right","center");

    $graph->subtitle->Set('(Total: '.$Total_files_lang.' files)');
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
    if ($noExplode != 1) {
       $p1->ExplodeAll();
    }
    $p1->SetCenter(0.35,0.55);
    $p1->value->Show(false);

    $p1->SetLegends($legend);

    $graph->Add($p1);
    $graph->Stroke('../www/images/revcheck/info_revcheck_php_' . $lang . '.png');


    return TRUE;
}

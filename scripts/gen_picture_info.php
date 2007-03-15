<?php
/* $Id$ */

include '../include/jpgraph/jpgraph.php';
include '../include/jpgraph/jpgraph_pie.php';
include '../include/jpgraph/jpgraph_pie3d.php';

$inCli = true;
include '../include/init.inc.php';
include '../include/lib_revcheck.inc.php';

$self = array_shift($argv);

$TYPE = array_shift($argv); // Type of documentation
$LANGS = $argv;

// generate all languages
if (count($LANGS) == 0) {
    include "../include/lib_proj_lang.inc.php";
    $LANGS = array_keys($LANGUAGES);
}


foreach ($LANGS as $lang) {
    if (!generation_image($TYPE, $lang) ) {
        echo "The $TYPE documentation for $lang language don't exist.\n";
    } else {
        echo " Generated images/revcheck/info_revcheck_" . $TYPE . "_" . $lang . ".png\n";
    }
}


function generation_image($TYPE, $lang) {
    global $LANGUAGES;

    $idx = sqlite_open(SQLITE_DIR . 'rev.' . $TYPE . '.sqlite');
    $Total_files = @get_nb_LANG_files($idx);
    if (!isset($Total_files[$lang]) ) {
        return FALSE;
    }


    $Total_files_lang = $Total_files[$lang];
    //
    $up_to_date = @get_nb_LANG_files_Translated($idx, $lang);
    $up_to_date = ( $up_to_date['total'] == '') ? 0 : $up_to_date['total'];
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
    $pourcent = array();
    $total = 0;
    $total = array_sum($data);

    foreach ( $data as $valeur ) {

        $pourcent[] = round($valeur * 100 / $total);

    }
    
    $noExplode = ($Total_files_lang == $up_to_date) ? 1 : 0;

    $legend = array($pourcent[0] . '%% up to date ('.$up_to_date.')', $pourcent[1] . '%% critical ('.$critical.')', $pourcent[2] . '%% old ('.$old.')', $pourcent[3] . '%% missing ('.$missing.')', $pourcent[4] . '%% without revtag ('.$no_tag.')');
    $title = ucfirst($TYPE). ' : Details for '.$LANGUAGES[$lang].' Doc';

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
    $graph->Stroke('../www/images/revcheck/info_revcheck_' . $TYPE . '_' . $lang . '.png');


    return TRUE;
}

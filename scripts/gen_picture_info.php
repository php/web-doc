<?php
include '../include/jpgraph/jpgraph.php';
include '../include/jpgraph/jpgraph_pie.php';
include '../include/jpgraph/jpgraph_pie3d.php';

include '../include/lib_general.inc.php';
include '../include/lib_revcheck.inc.php';

$plop = array_shift($argv);

$TYPE = array_shift($argv); // Type of documentation

/** make this work for now 
// documentation exist ?
if (!documentation_exists($TYPE)) {
  
  echo "The $TYPE documentation don't exist.\n";
  exit(0);
  
}
*/

foreach( $argv as $lang ) {



  if( !generation_image($TYPE, $lang) ) {
  
    echo "The $TYPE documentation for $lang language don't exist.\n";
  
  } else {
  
    echo " Generate images/revcheck/info_revcheck_" . $TYPE . "_" . $lang . ".png\n";
  
  }
}


function generation_image($TYPE, $lang) {
  global $DOC_LANG;
  
  
$idx = sqlite_open(SQLITE_DIR . 'rev.' . $TYPE . '.sqlite');

//
$Total_files = @get_nb_LANG_files($idx);

   if( !isset($Total_files[$lang]) ) {

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

$legend = array($pourcent[0] . '%% up to date ('.$up_to_date.')', $pourcent[1] . '%% critical ('.$critical.')', $pourcent[2] . '%% old ('.$old.')', $pourcent[3] . '%% missing ('.$missing.')', $pourcent[4] . '%% without revtag ('.$no_tag.')');
$title = ucfirst($TYPE). ' : Details for '.$DOC_LANG[$lang].' Doc';

$graph = new PieGraph(530,300);
$graph->SetShadow();

$graph->title->Set($title);
$graph->title->Align('left');
$graph->title->SetFont(FF_FONT1,FS_BOLD);

$graph->legend->Pos(0.02,0.18,"right","center");

$graph->subtitle->Set('( Info : '.$Total_files_lang.' files )');
$graph->subtitle->Align('left');
$graph->subtitle->SetColor('darkred');

$p1 = new PiePlot3D($data);
$p1->SetSliceColors(array("#68d888", "#ff6347", "#eee8aa", "#dcdcdc", "#f4a460"));
$p1->ExplodeAll();
$p1->SetCenter(0.35,0.55); 
$p1->value->Show(false);

$p1->SetLegends($legend);

$graph->Add($p1);
$graph->Stroke('../www/images/revcheck/info_revcheck_' . $TYPE . '_' . $lang . '.png');


return TRUE;
} 

?>

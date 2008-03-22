<?php

include '../include/jpgraph/jpgraph.php';
include '../include/jpgraph/jpgraph_bar.php';

$inCli = true;
include '../include/init.inc.php';
include '../include/lib_revcheck.inc.php';


// Couleurs des barres, suivant le type de la doc
$type_col_bar = array(

'php' => '9999cc',
'pear' => '339900',
'gtk' => '0099cc'

);


foreach($type_col_bar as $type => $pos) {

    if (is_file(SQLITE_DIR . 'rev.' . $type . '.sqlite') && filesize(SQLITE_DIR . 'rev.' . $type . '.sqlite') != 0 ) {

        $idx = sqlite_open(SQLITE_DIR . 'rev.' . $type . '.sqlite');

        $language = revcheck_available_languages($idx, $type);
        sort($language);
        $files_EN = get_nb_EN_files($idx);


        $tmp = Array();
        $datay_tmp = Array();
        $legendy_tmp = Array();
        $datay = Array();
        $legendy = Array();


        foreach( $language as $lang ) {

            $tmp = get_nb_LANG_files_Translated($idx, $lang);

            $datay_tmp[] = round($tmp['total'] * 100 / $files_EN);
            $legendy_tmp[] = $lang;

        }
        $datay = array_values($datay_tmp);
        $legendy = array_values($legendy_tmp);

        echo "Generating $type graphic for all languages...";

        generation_image($type);

        echo " Done.\n";

    } // end if

} // end foreach




function generation_image($TYPE) {
    global $datay;
    global $legendy;
    global $type_col_bar;

    // Create the graph. These two calls are always required
    $graph = new Graph(550,250);
    $graph->SetScale("textlin");
    $graph->yaxis->scale->SetGrace(20);

    $graph->xaxis->SetLabelmargin(5);
    $graph->xaxis->SetTickLabels($legendy);


    $graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');

    // Add a drop shadow
    $graph->SetShadow();

    // Adjust the margin a bit to make more room for titles
    $graph->img->SetMargin(50,30,20,40);

    // Create a bar pot
    $bplot = new BarPlot($datay);

    // Adjust fill color
    $bplot->SetFillColor('#'.$type_col_bar[$TYPE]);

    $bplot->SetShadow();
    $bplot->value->Show();
    $bplot->value->SetFont(FF_ARIAL,FS_BOLD,10);
    $bplot->value->SetAngle(45);
    $bplot->value->SetFormat('%0.0f');

    // Width
    $bplot->SetWidth(0.6);

    $graph->Add($bplot);

    // Setup the titles
    $graph->title->Set(strtoupper($TYPE)." documentation");
    $graph->xaxis->title->Set("Language");
    $graph->yaxis->title->Set("Files up to date (%)");

    $graph->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

    // Display the graph
    $graph->Stroke('../www/images/revcheck/info_revcheck_' . $TYPE . '_all_lang.png');

}

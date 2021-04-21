<?php
include '../include/jpgraph/src/jpgraph.php';
include '../include/jpgraph/src/jpgraph_bar.php';

include '../include/init.inc.php';
include '../include/lib_revcheck.inc.php';

$idx = new SQLite3(SQLITE_DIR . 'rev.php.sqlite');

$language = revcheck_available_languages($idx);
sort($language);
$files_EN = count_en_files($idx);

foreach ($language as $lang) {
    $tmp = get_stats($idx, $lang, 'uptodate');

    $percent_tmp[] = round($tmp[0] * 100 / $files_EN);
    $legend_tmp[] = $lang;
}

$percent = array_values($percent_tmp);
$legend = array_values($legend_tmp);

echo "Generating PHP graphic for all languages...";
generate_image();
echo " Done.\n";

function generate_image() {
    global $percent, $legend;

    // Create the graph. These two calls are always required
    $graph = new Graph(600,262);
    $graph->SetScale("textlin");
    $graph->yaxis->scale->SetGrace(20);

    $graph->xaxis->SetLabelmargin(5);
    $graph->xaxis->SetTickLabels($legend);

    $graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');

    // Add a drop shadow
    $graph->SetShadow();

    // Adjust the margin a bit to make more room for titles
    $graph->img->SetMargin(50,30,20,40);

    // Create a bar pot
    $bplot = new BarPlot($percent);

    // Adjust fill color
    $bplot->SetFillColor('#9999CC');

    $bplot->SetShadow();
    $bplot->value->Show();
    $bplot->value->SetFont(FF_ARIAL,FS_BOLD,10);
    $bplot->value->SetAngle(45);
    $bplot->value->SetFormat('%0.0f');

    // Width
    $bplot->SetWidth(0.6);

    $graph->Add($bplot);

    // Setup the titles
    $graph->title->Set("PHP documentation");
    $graph->xaxis->title->Set("Language");
    $graph->yaxis->title->Set("Files up to date (%)");

    $graph->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
    $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

    // Display the graph
    $graph->Stroke('../www/images/revcheck/info_revcheck_php_all_lang.png');
}

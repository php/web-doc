<?php
require_once __DIR__ . '/../include/jpgraph/src/jpgraph.php';
require_once __DIR__ . '/../include/jpgraph/src/jpgraph_bar.php';

require_once __DIR__ . '/../include/init.inc.php';
require_once __DIR__ . '/../include/lib_revcheck.inc.php';

$idx = new SQLite3(SQLITE_DIR . 'status.sqlite');

$language = revcheck_available_languages($idx);
sort($language);

foreach ($language as $lang) {
    $stats = get_lang_stats($idx, $lang);

    if (!$stats) die("No stats for $lang");

    $percent_tmp[] = round($stats['TranslatedOk']['total'] * 100 / $stats['total']['total']);
    $legend_tmp[] = $lang;
}

$percent = array_values($percent_tmp);
$legend = array_values($legend_tmp);

// Create the graph. These two calls are always required
$graph = new Graph(600,262);
$graph->SetScale("textlin");

$graph->xaxis->SetLabelmargin(5);
$graph->xaxis->SetTickLabels($legend);

$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');

// Add a drop shadow
$graph->SetShadow();

// Adjust the margin a bit to make more room for titles
$graph->img->SetMargin(50,30,20,40);

// Create a bar pot
$bplot = new BarPlot($percent);
$graph->Add($bplot);

// Adjust fill color
$bplot->SetFillColor([ '#9999CC', '#99CC99', '#CC9999' ]);

$bplot->SetShadow();
$bplot->value->Show();
$bplot->value->SetFont(FF_FONT1,FS_NORMAL,10);
$bplot->value->SetFormat('%0.0f%%');

// Width
$bplot->SetWidth(0.6);

// Setup the titles
$graph->title->Set("PHP Translation Status");
$graph->xaxis->title->Set("Language");
$graph->yaxis->title->Set("Files up to date (%)");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_NORMAL);
$graph->xaxis->title->SetFont(FF_FONT1,FS_NORMAL);

// Display the graph
$graph->Stroke();

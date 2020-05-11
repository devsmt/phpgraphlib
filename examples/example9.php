<?php
include(__DIR__.'/../src/phpgraphlib.php');
$graph = new PHPGraphLib(495, 280, $path = str_replace('.php', '.png', __FILE__ ) );
$data = ['alpha' => 23, 'beta' => 45, 'cappa' => 20, 'delta' => 32, 'echo' => 14];
$data2 = ['alpha' => 15, 'beta' => 23, 'cappa' => 23, 'delta' => 12, 'echo' => 17];
$data3 = ['alpha' => 43, 'beta' => 23, 'cappa' => 34, 'delta' => 16, 'echo' => 20];
$data4 = ['alpha' => 23, 'beta' => 34, 'cappa' => 23, 'delta' => 9, 'echo' => 8];
$graph->addData($data, $data2, $data3, $data4);
$graph->setupYAxis("15");
$graph->setGradient('teal', '#0000FF');
$graph->setXValuesHorizontal(true);
$graph->setXAxisTextColor ('navy');
$graph->setLegend(true);
$graph->setLegendTitle('M1', 'M2', 'M3', 'M4');
$graph->createGraph();

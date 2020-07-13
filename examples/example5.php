<?php
include(__DIR__.'/../src/phpgraphlib.php');
$graph = new PHPGraphLib(500,280, $path = str_replace('.php', '.png', __FILE__ ) );
$data = [23, 45, 20, 44, 41, 18, 49, 19, 42];
$data2 = [15, 23, 23, 11, 54, 21, 56, 34, 23];
$data3 = [43, 23, 34, 23, 53, 32, 43, 41];
$graph->addData($data, $data2, $data3);
$graph->setTitle('CPU Cycles x1000');
$graph->setTitleLocation('left');
$graph->setLegend(true);
$graph->setLegendTitle('Module-1', 'Module-2', 'Module-3');
$graph->setGradient('green', 'olive');
$graph->createGraph();

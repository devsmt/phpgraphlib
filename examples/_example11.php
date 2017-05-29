<?php
include(__DIR__.'/../phpgraphlib.php');
die('unsupported test pie'.PHP_EOL);
include(__DIR__.'/../phpgraphlib_pie.php');
$graph = new PHPGraphLibPie(400, 200, $path = str_replace('.php', '.png', __FILE__ ) );
$data = array("CBS" => 6.3, "NBC" => 4.5,"FOX" => 2.8,
    "ABC" => 2.7, "CW" => 1.4);
$graph->addData($data);
$graph->setTitle('8/29/07 Top 5 TV Networks Market Share');
$graph->setLabelTextColor('50, 50, 50');
$graph->setLegendTextColor('50, 50, 50');
$graph->createGraph();

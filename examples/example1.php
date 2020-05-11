<?php
include __DIR__ . '/../src/phpgraphlib.php';
$graph = new PHPGraphLib(500, 350, $path = str_replace('.php', '.png', __FILE__));
$data = [12124, 5535, 43373, 22223, 90432, 23332, 15544, 24523, 32778,
    38878, 28787, 33243, 34832, 32302];
$graph->addData($data);
$graph->setTitle('Widgets Produced');
$graph->setGradient('red', 'maroon');
$graph->createGraph();

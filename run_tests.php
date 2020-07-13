<?php
declare (strict_types = 1); //php7.0+, will throw a catchable exception if call typehints and returns do not match declaration
//
//
//
$dir = __DIR__;
echo `rm $dir/examples/*.png`;
$a_range = range(1, 9, 1);
foreach ($a_range as $i) {
    echo `php $dir/examples/example$i.php`;
}

#!/bin/bash
rm ./examples/*.png
for i in {1..9}; do php ./examples/example$i.php;   done

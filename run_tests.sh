#!/bin/bash
rm ./examples/*.png
for i in {1..11}; do php ./examples/example$i.php;   done

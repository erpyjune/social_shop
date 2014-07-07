#!/bin/bash

if [ "$#" -ne 1 ]; then
    echo "(USAGE) query"
	 exit 1
fi

for i in {1..100}
do
	echo "okoutdoor.php $i"
	php okoutdoor.php $i
	sleep 2.7 
done


#!/bin/bash

if [ "$#" -ne 1 ]; then
    echo "(USAGE) query"
	 exit 1
fi

echo "php timon_main.php $1"
php timon_main.php $1
echo "php timon_more.php $1"
php timon_more.php $1


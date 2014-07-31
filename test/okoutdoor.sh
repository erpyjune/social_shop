#!/bin/bash

SHOES="http://www.okoutdoor.com/product/product.html?p_category_id=B&page="
BAGL="http://www.okoutdoor.com/product/product.html?p_category_id=D&page="
SLEEPING="http://www.okoutdoor.com/product/product.html?p_category_id=U&page="
TENT="http://www.okoutdoor.com/product/product.html?p_category_id=F&page="

URL_LIST="url1 url2 url3 url4 url5"

if [ "$#" -ne 1 ]; then
    echo "(USAGE) query"
	 exit 1
fi

for url in $URL_LIST
do
	for i in {1..3}
	do
		echo "okoutdoor.php ($i)($url)"
		sleep 1 
	done
done

# end..

#!/bin/sh

cd ../Database/

for f in Operazioni/*.sql
do
		cat $f
done

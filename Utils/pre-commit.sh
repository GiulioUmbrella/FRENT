#!/bin/bash


for file in $(git diff --cached --name-only | grep -E '\.(html)$')
do
  python3 Utils/validator.py -f $file # we only want to lint the staged changes, not any un-staged changes
  if [ $? -ne 0 ]; then
    echo "Il validatore w3c ha trovato degli errori in  '$file'. Correggili e rifai il commit"
	echo "Maggiori dettagli possono essere forniti con il comando" 
	echo "\tpython3 Utils/validator.py -f $file"
    exit 1 # exit with failure status
  fi
done

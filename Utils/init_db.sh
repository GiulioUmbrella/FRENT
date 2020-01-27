#!/bin/bash

GREEN='\e[32m'
RED='\e[31m'
YELLOW='\e[33m'
NC='\e[39m' # No Color

cd ~/public_html/TECHWEB

#if git checkout Db_branch; then
#  echo echo "${YELLOW}Moving to Db_Branch${NC}"
#fi

if [ "$1" == "-u" ]; then
  echo -n "Update del repository..."
  if git pull --all; then
    echo "${GREEN}DONE${NC}"
  else
    echo "${RED}FAILED${NC}"
    exit 1
  fi
fi

echo -n "Creazione dati... "
cd Database/data_builder
if python3 build_data.py; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

cd .. #Torno alla cartella TECHWEB/Database

echo -n "Pulizia database (clean.sql)... "
if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < clean.sql; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

echo -n "Creazione tabelle (createtables.sql)... "
if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < create_tables.sql; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

echo "Creazione funzioni e procedure (functions.sql)... "
PATH_TO_OPERATIONS="Operazioni/*.sql"
for f in $PATH_TO_OPERATIONS
do
  if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < $f; then
    echo "${f}: ${GREEN}DONE${NC}"
  else
    echo "${f}: ${RED}FAILED${NC}"
  fi
done

#echo -n "Caricamento dati (load_data.sql)... "
#if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < load_data.sql; then
#  echo "${GREEN}DONE${NC}"
#else
#  echo "${RED}FAILED${NC}"
#  exit 1
#fi

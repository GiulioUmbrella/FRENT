SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE `amministratori`;
TRUNCATE TABLE `utenti`;
TRUNCATE TABLE `annunci`;
TRUNCATE TABLE `foto_annunci`;
TRUNCATE TABLE `occupazioni`;
TRUNCATE TABLE `commenti`;

SET FOREIGN_KEY_CHECKS=1;

LOAD DATA LOCAL INFILE 'data_builder/utenti.csv' INTO TABLE utenti
  FIELDS TERMINATED BY ','
  OPTIONALLY ENCLOSED BY '"'
  ESCAPED BY '"'
  LINES TERMINATED BY '\n'
  IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'data_builder/annunci.csv' INTO TABLE annunci
  FIELDS TERMINATED BY ','
  OPTIONALLY ENCLOSED BY '"'
  ESCAPED BY '"'
  LINES TERMINATED BY '\n'
  IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'data_builder/occupazioni.csv' INTO TABLE occupazioni
  FIELDS TERMINATED BY ','
  OPTIONALLY ENCLOSED BY '"'
  ESCAPED BY '"'
  LINES TERMINATED BY '\n'
  IGNORE 1 LINES;
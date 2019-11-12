/*Funzione per l'aggiunta di una foto ad un annuncio
NB: questa funzione si occupa solo di inseirire il record nel DB non gestisce file
Cosa restituisce:
  ID della foto aggiunta se tutto è andata ok (ID >= 1).
  -1 in caso di annuncio inesistente
  -2 in caso di _file_path o _descrizione non soddisfino una lunghezza minima
  -3 l'inserimento è fallito
*/
DROP FUNCTION IF EXISTS insert_foto;
DELIMITER |
CREATE FUNCTION insert_foto(id_annuncio int, _file_path varchar(128), _descrizione varchar(128)) RETURNS INT
BEGIN
    DECLARE min_file_path_length INT;
    DECLARE min_descrizione_length INT;

    -- Ritorna -1 in caso di annuncio inesistente
    DECLARE EXIT HANDLER FOR 1452
    BEGIN
        RETURN -1;
    END;

    -- Ritorna -2 in caso _file_path o _descrizione non siano validi
    SET min_file_path_length = 1;
    SET min_descrizione_length = 1;

    IF CHAR_LENGTH(_file_path) < min_file_path_length OR CHAR_LENGTH(_descrizione) < min_descrizione_length THEN
      RETURN -2;
    END IF;

    INSERT INTO foto_annunci (file_path, descrizione, annuncio) VALUES (_file_path, _descrizione, id_annuncio);
    IF ROW_COUNT() = 0 THEN
        RETURN -3;
    ELSE
        RETURN LAST_INSERT_ID();
    END IF;
END |
DELIMITER ;

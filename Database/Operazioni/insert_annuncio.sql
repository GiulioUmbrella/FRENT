/*Funzione per l'inserimento di un annuncio
Cosa restituisce:
  ID dell'annuncio aggiunto se tutto è andata ok (ID >= 1).
  -1 in caso di host inesistente
  -2 in caso ci sia stato un errore durante l'inserimento
*/
DROP FUNCTION IF EXISTS insert_annuncio;
DELIMITER |
CREATE FUNCTION insert_annuncio(_titolo varchar(32), _descrizione varchar(512), _img_anteprima varchar(48), _indirizzo varchar(128), _citta varchar(128), _host int, _max_ospiti tinyint(2), _prezzo_notte float) RETURNS INT
BEGIN
    -- Ritorna -1 in caso di host inesistente
    DECLARE EXIT HANDLER FOR 1452
    BEGIN
        RETURN -1;
    END;

    INSERT INTO annunci (titolo, descrizione, img_anteprima, indirizzo, citta, host, max_ospiti, prezzo_notte)
    VALUES (_titolo, _descrizione, _img_anteprima, _indirizzo, _citta, _host, _max_ospiti, _prezzo_notte);
    IF ROW_COUNT() = 0 THEN
        RETURN -2;
    ELSE
        RETURN LAST_INSERT_ID();
    END IF;
END |
DELIMITER ;

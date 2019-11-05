-- template funzione
/*
DELIMITER |
CREATE FUNCTION nomefunzione(nomeparam tipoparam) RETURNS tipo
BEGIN
 corpo;
RETURN val;
END |
DELIMITER ;
*/

-- template procedura
/*
DELIMITER |
CREATE PROCEDURE nomeprocedura(nomeparam tipoparam)
BEGIN
 corpo;
END |
DELIMITER ;
*/


-- Login
-- Registrazione
-- Modifica dei dati personali dell'utente
-- Eliminazione della propria utenza

-- Ricerca annunci con parametri
-- PRE: _citta e' esistente nel database
DELIMITER |
CREATE PROCEDURE ricerca_annunci(_citta varchar(128), _num_ospiti int(2), di date, df date)
BEGIN
SELECT A.id_annuncio, A.titolo, A.descrizione, A.img_anteprima, A.indirizzo, A.prezzo_notte
FROM annunci A
WHERE A.bloccato = 0 AND A.stato_approvazione = 1 AND A.citta = _citta
AND A.max_ospiti >= _num_ospiti
AND A.id_annuncio NOT IN (
    SELECT annuncio
    FROM occupazioni
    WHERE   (di > data_inizio AND di < data_fine)
            OR (df > data_inizio AND df < data_fine)
            OR (di < data_inizio AND df > data_fine)
            OR (di > data_inizio AND df < data_fine)
    );
END |
DELIMITER ;

-- Ottenere i dettagli (e anche foto e commenti) di un annuncio dato il suo ID
DELIMITER |
CREATE PROCEDURE dettagli_annuncio(id int)
BEGIN
 SELECT *
 FROM annunci
 WHERE id_annuncio = id;
END |
DELIMITER ;

-- Ottenere le foto di un annuncio dato il suo ID
DELIMITER |
CREATE PROCEDURE foto_annuncio(id int)
BEGIN
 SELECT *
 FROM foto_annunci
 WHERE annuncio = id;
END |
DELIMITER ;

-- Ottenere i commenti di un annuncio dato il suo ID
DELIMITER |
CREATE PROCEDURE commenti_annuncio(id int)
BEGIN
 SELECT C.*
 FROM occupazioni O INNER JOIN commenti C ON O.id_occupazione = C.prenotazione
 WHERE O.annuncio = id;
END |
DELIMITER ;

-- Effettuare la prenotazione di un annuncio sui parametri di ricerca
-- Eliminare una prenotazione dato il suo ID
-- Ottenere le prenotazioni effettuate da un guest

-- Pubblicare un commento dato l'ID di una prenotazione
-- PRE: _prenotazione è l'ID di una prenotazione (occupazione di un guest), gli altri parametri sono validi
-- POST: ritornato 1 se il commento è stato pubblicato con successo, 0 altrimenti (se si è verificato un errore o se ne era già presente uno)
DELIMITER |
CREATE FUNCTION pubblica_commento(_prenotazione int, _titolo varchar(64), _commento varchar(512), _votazione tinyint(1)) RETURNS tinyint(1)
BEGIN
    DECLARE commento_pubblicato tinyint(1);

    IF NOT EXISTS(
        SELECT prenotazione
        FROM commenti
        WHERE prenotazione = _prenotazione
    ) THEN
        INSERT INTO commenti(prenotazione, titolo, commento, votazione) VALUES
        (_prenotazione, _titolo, _commento, _votazione);

        -- verifico che il commento sia stato inserito
        IF EXISTS(
            SELECT prenotazione
            FROM commenti
            WHERE prenotazione = _prenotazione
        ) THEN
            SET commento_pubblicato = 1;
        ELSE
            SET commento_pubblicato = 0;
        END IF;
    ELSE
        SET commento_pubblicato = 0;
    END IF;

    RETURN commento_pubblicato;
END |
DELIMITER ;
-- Modificare un commento dato l'ID di una prenotazione
-- Eliminare un commento dato l'ID di una prenotazione

-- Ottenere gli annunci pubblicati da un host
-- Modificare un annuncio dato il suo ID
-- Eliminare un annuncio dato il suo ID

-- Ottenere le occupazioni di un annuncio dato un ID di un annuncio
DELIMITER |
CREATE PROCEDURE occupazioni_annuncio(_id_annuncio int)
BEGIN
  SELECT id_occupazione, utente, prenotazione_guest, num_ospiti, data_inizo, data_fine
  FROM occupazioni
  WHERE annuncio = _id_annuncio;
END |
DELIMITER ;

-- Aggiunta di una foto (e dei dettagli) ad un annuncio dato l'ID di un annuncio
/*Cosa ritorna:
ID della foto aggiunta se tutto è andata ok (ID >= 1).
-1 in caso di annuncio inesistente
-2 in caso di _file_path o _descrizione non soddisfino una lunghezza minima
*/
DELIMITER |
CREATE FUNCTION aggiungi_foto(id_annuncio int, _file_path varchar, _descrizione varchar) RETURNS TINYINT
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

  INSERT INTO foto_annunci (file_path, descrizione, annuncio) 	VALUES (_file_path, _descrizione, id_annuncio);
  RETURN LAST_INSERT_ID();
END |
DELIMITER ;
-- Rimozione di una foto ad un annuncio dato l'ID di un annuncio

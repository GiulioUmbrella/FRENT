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
-- Modificare un commento dato l'ID di una prenotazione
-- Eliminare un commento dato l'ID di una prenotazione

-- Ottenere gli annunci pubblicati da un host
-- Modificare un annuncio dato il suo ID
-- Eliminare un annuncio dato il suo ID

-- Ottenere le occupazioni di un annuncio dato un ID di un annuncio

-- Aggiunta di una foto (e dei dettagli) ad un annuncio dato l'ID di un annuncio
-- Rimozione di una foto ad un annuncio dato l'ID di un annuncio

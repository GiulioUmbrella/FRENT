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

DELIMITER |
CREATE PROCEDURE modifica_commento(_id int, _titolo varchar(64),_commento varchar(512), _valutazione tinyint(1))
BEGIN
    update commenti
    set commenti.titolo = _titolo, commenti.commento= _commento, commenti.votazione= _valutazione
    where  commenti.prenotazione= _id;
END |
DELIMITER ;
-- Eliminare un commento dato l'ID di una prenotazione
--
DELIMITER |
CREATE PROCEDURE elimina_commento_con_id(_id int)
BEGIN
    delete from commenti where prenotazione = _id;
END |
DELIMITER ;

-- Ottenere gli annunci pubblicati da un host

DELIMITER |
CREATE procedure list_annunci_host(_id_host int)
BEGIN
    select *
    from annunci
        where _id_host= annunci.host;
END |
-- Modificare un annuncio dato il suo ID
DELIMITER |
CREATE PROCEDURE modifica_annuncio(_id int, _titolo varchar(32), _descrizione varchar(512),_img_anteprima varchar(48),
     _indirizzo varchar(128), _citta varchar(128),_max_ospiti tinyint(2),_prezzo_notte float)
BEGIN
    update annunci
    set annunci.titolo = _titolo, annunci.descrizione= _descrizione , annunci.indirizzo=_indirizzo,
        annunci.img_anteprima= _img_anteprima, annunci.citta= _citta, annunci.max_ospiti= _max_ospiti,
        annunci.prezzo_notte =_prezzo_notte, stato_approvazione = 0
    where  annunci.id_annuncio= _id;
END |
DELIMITER ;

-- Eliminare un annuncio dato il suo ID

-- Ottenere le occupazioni di un annuncio dato un ID di un annuncio

-- Aggiunta di una foto (e dei dettagli) ad un annuncio dato l'ID di un annuncio
-- Rimozione di una foto ad un annuncio dato l'ID di un annuncio

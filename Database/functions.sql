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
-- PRE: utente corrisponde a mail o username dell'utente
DELIMITER |
CREATE PROCEDURE login(_utente varchar(191), _password varchar(48))
BEGIN
  SELECT *
  FROM utenti u
  WHERE (u.user_name = _utente OR u.mail = _utente)
  AND u.password = _password;
END |
DELIMITER ;

-- Registrazione
-- PRE: _password è la password hashata tramite la funzione PHP
-- Se il nuovo utente è stato inserito restituisce il suo ID, 0 altrimenti
DELIMITER |
CREATE FUNCTION registrazione(_nome varchar(32), _cognome varchar(32), _username varchar(32), _mail varchar(191), _password varchar(48), _data date, _img_profilo varchar(48), _telefono varchar(18)) RETURNS INT
BEGIN
  INSERT INTO utenti(nome, cognome, user_name, mail, password, data_nascita, img_profilo, telefono) VALUES (_nome, _cognome, _username, _mail, _password, _data, _img_profilo, _telefono);

  RETURN LAST_INSERT_ID();
END |
DELIMITER ;

-- Modifica dei dati personali dell'utente
-- PRE: _password è una stringa risultato dell'applicazione di una funzione di hash sulla stringa corrispondente alla password dell'utente
DELIMITER |
CREATE FUNCTION modifica_dati_utente(_idutente int, _nome varchar(32), _cognome varchar(32), _username varchar(32), _mail varchar(255), _password varchar(48), _datanascita date, _imgprofilo varchar(48), telefono varchar(18)) RETURNS tinyint(1)
BEGIN
    UPDATE utenti
    SET nome = _nome,
    cognome = _cognome,
    username = _username,
    mail = _mail,
    password = _password,
    data_nascita = _datanascita,
    img_profilo = _imgprofilo,
    telefono = _telefono
    WHERE id_utente = _idutente;

    IF ROW_COUNT() = 1 THEN
        RETURN 1;
    ELSE
        RETURN 0;
    END IF;
END |
DELIMITER ;

-- Eliminazione della propria utenza

-- Ricerca annunci con parametri
-- PRE: _citta e' esistente nel database, di e df sono date valide e di < df
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
        WHERE (
            (di > data_inizio AND di < data_fine) OR
            (df > data_inizio AND df < data_fine) OR
            (di < data_inizio AND df > data_fine) OR
            (di > data_inizio AND df < data_fine)
        )
    );
END |
DELIMITER ;

-- Ottenere i dettagli di un annuncio dato il suo ID
-- PRE: id è l'identificativo di un annuncio
DELIMITER |
CREATE PROCEDURE dettagli_annuncio(id int)
BEGIN
    SELECT *
    FROM annunci
    WHERE id_annuncio = id;
END |
DELIMITER ;

-- Ottenere le foto di un annuncio dato il suo ID
-- PRE: id è l'identificativo di un annuncio
DELIMITER |
CREATE PROCEDURE foto_annuncio(id int)
BEGIN
    SELECT *
    FROM foto_annunci
    WHERE annuncio = id;
END |
DELIMITER ;

-- Ottenere i commenti di un annuncio dato il suo ID
-- PRE: id è l'identificativo di un annuncio
DELIMITER |
CREATE PROCEDURE commenti_annuncio(id int)
BEGIN
    SELECT C.*
    FROM occupazioni O INNER JOIN commenti C ON O.id_occupazione = C.prenotazione
    WHERE O.annuncio = id;
END |
DELIMITER ;

-- Effettuare la prenotazione di un annuncio sui parametri di ricerca
DELIMITER |
CREATE FUNCTION effettua_prenotazione(_utente int, _annuncio int, _numospiti int(2), di date, df date) RETURNS tinyint(1)
BEGIN
    DECLARE occupazione_generata tinyint(1);

    IF DATEDIFF(df, di) <= 0 -- controllo correttezza delle date
    THEN SET occupazione_generata = 0;
    END IF;

    -- date corrette
    IF EXISTS (
        SELECT *
        FROM occupazioni
        WHERE annuncio = _annuncio AND (
            (di > data_inizio AND di < data_fine) OR
            (df > data_inizio AND df < data_fine) OR
            (di < data_inizio AND df > data_fine) OR
            (di > data_inizio AND df < data_fine)
        )
    ) THEN
        SET occupazione_generata = 0;
    ELSE
        INSERT INTO occupazioni(utente, annuncio, prenotazione_guest, num_ospiti, data_inizio, data_fine)
        VALUES (_utente, _annuncio, _numospiti, di, df);

        IF ROW_COUNT() = 0 THEN
            SET occupazione_generata = 0;
        ELSE
            SET occupazione_generata = 1;
        END IF;
    END IF;

    RETURN occupazione_generata;
END |
DELIMITER ;

-- Eliminare una prenotazione dato il suo ID

-- Ottenere le prenotazioni effettuate da un guest
-- PRE: id_utente corrisponde ad un utenti.id_utente
DELIMITER |
CREATE PROCEDURE prenotazioni_effettuate_guest(id_utente int)
BEGIN
    SELECT *
    FROM occupazioni
    WHERE utente = id_utente
    AND prenotazione_guest = 1;
END |
DELIMITER ;

-- Pubblicare un commento dato l'ID di una prenotazione
-- PRE: _prenotazione è l'ID di una prenotazione (occupazione di un guest), gli altri parametri sono validi
-- POST: ritornato 1 se il commento è stato pubblicato con successo, 0 altrimenti (se si è verificato un errore o se ne era già presente uno)
DELIMITER |
CREATE FUNCTION pubblica_commento(_prenotazione int, _titolo varchar(64), _commento varchar(512), _votazione tinyint(1)) RETURNS tinyint(1)
BEGIN
    DECLARE commento_pubblicato tinyint(1);

    IF EXISTS(
        SELECT *
        FROM commenti
        WHERE prenotazione = _prenotazione
    ) THEN
        SET commento_pubblicato = 0;
    ELSE
        INSERT INTO commenti(prenotazione, titolo, commento, votazione) VALUES
        (_prenotazione, _titolo, _commento, _votazione);

        -- verifico che il commento sia stato inserito
        IF ROW_COUNT() = 0 THEN
            SET commento_pubblicato = 0;
        ELSE
            SET commento_pubblicato = 1;
        END IF;
    END IF;

    RETURN commento_pubblicato;
END |
DELIMITER ;

-- Modificare un commento dato l'ID di una prenotazione
-- PRE: _id è l'ID di una prenotazione
DELIMITER |
CREATE PROCEDURE modifica_commento(_id int, _titolo varchar(64),_commento varchar(512), _valutazione tinyint(1))
BEGIN
    update commenti
    set commenti.titolo = _titolo, commenti.commento= _commento, commenti.votazione= _valutazione
    where  commenti.prenotazione= _id;
END |
DELIMITER ;

-- Eliminare un commento dato l'ID di una prenotazione
-- PRE: _id è l'ID di una prenotazione
-- 0: non è stato eliminato nulla
-- 1: è stato eliminato il commento
DELIMITER |
CREATE FUNCTION elimina_commento(_id int) RETURNS INT
BEGIN
    delete from commenti where prenotazione = _id;
    if (row_count() = 0) then
        return 0;
    else
        return 1;
    end if;
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
DELIMITER ;

-- Modificare un annuncio dato il suo ID
DELIMITER |
CREATE PROCEDURE modifica_annuncio(_id int, _titolo varchar(32), _descrizione varchar(512),_img_anteprima varchar(48),
     _indirizzo varchar(128), _citta varchar(128),_max_ospiti tinyint(2), _prezzo_notte float)
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
DELIMITER |
CREATE PROCEDURE occupazioni_annuncio(_id_annuncio int)
BEGIN
    SELECT id_occupazione, utente, prenotazione_guest, num_ospiti, data_inizio, data_fine
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
CREATE FUNCTION aggiungi_foto(id_annuncio int, _file_path varchar(128), _descrizione varchar(128)) RETURNS INT
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
    RETURN LAST_INSERT_ID();
END |
DELIMITER ;


-- Rimozione di una foto ad un annuncio dato l'ID di un annuncio
/*Cosa ritorna:
0 in caso di successo
-1 in caso ci sia stato un errore nell'eliminare la foto
*/
DELIMITER |
CREATE FUNCTION rimozione_foto(_id_foto INT) RETURNS INT
BEGIN
    DELETE FROM foto_annunci WHERE id_foto = _id_foto;

    IF ROW_COUNT() = 0 THEN
    RETURN -1;
    ELSE
    RETURN 0;
    END IF;
END |
DELIMITER ;

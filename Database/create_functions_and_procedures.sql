/*Funzione che permette la modifica dello stato di approvazione di  un annuncio
Cosa restituisce:
  ID dell'annuncio modificato
  -1 in caso di errori
*/
DROP FUNCTION IF EXISTS admin_edit_stato_approvazione_annuncio;
DELIMITER |
CREATE FUNCTION admin_edit_stato_approvazione_annuncio(_id int, _stato tinyint(1)) RETURNS INT
BEGIN
    -- controllo validità
    IF _stato < 0 OR _stato > 2 THEN
        RETURN -1;
    END IF;

    update annunci
    set annunci.stato_approvazione = _stato
    where annunci.id_annuncio= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END |
DELIMITER ;

/*Procedura per ottenere tutti gli annunci da approvare
Resituisce gli annunci non ancora approvati da 
*/
DROP PROCEDURE IF EXISTS admin_get_annunci;
DELIMITER |
CREATE PROCEDURE admin_get_annunci()
BEGIN
    SELECT id_annuncio, titolo, stato_approvazione
    FROM annunci
    WHERE stato_approvazione = 0;
END |
DELIMITER ;

/*Procedura di login per admin
PRE: utente corrisponde a mail dell'admin
Restituisce il record relativo all'username/email alla quale si sta provando ad accedere,
in caso non vada a buon fine, verrà restituito un empty set
*/
DROP PROCEDURE IF EXISTS admin_login;
DELIMITER |
CREATE PROCEDURE admin_login(_mail varchar(191), _password varchar(48))
BEGIN
  SELECT *
  FROM amministratori a
  WHERE a.mail = _mail
  AND a.password = _password;
END |
DELIMITER ;

/*Funzione che permette l'eliminazio di un annucio previo controllo
Cosa restituisce:
  0 l'annuncio è stato eliminato e con esso le foto e i commenti
  -1 l'annuncio non è eliminabile perchè ci sono prenotazioni in corso o future
  -2 l'annuncio, i commenti e le foto non sono stati eliminati (per esempio per errori nelle chiavi esterne)
*/
DROP FUNCTION IF EXISTS delete_annuncio;
DELIMITER |
CREATE FUNCTION delete_annuncio(_id_annuncio INT) RETURNS INT
BEGIN
  DECLARE curdate DATE;

  DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        RETURN -2;
    END;

  SET curdate = CURDATE();
  IF _id_annuncio IN (SELECT annuncio
                      FROM prenotazioni
                      WHERE (data_inizio <= curdate AND data_fine >= curdate) OR data_inizio > curdate) THEN
    RETURN -1;
  END IF;

  DELETE FROM commenti
  WHERE prenotazione IN ( SELECT id_prenotazione
                          FROM prenotazioni
                          WHERE annuncio = _id_annuncio);
  -- in prenotazioni il campo annuncio viene messo a null dalla politica di reazione
  DELETE FROM annunci WHERE id_annuncio = _id_annuncio;

  IF ROW_COUNT() = 0 THEN
    RETURN -2;
  ELSE
      RETURN 0;
  END IF;
END |
DELIMITER ;

/*Funzione che elimina un commento
PRE: _id è l'ID di una prenotazione
Cosa restituisce:
  0 in caso il commento venga eliminato correttamente
  -1 altrimenti
*/
DROP FUNCTION IF EXISTS delete_commento;
DELIMITER |
CREATE FUNCTION delete_commento(_id_prenotazione int) RETURNS INT
BEGIN
    delete from commenti where prenotazione = _id_prenotazione;
    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN 0;
    END IF;
END |
DELIMITER ;

/*Funzione che si occupa di eliminare un prenotazione previo controlli
Cosa restituisce:
  0 se l'prenotazione è stata correttamente eliminata
  -1 se l'prenotazione non è eliminabile in quanto prenotazione presente o passata
  -2 in caso l'prenotazione non sia stata eliminata (per esempio per errori nelle chiavi esterne)
*/
DROP FUNCTION IF EXISTS delete_prenotazione;
DELIMITER |
CREATE FUNCTION delete_prenotazione( _id_prenotazione int) RETURNS INT
BEGIN
  DECLARE d_inizio date;

  SELECT data_inizio INTO d_inizio
  FROM prenotazioni
  WHERE id_prenotazione = _id_prenotazione;

  IF CURDATE() >= d_inizio THEN
    RETURN -1;
  END IF;

  DELETE FROM prenotazioni
  WHERE id_prenotazione = _id_prenotazione;

  IF ROW_COUNT() = 0 THEN
    RETURN -2;
  ELSE
    RETURN 0;
  END IF;
END |
DELIMITER ;

/*Funzione per l'eleiminazione di un utente
Cosa ritorna:
  0 in caso di successo altrimenti:
  -1 in caso ci siano prenotazioni correnti
  -2 in caso ci siano annuci con prenotazioni correnti o future
  -3 in caso l'operazione di delete abbia fallito (per esempio gli è stato passato un id non valido)
*/
DROP FUNCTION IF EXISTS delete_user;
DELIMITER |
CREATE FUNCTION delete_user(_id_utente int) RETURNS INT
BEGIN

-- Abort in caso di prenotazioni correnti
IF EXISTS (
    SELECT * FROM prenotazioni WHERE utente = _id_utente AND (data_inizio <= CURDATE() AND data_fine >= CURDATE())
  ) THEN
  RETURN -1;
END IF;

-- Abort in caso di annunci con prenotazioni future o in corso
IF EXISTS (
  SELECT * FROM prenotazioni WHERE annuncio IN (
    SELECT id_annuncio FROM annunci WHERE host = _id_utente) AND ((data_inizio <= CURDATE() AND data_fine >= CURDATE()) OR (data_inizio >= CURDATE())
  )
) THEN
  RETURN -2;
END IF;

DELETE FROM utenti WHERE id_utente = _id_utente;

IF ROW_COUNT() = 0 THEN
  RETURN -3;
ELSE
  RETURN 0;
END IF;
END |
DELIMITER ;

/*Funzione che permette la modifca di un annuncio
Cosa restituisce:
  ID dell'annuncio modificato
  -1 in caso di errori
*/
DROP FUNCTION IF EXISTS edit_annuncio;
DELIMITER |
CREATE FUNCTION edit_annuncio(_id int, _titolo varchar(32), _descrizione varchar(512), _img_anteprima varchar(48), _desc_anteprima varchar(256),
_indirizzo varchar(128), _citta varchar(128), _max_ospiti tinyint(2), _prezzo_notte float) RETURNS INT
BEGIN
    update annunci
    set annunci.titolo = _titolo, annunci.descrizione= _descrizione , annunci.indirizzo=_indirizzo,
        annunci.img_anteprima= _img_anteprima, annunci.desc_anteprima = _desc_anteprima, annunci.citta= _citta, annunci.max_ospiti= _max_ospiti,
        annunci.prezzo_notte =_prezzo_notte, stato_approvazione = 0
    where  annunci.id_annuncio= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END |
DELIMITER ;

/*Funzione che permette la modifica di un commento
PRE: _id è l'ID di una prenotazione
Cosa restituisce:
  ID della prenotazione (e quindi del commento) modificato in caso di successo
  -1 in caso ci siano stati problemi durante l'update (per esempio qualche errore con le chiavi esterne)
*/
DROP FUNCTION IF EXISTS edit_commento;
DELIMITER |
CREATE FUNCTION edit_commento(_id int, _titolo varchar(64),_commento varchar(512), _valutazione tinyint(1)) RETURNS INT
BEGIN
    update commenti
    set commenti.titolo = _titolo, commenti.commento= _commento, commenti.votazione= _valutazione
    where  commenti.prenotazione= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END |
DELIMITER ;

/*Funzione per l'aggiornamento/modifica dei dati di un utente
PRE: _password è una stringa risultato dell'applicazione di una funzione di hash sulla stringa corrispondente alla password dell'utente
Cosa restituisce:
  l'ID dell'utente modificato in caso di successo
  -1 altrimenti
*/
DROP FUNCTION IF EXISTS edit_user;
DELIMITER |
CREATE FUNCTION edit_user(_id_utente int, _nome varchar(32), _cognome varchar(32), _username varchar(32), _mail varchar(255), _password varchar(48), _datanascita date, _imgprofilo varchar(48), _telefono varchar(18)) RETURNS INT
BEGIN
    UPDATE utenti
    SET utenti.nome = _nome,
    utenti.cognome = _cognome,
    utenti.user_name = _username,
    utenti.mail = _mail,
    utenti.password = _password,
    utenti.data_nascita = _datanascita,
    utenti.img_profilo = _imgprofilo,
    utenti.telefono = _telefono
    WHERE utenti.id_utente = _id_utente;

    IF ROW_COUNT() = 0 THEN
        RETURN -1;
    ELSE
        RETURN _id_utente;
    END IF;
END |
DELIMITER ;

/*Procedura per ottenre gli annunci inseriti da un utente (host)
Restituisce i record degli annunci creati da un utente (host)
*/
DROP PROCEDURE IF EXISTS get_annunci_host;
DELIMITER |
CREATE procedure get_annunci_host(_id_host int)
BEGIN
    select *
    from annunci
    where host = _id_host;
END |
DELIMITER ;

/*Procedura per ottenere i dettagli di un annuncio
PRE: id è l'identificativo di un annuncio
Restituisce il reocrd relativo all'ID passato come parametro
*/
DROP PROCEDURE IF EXISTS get_annuncio;
DELIMITER |
CREATE PROCEDURE get_annuncio(id int)
BEGIN
    SELECT *
    FROM annunci
    WHERE id_annuncio = id;
END |
DELIMITER ;

/*Procedura per ottenere tutte le città in cui è attivo Frent (ovvero dove ci sono annunci)
Resituisce tutte le città in cui ci sono annunci
*/
DROP PROCEDURE IF EXISTS get_citta_annunci;
DELIMITER |
CREATE PROCEDURE get_citta_annunci()
BEGIN
    SELECT DISTINCT citta
    FROM annunci
    WHERE stato_approvazione = 1;
END |
DELIMITER ;

/*Procedura per ottenere tutt i commenti relativi ad un annucio
PRE: id è l'identificativo di un annuncio
Resituisce tutti i record relativi ai commnenti dell'annuncio identificato dall'ID
*/
DROP PROCEDURE IF EXISTS get_commenti_annuncio;
DELIMITER |
CREATE PROCEDURE get_commenti_annuncio(id int)
BEGIN
    SELECT C.*, U.user_name, U.img_profilo 
    FROM prenotazioni O INNER JOIN commenti C ON O.id_prenotazione = C.prenotazione
    INNER JOIN utenti U ON O.utente = U.id_utente
    WHERE O.annuncio = id;
END |
DELIMITER ;

/*Procedura per ottenere i dettagli di un'prenotazione
PRE: id è l'identificativo di un annuncio
Restituisce il reocrd relativo all'ID passato come parametro
*/
DROP PROCEDURE IF EXISTS get_prenotazione;
DELIMITER |
CREATE PROCEDURE get_prenotazione(id int)
BEGIN
    SELECT *
    FROM prenotazioni
    WHERE id_prenotazione = id;
END |
DELIMITER ;

/*Procedura che permette di ottenere tutte le prenotazioni riguradanti un annuncio
Restituisce i record relativi alle prenotazioni dell'annuncio il cui ID è passato in input
*/
DROP PROCEDURE IF EXISTS get_prenotazioni_annuncio;
DELIMITER |
CREATE PROCEDURE get_prenotazioni_annuncio(_id_annuncio int)
BEGIN
    SELECT id_prenotazione, utente, num_ospiti, data_inizio, data_fine
    FROM prenotazioni
    WHERE annuncio = _id_annuncio
    ORDER BY data_inizio;
END |
DELIMITER ;

/*Procedura per ottenere le prenotazioni effettuate da un utente (guest)
PRE: id_utente corrisponde ad un utenti.id_utente
Restituisce i record relativi alle prenotazioni di un utente che ha effettuato come guest
*/
DROP PROCEDURE IF EXISTS get_prenotazioni_guest;
DELIMITER |
CREATE PROCEDURE get_prenotazioni_guest(id_utente int)
BEGIN
    SELECT *
    FROM prenotazioni
    WHERE utente = id_utente
    ORDER BY prenotazioni.data_inizio;
END |
DELIMITER ;

/* Procedura per ottenere i dettagli di un annuncio
Restituisce i record degli ultimi sei annunci approvati dagli amministratori
*/
DROP PROCEDURE IF EXISTS get_ultimi_annunci_approvati;
DELIMITER |
CREATE PROCEDURE get_ultimi_annunci_approvati(id_utente int)
BEGIN
    SELECT id_annuncio, titolo, img_anteprima, desc_anteprima
    FROM annunci
    WHERE stato_approvazione=1 and annunci.host!= id_utente
    ORDER BY id_annuncio DESC
    LIMIT 6;
END |
DELIMITER ;

/*Procedura per ottenere i dettagli di un annuncio
PRE: id è l'identificativo di un annuncio
Restituisce il reocrd relativo all'ID passato come parametro
*/
DROP PROCEDURE IF EXISTS get_user;
DELIMITER |
CREATE PROCEDURE get_user(id int)
BEGIN
    SELECT *
    FROM utenti
    WHERE id_utente = id;
END |
DELIMITER ;

/*Funzione per l'inserimento di un annuncio
Cosa restituisce:
  ID dell'annuncio aggiunto se tutto è andata ok (ID >= 1).
  -1 in caso di host inesistente
  -2 in caso ci sia stato un errore durante l'inserimento
*/
DROP FUNCTION IF EXISTS insert_annuncio;
DELIMITER |
CREATE FUNCTION insert_annuncio(_titolo varchar(32), _descrizione varchar(512), _img_anteprima varchar(48), _desc_anteprima varchar(256), _indirizzo varchar(128), _citta varchar(128), _host int, _max_ospiti tinyint(2), _prezzo_notte float) RETURNS INT
BEGIN
    -- Ritorna -1 in caso di host inesistente
    DECLARE EXIT HANDLER FOR 1452
    BEGIN
        RETURN -1;
    END;

    INSERT INTO annunci (titolo, descrizione, img_anteprima, desc_anteprima, indirizzo, citta, host, max_ospiti, prezzo_notte)
    VALUES (_titolo, _descrizione, _img_anteprima, _desc_anteprima, _indirizzo, _citta, _host, _max_ospiti, _prezzo_notte);
    IF ROW_COUNT() = 0 THEN
        RETURN -2;
    ELSE
        RETURN LAST_INSERT_ID();
    END IF;
END |
DELIMITER ;

/*Funzione che permette l'inserimento di un commento ad una prenotazione
PRE: _prenotazione è l'ID di una prenotazione (prenotazione di un guest), gli altri parametri sono validi
Cosa restituisce:
  ID del commento appena inserito se l'inserimento è andato a buon fine
  -1 in caso di prenotazione inesistente
  -2 in caso di prenotazione già commentata
  -4 se il commento non è stato inserito (per esempio in caso di errori nelle chiavi esterne)
*/
DROP FUNCTION IF EXISTS insert_commento;
DELIMITER |
CREATE FUNCTION insert_commento(_prenotazione int, _titolo varchar(64), _commento varchar(512), _votazione tinyint(1)) RETURNS INT
BEGIN
    -- Ritorna 0 in caso di prenotazione inesistente
    DECLARE EXIT HANDLER FOR 1452
    BEGIN
      RETURN -1;
    END;

    -- Prenotazione già commentata
    IF EXISTS(
        SELECT *
        FROM commenti
        WHERE prenotazione = _prenotazione
    ) THEN
        RETURN -2;
    END IF;

    INSERT INTO commenti(prenotazione, titolo, commento, votazione) VALUES
      (_prenotazione, _titolo, _commento, _votazione);

      -- verifico che il commento sia stato inserito
      IF ROW_COUNT() = 0 THEN
          RETURN -4;
      ELSE
          RETURN _prenotazione; 
      END IF;
END |
DELIMITER ;

/*Funzione che permette la creazione di una nuova funzione controllando la validità dei dati inseriti
Cosa restituisce:
  ID dell'prenotazione appena inserita se tutto è andato a buon fine
  -1 se la data di inizio e la data di fine passate in input non sono ordinate temporalmente
  -2 se ci sono altre prenotazioni nel range di date passate in input
  -3 se l'inserimento è fallito (per esempio a causa di chiavi esterne errate)
  -4 se l'host sta tentando di prenotare presso un suo annuncio
*/
DROP FUNCTION IF EXISTS insert_prenotazione;
DELIMITER |
CREATE FUNCTION insert_prenotazione(_utente int, _annuncio int, _numospiti int(2), di date, df date) RETURNS INT
BEGIN
    -- controllo correttezza delle date
    IF DATEDIFF(df, di) <= 0 THEN
      RETURN -1;
    END IF;

    -- Controllo presenza altre prenotazioni
    IF EXISTS (
        SELECT *
        FROM prenotazioni
        WHERE annuncio = _annuncio AND (
          (di >= data_inizio AND di <= data_fine) OR
          (df >= data_inizio AND df <= data_fine) OR
          (di <= data_inizio AND df >= data_fine) OR
          (di >= data_inizio AND df <= data_fine)
        )
    ) THEN
        RETURN -2;
    END IF;

    IF _utente = (SELECT host FROM annunci WHERE id_annuncio = _annuncio) THEN
       RETURN -4;
    END IF;

    INSERT INTO prenotazioni(utente, annuncio, num_ospiti, data_inizio, data_fine)
    VALUES (_utente, _annuncio, _numospiti, di, df);

    IF ROW_COUNT() = 0 THEN -- Modifica non effettuata
        RETURN -3;
    ELSE
        RETURN LAST_INSERT_ID();
    END IF;
END |
DELIMITER ;

/*Procedura di login
PRE: utente corrisponde a mail o username dell'utente
Restituisce il reocrd relativo all'username/email alla quale si sta provando ad accedere,
in caso non vada a buon fine, verrà restituito un empty set
*/
DROP PROCEDURE IF EXISTS login;
DELIMITER |
CREATE PROCEDURE login(_mail varchar(191), _password varchar(48))
BEGIN
  SELECT *
  FROM utenti u
  WHERE u.mail = _mail
  AND u.password = _password;
END |
DELIMITER ;

/*Funzione di registrazione
PRE: _password è la password hashata tramite la funzione PHP
Cosa restituisce:
  ID dell'utente appena creato
  -1 in caso ci siano stati problemi durante la registrazione
  -2 e-mail già presente nel database
*/
DROP FUNCTION IF EXISTS registrazione;
DELIMITER |
CREATE FUNCTION registrazione(_nome varchar(32), _cognome varchar(32), _username varchar(32), _mail varchar(191), _password varchar(48), _data date, _img_profilo varchar(48), _telefono varchar(18)) RETURNS INT
BEGIN
  -- verifico che non sia già presente una mail associata ad un account con quella passata come parametro
  IF EXISTS(SELECT * FROM utenti WHERE mail = _mail) THEN
    RETURN -2;
  END IF;

  INSERT INTO utenti(nome, cognome, user_name, mail, password, data_nascita, img_profilo, telefono) VALUES (_nome, _cognome, _username, _mail, _password, _data, _img_profilo, _telefono);

  IF ROW_COUNT() = 0 THEN
      RETURN -1;
  ELSE
      RETURN LAST_INSERT_ID();
  END IF;
END |
DELIMITER ;

/*Procedura per la ricerca degli annunci secondo alcuni parametri
PRE: _citta e' esistente nel database, di e df sono date valide e di < df
Resituisce i record che soddisfano i criteria di ricerca e di disponibilità
*/
DROP PROCEDURE IF EXISTS ricerca_annunci;
DELIMITER |
CREATE PROCEDURE ricerca_annunci(_citta varchar(128), _num_ospiti int(2), di date, df date)
BEGIN
    SELECT A.id_annuncio, A.titolo, A.descrizione, A.img_anteprima, A.desc_anteprima, A.indirizzo, A.prezzo_notte
    FROM annunci A
    WHERE A.stato_approvazione = 1 AND A.citta like _citta
    AND A.max_ospiti >= _num_ospiti
    AND A.id_annuncio NOT IN (
        SELECT annuncio
        FROM prenotazioni
        WHERE (
          (di >= data_inizio AND di <= data_fine) OR
          (df >= data_inizio AND df <= data_fine) OR
          (di <= data_inizio AND df >= data_fine) OR
          (di >= data_inizio AND df <= data_fine)
        )
    );
END |
DELIMITER ;
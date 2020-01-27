-- drop delle tabelle
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS amministratori, utenti, annunci, prenotazioni, commenti;
SET FOREIGN_KEY_CHECKS=1;

-- creazione delle tabelle
create table amministratori (
	id_amministratore int primary key auto_increment,
	user_name varchar(32) not null,
	password varchar(48) not null,
	mail varchar(191) not null,
	unique(mail)
);


create table utenti (
	id_utente int primary key auto_increment,
	nome varchar(32) not null,
	cognome varchar(32) not null,
	user_name varchar(32) not null,
	mail varchar(191) not null,
	password varchar(48) not null,
	data_nascita date not null,
	img_profilo varchar(48) not null default "defaultImages/imgAnteprimaAnnuncioDefault.png",
	telefono varchar(18) not null,
	unique(mail)
);

create table annunci (
	id_annuncio int primary key auto_increment,
	titolo varchar(32) not null,
	descrizione varchar(512) not null,
	img_anteprima varchar(48) not null default "defaultImages/imgProfiloDefault.png",
	desc_anteprima varchar(256) not null default "Immagine di default",
	indirizzo varchar(128) not null,
	citta varchar(128) not null,
	host int not null,
	stato_approvazione tinyint(1) not null default 0, -- 0 = NVNA / VA = 1 / VNA = 2 (per le sigle guardare analisirequisiti.md)
	max_ospiti int(2) not null default 1, -- limite da 0 a 99 (almeno da db)
	prezzo_notte float not null,
	foreign key (host) references utenti(id_utente) on update cascade on delete cascade
);

create table prenotazioni (
	id_prenotazione int primary key auto_increment,
	utente int not null,
	annuncio int,
	num_ospiti int(2) not null default 1, -- limite da 0 a 99 (almeno da db)
	data_inizio date not null,
	data_fine date not null,
	foreign key (utente) references utenti(id_utente) on update cascade on delete cascade,
	foreign key (annuncio) references annunci(id_annuncio) on update cascade on delete cascade
);

create table commenti (
	prenotazione int primary key, -- prenotazione=prenotazione
	data_pubblicazione datetime DEFAULT CURRENT_TIMESTAMP,
	titolo varchar(64) not null,
	commento varchar(512) not null,
	votazione tinyint(1) not null, -- verificare via trigger che sia 0 < voto < 6
	foreign key(prenotazione) references prenotazioni(id_prenotazione) on update cascade on delete cascade
);

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

INSERT INTO `amministratori` (`id_amministratore`, `user_name`, `password`, `mail`) VALUES
(1, 'admin', 'admin', 'admin@gmail.com');

INSERT INTO `utenti` (`id_utente`, `nome`, `cognome`, `user_name`, `mail`, `password`, `data_nascita`, `img_profilo`, `telefono`) VALUES
(1, 'Jolanda', 'Rossi', 'jolandarossi', 'jolanda.rossi@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(2, 'Melchiorre', 'Ferrari', 'melchiorreferrari', 'melchiorre.ferrari@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(3, 'Cirino', 'Russo', 'cirinorusso', 'cirino.russo@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(4, 'Ignazio', 'Bianchi', 'ignaziobianchi', 'ignazio.bianchi@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(5, 'Elio', 'Romano', 'elioromano', 'elio.romano@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(6, 'Gianni', 'Gallo', 'giannigallo', 'gianni.gallo@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(7, 'Amore', 'Costa', 'amorecosta', 'amore.costa@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(8, 'Marisa', 'Fontana', 'marisafontana', 'marisa.fontana@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(9, 'Dania', 'Conti', 'daniaconti', 'dania.conti@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(10, 'Alberico', 'Esposito', 'albericoesposito', 'alberico.esposito@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(11, 'Lodovico', 'Ricci', 'lodovicoricci', 'lodovico.ricci@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(12, 'Marino', 'Bruno', 'marinobruno', 'marino.bruno@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(13, 'Nella', 'Rizzo', 'nellarizzo', 'nella.rizzo@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(14, 'Pompeo', 'Moretti', 'pompeomoretti', 'pompeo.moretti@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(15, 'Alfonso', 'Marino', 'alfonsomarino', 'alfonso.marino@mail.it', 'password', '1998-01-01', 'defaultImages/imgProfiloDefault.png', '+390000000000'),
(16, 'Marco', 'Ferrati', 'mferrati', 'user@gmail.com', 'user', '1998-02-08', 'defaultImages/imgProfiloDefault.png', '+391234567890'),
(17, 'Utente', 'Generico', 'genericUser', 'utente.generico@mail.com', 'password', '1992-05-02', 'defaultImages/imgProfiloDefault.png', '3401234567'),
(18, 'Tommaso', 'Azzalin', 'tazzalin', 'tommaso.azzalin@studenti.unipd.it', 'tommaso', '1998-05-22', 'user18/imgProfilo18.png', '1234567890');


INSERT INTO `annunci` (`id_annuncio`, `titolo`, `descrizione`, `img_anteprima`, `desc_anteprima`, `indirizzo`, `citta`, `host`, `stato_approvazione`, `max_ospiti`, `prezzo_notte`) VALUES
(1, 'Casa Loreto', 'Accogliente casa in riva al mare con una vista fantastica. Molto vicina al centro città e ai negozzi dove è possibile fare shopping.', 'user4/annuncio1.jpg', 'Stanza con un tavolo, due finestre, separate da una colonna', 'Via dell\'Accoglienza 10', 'Partinico', 4, 1, 2, 128),
(2, 'Casa Agrippa', 'Casa in aperta campagna, ottimo luogo dove riposarsi nei fine settiman. Consigliata soprattutto alle coppie', 'user9/annuncio2.jpg', 'Sala da pranzo con zona cucina e zona soggiorno. Una televisione a destra e una porta in fondo a sinistra.', 'Via Galaverna 8', 'Gressan', 9, 0, 3, 30),
(3, 'Casa Celeste', 'Casa in montagna a 2 metri dalle piste da sci. Otiima per passare la settimana bianca.', 'user10/annuncio3.jpg', 'Stanza con un divano a due posti, uno specchio e  una piccola tv.', 'Via Comune Catanzaro 1', 'Fasano', 10, 1, 1, 118),
(4, 'Casa Aquila', 'Casa accogliente in centro città a due passi dalla metro. Molto vicina al nuovisissimo MCDonald.', 'user1/annuncio4.jpg', 'Casa a due piani con giardino.', 'Via Casa Comunale', 'Cesena', 1, 1, 1, 83),
(5, 'Casa Amore', 'Casa Amore, un nome una garanzia. Il vostro soggiorno sarà molto appagante.', 'user9/annuncio5.jpg', 'Casa ad un unico piano con un garage ed un ampio giardino ben curato.', 'Via dell\'Olmo 1', 'Taranto', 9, 0, 4, 93),
(6, 'Appartamento Aran', 'Appartamentino in centro città a due metri dalla stazione dei treni e dall\'aereoporto.', 'user4/annuncio6.jpg', 'Vista esterna di una casa a due piani con un garage e un giardino ben curato.', 'Via del Palazzo Comunale 2', 'Cuneo', 4, 0, 1, 40),
(7, 'Appartamento Flann', 'Un appartamentino molto accogliente nel quale sono ben accetti animali di tutte le taglie.', 'user12/annuncio7.jpg', 'Sala da pranzo con un tavolino, 4 sedie, un divanetto e una televisione sopra un mobile.', 'Via del Palazzo Comunale 3', 'Reggio Calabria', 12, 1, 3, 149),
(8, 'Appartamento Glaucia', 'Appartamento dalle nobili origini ristrutturato per renderlo un posticino accogliente e pragmatico.', 'user11/annuncio8.jpg', 'Sala da pranzo con cucina, un tavolo con una panca e due sedie. A destra è presente un corridoio.', 'Via del Comune 8', 'Cosenza', 11, 1, 3, 143),
(9, 'Appartamento Nollaig', 'Appartemento vista mare con spiaggia annesse, venite da noi a godervi delle bellissime giornate di solo.', 'user12/annuncio9.jpg', 'Cucina con un tavolo ed un divano sttaccato alla parete. Sulla sinistra c\'è una porta finestra.', 'Via dell\'Orologio 10', 'Cesenatico', 12, 1, 5, 73),
(10, 'Appartamento Amore', 'Appartamento dove l\'amore è di casa.', 'user9/annuncio10.jpg', 'Appartamento visto da fuori con un balconcino con delle fioriere. Appartamento a due piani.', 'Via del Domicilio 11', 'Cogoleto', 9, 0, 3, 35),
(12, 'Appartamento Torre Archimede', 'Un appartamento in zona universitaria ottimo per studenti che vogliono andarsi a prendere uno spritz in compagnia. Ottimo anche per studenti in sessione.', 'user16/annuncio12.jpg', 'Vista di Torre Archimede, un edificio formato da 4 torri collegate da uffici e aule.', 'Via Trieste, 63', 'Padova', 16, 1, 5, 20),
(13, 'Alloggio ASTA', 'Quando non sai cosa fare oppure non hai una casa, vieni nell\'alloggio ASTA! Ti sentirai a tuo agio per tutto il soggiorno. Un po\' meno se sei in vacanza e devi dare esami, ma questo non ci interessa. Sarò lieto di accoglierti!', 'user18/annuncio13.jpg', 'Ingresso di alloggio ASTA, con vista su studente studiante.', 'Via Trieste, 63', 'Padova', 18, 1, 80, 50.5);

INSERT INTO `prenotazioni` (`id_prenotazione`, `utente`, `annuncio`, `num_ospiti`, `data_inizio`, `data_fine`) VALUES
(1, 1, 1, 1, '2020-02-02', '2020-02-07'),
(2, 2, 1, 1, '2020-02-11', '2020-02-15'),
(3, 3, 1, 1, '2020-02-21', '2020-02-25'),
(4, 9, 2, 1, '2020-02-14', '2020-02-19'),
(5, 5, 3, 1, '2020-02-07', '2020-02-09'),
(6, 6, 3, 1, '2020-02-14', '2020-02-16'),
(7, 6, 3, 1, '2020-02-28', '0000-00-00'),
(8, 7, 4, 1, '2020-02-04', '2020-02-07'),
(9, 9, 4, 1, '2020-02-19', '2020-02-22'),
(10, 4, 6, 1, '2020-02-01', '0000-00-00'),
(11, 9, 7, 1, '2020-02-04', '2020-02-07'),
(12, 10, 8, 1, '2020-02-10', '2020-02-13'),
(13, 11, 8, 1, '2020-02-19', '2020-02-23'),
(14, 12, 9, 1, '2020-02-06', '2020-02-09'),
(15, 13, 9, 1, '2020-02-19', '2020-02-22'),
(16, 12, 9, 1, '2020-02-24', '2020-02-26'),
(17, 14, 10, 1, '2020-02-02', '2020-02-05'),
(18, 14, 10, 1, '2020-02-13', '2020-02-17'),
(19, 16, 9, 1, '2020-03-02', '2020-03-08'),
(20, 16, 9, 1, '2020-01-01', '2020-01-04'),
(21, 16, 9, 1, '2020-01-23', '2020-01-25'),
(22, 16, 7, 1, '2020-03-27', '2020-03-29'),
(23, 18, 7, 1, '2020-01-26', '2020-01-28');

INSERT INTO `commenti` (`prenotazione`, `data_pubblicazione`, `titolo`, `commento`, `votazione`) VALUES
(2, '2020-02-10 09:21:00', 'Soggiorno molto bello', 'Il soggiorno è stato molto piacevole, il cane del propietario è vermente simpatico', 4),
(3, '2020-02-10 09:21:00', 'Un po\' meh come cosa', 'Ho visto case meglio tenute ma tutto sommato non è stato male', 3),
(5, '2020-02-10 09:21:00', 'Fantastico', 'Un soggiorno veramente fantastico', 3),
(6, '2020-02-10 09:21:00', 'Indimenticabile soggiorno', 'Mi sono divertito moltissio surante il periodo in cui ho soggiornato qui', 3),
(7, '2020-02-10 09:21:00', 'Poteva andare meglio', 'Non c\'era l\'acqua calda', 2),
(8, '2020-02-10 09:21:00', 'Ottimo soggiorno', 'Whoa un soggiorno luogo così accogliente è difficile da trovare in giro', 5),
(9, '2020-02-10 09:21:00', 'Propietari molto scortesi', 'Non mi hanno fatto trovare il giardino in ordine quando sono arrivato', 2),
(11, '2020-02-10 09:21:00', 'Incredibile!!!', 'Un soggiorno così difficilmente lo si può dimenticare. Verrò anche l\'anno prossimo', 5),
(13, '2020-02-10 09:21:00', 'Bello il locale ma la zona...', '...non è tutto questo granchè, infatti non c\'è nemmeno un supermercato', 2),
(14, '2020-02-10 09:21:00', 'Whoa!!!!!', 'All\'arrivo sono rimasto senza fiato', 5),
(15, '2020-02-10 09:21:00', 'Soggiorno molto bello', 'Il soggiorno è stato molto piacevole, consiglio assolutamente la visita', 3),
(16, '2020-02-10 09:21:00', 'Soggiorno da non credere', 'Il primo girono il proprietario ci ha portato una crostata che era la fine del mondo.', 4),
(21, '2020-01-26 00:34:08', 'Davvero un bel soggiorno', 'Tanto di cappello al proprietario', 5);

-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Gen 25, 2020 alle 20:30
-- Versione del server: 10.1.43-MariaDB-0ubuntu0.18.04.1
-- Versione PHP: 7.2.24-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mferrati`
--

DELIMITER $$
--
-- Procedure
--
CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `admin_get_annunci` ()  BEGIN
    SELECT id_annuncio, titolo, stato_approvazione
    FROM annunci
    WHERE stato_approvazione = 0;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `admin_login` (`_mail` VARCHAR(191), `_password` VARCHAR(48))  BEGIN
  SELECT *
  FROM amministratori a
  WHERE a.mail = _mail
  AND a.password = _password;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_annuncio` (`id` INT)  BEGIN
    SELECT *
    FROM annunci
    WHERE id_annuncio = id;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_annunci_host` (`_id_host` INT)  BEGIN
    select *
    from annunci
    where host = _id_host;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_citta_annunci` ()  BEGIN
    SELECT DISTINCT citta
    FROM annunci
    WHERE stato_approvazione = 1;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_commenti_annuncio` (`id` INT)  BEGIN
    SELECT C.*, U.user_name, U.img_profilo 
    FROM occupazioni O INNER JOIN commenti C ON O.id_occupazione = C.prenotazione
    INNER JOIN utenti U ON O.utente = U.id_utente
    WHERE O.annuncio = id;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_occupazione` (`id` INT)  BEGIN
    SELECT *
    FROM occupazioni
    WHERE id_occupazione = id;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_occupazioni_annuncio` (`_id_annuncio` INT)  BEGIN
    SELECT id_occupazione, utente, num_ospiti, data_inizio, data_fine
    FROM occupazioni
    WHERE annuncio = _id_annuncio
    ORDER BY data_inizio;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_prenotazioni_guest` (`id_utente` INT)  BEGIN
    SELECT *
    FROM occupazioni
    WHERE utente = id_utente
    ORDER BY occupazioni.data_inizio;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_ultimi_annunci_approvati` (`id_utente` INT)  BEGIN
    IF(id_utente = -1) THEN
        SELECT id_annuncio, titolo, img_anteprima, desc_anteprima
        FROM annunci
        WHERE stato_approvazione=1
        ORDER BY id_annuncio DESC
        LIMIT 6;
    ELSE
        SELECT id_annuncio, titolo, img_anteprima, desc_anteprima
        FROM annunci
        WHERE stato_approvazione=1 and annunci.host!= id_utente
        ORDER BY id_annuncio DESC
        LIMIT 6;
    END IF;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `get_user` (`id` INT)  BEGIN
    SELECT *
    FROM utenti
    WHERE id_utente = id;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `login` (`_mail` VARCHAR(191), `_password` VARCHAR(48))  BEGIN
  SELECT *
  FROM utenti u
  WHERE u.mail = _mail
  AND u.password = _password;
END$$

CREATE DEFINER=`mferrati`@`localhost` PROCEDURE `ricerca_annunci` (`_citta` VARCHAR(128), `_num_ospiti` INT(2), `di` DATE, `df` DATE)  BEGIN
    SELECT A.id_annuncio, A.titolo, A.descrizione, A.img_anteprima, A.desc_anteprima, A.indirizzo, A.prezzo_notte
    FROM annunci A
    WHERE A.stato_approvazione = 1 AND A.citta like _citta
    AND A.max_ospiti >= _num_ospiti
    AND A.id_annuncio NOT IN (
        SELECT annuncio
        FROM occupazioni
        WHERE (
          (di >= data_inizio AND di <= data_fine) OR
          (df >= data_inizio AND df <= data_fine) OR
          (di <= data_inizio AND df >= data_fine) OR
          (di >= data_inizio AND df <= data_fine)
        )
    );
END$$

--
-- Funzioni
--
CREATE DEFINER=`mferrati`@`localhost` FUNCTION `admin_edit_stato_approvazione_annuncio` (`_id` INT, `_stato` TINYINT(1)) RETURNS INT(11) BEGIN
    
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
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `delete_annuncio` (`_id_annuncio` INT) RETURNS INT(11) BEGIN
  DECLARE curdate DATE;

  DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        RETURN -2;
    END;

  SET curdate = CURDATE();
  IF _id_annuncio IN (SELECT annuncio
                      FROM occupazioni
                      WHERE (data_inizio <= curdate AND data_fine >= curdate) OR data_inizio > curdate) THEN
    RETURN -1;
  END IF;

  DELETE FROM commenti
  WHERE prenotazione IN ( SELECT id_occupazione
                          FROM occupazioni
                          WHERE annuncio = _id_annuncio);
  
  DELETE FROM annunci WHERE id_annuncio = _id_annuncio;

  IF ROW_COUNT() = 0 THEN
    RETURN -2;
  ELSE
      RETURN 0;
  END IF;
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `delete_commento` (`_id_prenotazione` INT) RETURNS INT(11) BEGIN
    delete from commenti where prenotazione = _id_prenotazione;
    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN 0;
    END IF;
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `delete_occupazione` (`_id_occupazione` INT) RETURNS INT(11) BEGIN
  DECLARE d_inizio date;

  SELECT data_inizio INTO d_inizio
  FROM occupazioni
  WHERE id_occupazione = _id_occupazione;

  IF CURDATE() >= d_inizio THEN
    RETURN -1;
  END IF;

  DELETE FROM occupazioni
  WHERE id_occupazione = _id_occupazione;

  IF ROW_COUNT() = 0 THEN
    RETURN -2;
  ELSE
    RETURN 0;
  END IF;
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `delete_user` (`_id_utente` INT) RETURNS INT(11) BEGIN


IF EXISTS (
    SELECT * FROM occupazioni WHERE utente = _id_utente AND (data_inizio <= CURDATE() AND data_fine >= CURDATE())
  ) THEN
  RETURN -1;
END IF;


IF EXISTS (
  SELECT * FROM occupazioni WHERE annuncio IN (
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
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `edit_annuncio` (`_id` INT, `_titolo` VARCHAR(32), `_descrizione` VARCHAR(512), `_img_anteprima` VARCHAR(48), `_desc_anteprima` VARCHAR(256), `_indirizzo` VARCHAR(128), `_citta` VARCHAR(128), `_max_ospiti` TINYINT(2), `_prezzo_notte` FLOAT) RETURNS INT(11) BEGIN
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
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `edit_commento` (`_id` INT, `_titolo` VARCHAR(64), `_commento` VARCHAR(512), `_valutazione` TINYINT(1)) RETURNS INT(11) BEGIN
    update commenti
    set commenti.titolo = _titolo, commenti.commento= _commento, commenti.votazione= _valutazione
    where  commenti.prenotazione= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `edit_user` (`_id_utente` INT, `_nome` VARCHAR(32), `_cognome` VARCHAR(32), `_username` VARCHAR(32), `_mail` VARCHAR(255), `_password` VARCHAR(48), `_datanascita` DATE, `_imgprofilo` VARCHAR(48), `_telefono` VARCHAR(18)) RETURNS INT(11) BEGIN
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
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `insert_annuncio` (`_titolo` VARCHAR(32), `_descrizione` VARCHAR(512), `_img_anteprima` VARCHAR(48), `_desc_anteprima` VARCHAR(256), `_indirizzo` VARCHAR(128), `_citta` VARCHAR(128), `_host` INT, `_max_ospiti` TINYINT(2), `_prezzo_notte` FLOAT) RETURNS INT(11) BEGIN
    
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
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `insert_commento` (`_prenotazione` INT, `_titolo` VARCHAR(64), `_commento` VARCHAR(512), `_votazione` TINYINT(1)) RETURNS INT(11) BEGIN
    
    DECLARE EXIT HANDLER FOR 1452
    BEGIN
      RETURN -1;
    END;

    
    IF EXISTS(
        SELECT *
        FROM commenti
        WHERE prenotazione = _prenotazione
    ) THEN
        RETURN -2;
    END IF;

    INSERT INTO commenti(prenotazione, titolo, commento, votazione) VALUES
      (_prenotazione, _titolo, _commento, _votazione);

      
      IF ROW_COUNT() = 0 THEN
          RETURN -4;
      ELSE
          RETURN _prenotazione; 
      END IF;
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `insert_occupazione` (`_utente` INT, `_annuncio` INT, `_numospiti` INT(2), `di` DATE, `df` DATE) RETURNS INT(11) BEGIN
    
    IF DATEDIFF(df, di) <= 0 THEN
      RETURN -1;
    END IF;

    
    IF EXISTS (
        SELECT *
        FROM occupazioni
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

    INSERT INTO occupazioni(utente, annuncio, num_ospiti, data_inizio, data_fine)
    VALUES (_utente, _annuncio, _numospiti, di, df);

    IF ROW_COUNT() = 0 THEN 
        RETURN -3;
    ELSE
        RETURN LAST_INSERT_ID();
    END IF;
END$$

CREATE DEFINER=`mferrati`@`localhost` FUNCTION `registrazione` (`_nome` VARCHAR(32), `_cognome` VARCHAR(32), `_username` VARCHAR(32), `_mail` VARCHAR(191), `_password` VARCHAR(48), `_data` DATE, `_img_profilo` VARCHAR(48), `_telefono` VARCHAR(18)) RETURNS INT(11) BEGIN
  
  IF EXISTS(SELECT * FROM utenti WHERE mail = _mail) THEN
    RETURN -2;
  END IF;

  INSERT INTO utenti(nome, cognome, user_name, mail, password, data_nascita, img_profilo, telefono) VALUES (_nome, _cognome, _username, _mail, _password, _data, _img_profilo, _telefono);

  IF ROW_COUNT() = 0 THEN
      RETURN -1;
  ELSE
      RETURN LAST_INSERT_ID();
  END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `amministratori`
--

CREATE TABLE `amministratori` (
  `id_amministratore` int(11) NOT NULL,
  `user_name` varchar(32) NOT NULL,
  `password` varchar(48) NOT NULL,
  `mail` varchar(191) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `amministratori`
--

INSERT INTO `amministratori` (`id_amministratore`, `user_name`, `password`, `mail`) VALUES
(1, 'admin', 'admin', 'admin@gmail.com');

-- --------------------------------------------------------

--
-- Struttura della tabella `annunci`
--

CREATE TABLE `annunci` (
  `id_annuncio` int(11) NOT NULL,
  `titolo` varchar(32) NOT NULL,
  `descrizione` varchar(512) NOT NULL,
  `img_anteprima` varchar(48) NOT NULL DEFAULT 'defaultImages/imgProfiloDefault.png',
  `desc_anteprima` varchar(256) NOT NULL DEFAULT 'Immagine di default',
  `indirizzo` varchar(128) NOT NULL,
  `citta` varchar(128) NOT NULL,
  `host` int(11) NOT NULL,
  `stato_approvazione` tinyint(1) NOT NULL DEFAULT '0',
  `max_ospiti` int(2) NOT NULL DEFAULT '1',
  `prezzo_notte` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `annunci`
--

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
(12, 'Appartamento Torre Archimede', 'Un appartamento in zona universitaria ottimo per studenti che vogliono andarsi a prendere uno spritz in compagnia.', 'user16/annuncio12.jpg', 'Vista di Torre Archimede, un edificio formato da 4 torri collegate da uffici e aule.', 'Via Trieste, 63', 'Padova', 16, 1, 5, 20);

-- --------------------------------------------------------

--
-- Struttura della tabella `commenti`
--

CREATE TABLE `commenti` (
  `prenotazione` int(11) NOT NULL,
  `data_pubblicazione` datetime DEFAULT CURRENT_TIMESTAMP,
  `titolo` varchar(64) NOT NULL,
  `commento` varchar(512) NOT NULL,
  `votazione` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `commenti`
--

INSERT INTO `commenti` (`prenotazione`, `data_pubblicazione`, `titolo`, `commento`, `votazione`) VALUES
(1, '2020-02-10 09:21:00', 'Soggiorno non troppo piacevole', 'Il soggiorno non è stato dei migliori. Non credo ci tornerò', 2),
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
(16, '2020-02-10 09:21:00', 'Soggiorno da non credere', 'Il primo girono il proprietario ci ha portato una crostata che era la fine del mondo.', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `occupazioni`
--

CREATE TABLE `occupazioni` (
  `id_occupazione` int(11) NOT NULL,
  `utente` int(11) NOT NULL,
  `annuncio` int(11) DEFAULT NULL,
  `num_ospiti` int(2) NOT NULL DEFAULT '1',
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `occupazioni`
--

INSERT INTO `occupazioni` (`id_occupazione`, `utente`, `annuncio`, `num_ospiti`, `data_inizio`, `data_fine`) VALUES
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
(22, 16, 7, 1, '2020-03-27', '2020-03-29');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id_utente` int(11) NOT NULL,
  `nome` varchar(32) NOT NULL,
  `cognome` varchar(32) NOT NULL,
  `user_name` varchar(32) NOT NULL,
  `mail` varchar(191) NOT NULL,
  `password` varchar(48) NOT NULL,
  `data_nascita` date NOT NULL,
  `img_profilo` varchar(48) NOT NULL DEFAULT 'defaultImages/imgAnteprimaAnnuncioDefault.png',
  `telefono` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id_utente`, `nome`, `cognome`, `user_name`, `mail`, `password`, `data_nascita`, `img_profilo`, `telefono`) VALUES
(1, 'Jolanda', 'Rossi', 'jolanda.rossi', 'jolanda.rossi@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(2, 'Melchiorre', 'Ferrari', 'melchiorre.ferrari', 'melchiorre.ferrari@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(3, 'Cirino', 'Russo', 'cirino.russo', 'cirino.russo@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(4, 'Ignazio', 'Bianchi', 'ignazio.bianchi', 'ignazio.bianchi@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(5, 'Elio', 'Romano', 'elio.romano', 'elio.romano@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(6, 'Gianni', 'Gallo', 'gianni.gallo', 'gianni.gallo@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(7, 'Amore', 'Costa', 'amore.costa', 'amore.costa@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(8, 'Marisa', 'Fontana', 'marisa.fontana', 'marisa.fontana@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(9, 'Dania', 'Conti', 'dania.conti', 'dania.conti@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(10, 'Alberico', 'Esposito', 'alberico.esposito', 'alberico.esposito@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(11, 'Lodovico', 'Ricci', 'lodovico.ricci', 'lodovico.ricci@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(12, 'Marino', 'Bruno', 'marino.bruno', 'marino.bruno@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(13, 'Nella', 'Rizzo', 'nella.rizzo', 'nella.rizzo@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(14, 'Pompeo', 'Moretti', 'pompeo.moretti', 'pompeo.moretti@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(15, 'Alfonso', 'Marino', 'alfonso.marino', 'alfonso.marino@mail.it', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(16, 'Marco', 'Ferrati', 'mferrati', 'user@gmail.com', 'user', '2020-02-08', '../uploads/defaultImages/imgProfiloDefault.png', '+391234567890'),
(17, 'Utente', 'Generico', 'genericUser', 'utente.generico@mail.com', 'password', '1992-05-02', 'defaultImages/imgProfiloDefault.png', '3401234567');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `amministratori`
--
ALTER TABLE `amministratori`
  ADD PRIMARY KEY (`id_amministratore`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Indici per le tabelle `annunci`
--
ALTER TABLE `annunci`
  ADD PRIMARY KEY (`id_annuncio`),
  ADD KEY `host` (`host`);

--
-- Indici per le tabelle `commenti`
--
ALTER TABLE `commenti`
  ADD PRIMARY KEY (`prenotazione`);

--
-- Indici per le tabelle `occupazioni`
--
ALTER TABLE `occupazioni`
  ADD PRIMARY KEY (`id_occupazione`),
  ADD KEY `utente` (`utente`),
  ADD KEY `annuncio` (`annuncio`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id_utente`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `amministratori`
--
ALTER TABLE `amministratori`
  MODIFY `id_amministratore` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT per la tabella `annunci`
--
ALTER TABLE `annunci`
  MODIFY `id_annuncio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT per la tabella `occupazioni`
--
ALTER TABLE `occupazioni`
  MODIFY `id_occupazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `annunci`
--
ALTER TABLE `annunci`
  ADD CONSTRAINT `annunci_ibfk_1` FOREIGN KEY (`host`) REFERENCES `utenti` (`id_utente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `commenti`
--
ALTER TABLE `commenti`
  ADD CONSTRAINT `commenti_ibfk_1` FOREIGN KEY (`prenotazione`) REFERENCES `occupazioni` (`id_occupazione`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `occupazioni`
--
ALTER TABLE `occupazioni`
  ADD CONSTRAINT `occupazioni_ibfk_1` FOREIGN KEY (`utente`) REFERENCES `utenti` (`id_utente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `occupazioni_ibfk_2` FOREIGN KEY (`annuncio`) REFERENCES `annunci` (`id_annuncio`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

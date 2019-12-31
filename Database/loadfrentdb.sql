-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 30, 2019 alle 18:23
-- Versione del server: 10.4.8-MariaDB
-- Versione PHP: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `frentdb`
--

DELIMITER $$
--
-- Procedure
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `admin_get_annunci` ()  BEGIN
    SELECT id_annuncio, titolo, stato_approvazione
    FROM annunci
    WHERE stato_approvazione = 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `admin_login` (`_admin` VARCHAR(191), `_password` VARCHAR(48))  BEGIN
  SELECT *
  FROM amministratori a
  WHERE (a.user_name = _admin OR a.mail = _admin)
  AND a.password = _password;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_annuncio` (`id` INT)  BEGIN
    SELECT *
    FROM annunci
    WHERE id_annuncio = id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_annunci_host` (`_id_host` INT)  BEGIN
    select *
    from annunci
    where host = _id_host;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_citta_annunci` ()  BEGIN
    SELECT DISTINCT citta
    FROM annunci
    WHERE stato_approvazione = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_commenti_annuncio` (`id` INT)  BEGIN
    SELECT C.*, U.user_name, U.img_profilo 
    FROM occupazioni O INNER JOIN commenti C ON O.id_occupazione = C.prenotazione
    INNER JOIN utenti U ON O.utente = U.id_utente
    WHERE O.annuncio = id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_occupazione` (`id` INT)  BEGIN
    SELECT *
    FROM occupazioni
    WHERE id_occupazione = id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_occupazioni_annuncio` (`_id_annuncio` INT)  BEGIN
    SELECT id_occupazione, utente, num_ospiti, data_inizio, data_fine
    FROM occupazioni
    WHERE annuncio = _id_annuncio
    ORDER BY data_inizio;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_prenotazioni_guest` (`id_utente` INT)  BEGIN
    SELECT *
    FROM occupazioni
    WHERE utente = id_utente
    ORDER BY occupazioni.data_inizio;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_ultimi_annunci_approvati` (`id_utente` INT)  BEGIN
    SELECT id_annuncio, titolo, img_anteprima, desc_anteprima
    FROM annunci
    WHERE stato_approvazione=1 and annunci.host!= id_utente
    ORDER BY id_annuncio DESC
    LIMIT 6;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user` (`id` INT)  BEGIN
    SELECT *
    FROM utenti
    WHERE id_utente = id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `login` (IN `_mail` VARCHAR(191), IN `_password` VARCHAR(48))  BEGIN
    SELECT *
    FROM utenti u
    WHERE u.mail = _mail
      AND u.password = _password;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ricerca_annunci` (`_citta` VARCHAR(128), `_num_ospiti` INT(2), `di` DATE, `df` DATE)  BEGIN
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
CREATE DEFINER=`root`@`localhost` FUNCTION `admin_edit_stato_approvazione_annuncio` (`_id` INT, `_stato` TINYINT(1)) RETURNS INT(11) BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `delete_annuncio` (`_id_annuncio` INT) RETURNS INT(11) BEGIN
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
    -- in occupazioni il campo annuncio viene messo a null dalla politica di reazione
    DELETE FROM annunci WHERE id_annuncio = _id_annuncio;

    IF ROW_COUNT() = 0 THEN
        RETURN -2;
    ELSE
        RETURN 0;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `delete_commento` (`_id_prenotazione` INT) RETURNS INT(11) BEGIN
    delete from commenti where prenotazione = _id_prenotazione;
    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN 0;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `delete_occupazione` (`_id_occupazione` INT) RETURNS INT(11) BEGIN
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

CREATE DEFINER=`root`@`localhost` FUNCTION `delete_user` (`_id_utente` INT) RETURNS INT(11) BEGIN

-- Abort in caso di occupazioni correnti
IF EXISTS (
    SELECT * FROM occupazioni WHERE utente = _id_utente AND (data_inizio <= CURDATE() AND data_fine >= CURDATE())
  ) THEN
  RETURN -1;
END IF;

-- Abort in caso di annunci con occupazioni future o in corso
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

CREATE DEFINER=`root`@`localhost` FUNCTION `edit_annuncio` (`_id` INT, `_titolo` VARCHAR(32), `_descrizione` VARCHAR(512), `_img_anteprima` VARCHAR(48), `_indirizzo` VARCHAR(128), `_citta` VARCHAR(128), `_max_ospiti` TINYINT(2), `_prezzo_notte` FLOAT) RETURNS INT(11) BEGIN
    update annunci
    set annunci.titolo = _titolo, annunci.descrizione= _descrizione , annunci.indirizzo=_indirizzo,
        annunci.img_anteprima= _img_anteprima, annunci.citta= _citta, annunci.max_ospiti= _max_ospiti,
        annunci.prezzo_notte =_prezzo_notte, stato_approvazione = 0
    where  annunci.id_annuncio= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `edit_commento` (`_id` INT, `_titolo` VARCHAR(64), `_commento` VARCHAR(512), `_valutazione` TINYINT(1)) RETURNS INT(11) BEGIN
    update commenti
    set commenti.titolo = _titolo, commenti.commento= _commento, commenti.votazione= _valutazione
    where  commenti.prenotazione= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `edit_user` (`_id_utente` INT, `_nome` VARCHAR(32), `_cognome` VARCHAR(32), `_username` VARCHAR(32), `_mail` VARCHAR(255), `_password` VARCHAR(48), `_datanascita` DATE, `_imgprofilo` VARCHAR(48), `_telefono` VARCHAR(18)) RETURNS INT(11) BEGIN
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

CREATE DEFINER=`root`@`localhost` FUNCTION `insert_annuncio` (`_titolo` VARCHAR(32), `_descrizione` VARCHAR(512), `_img_anteprima` VARCHAR(48), `_indirizzo` VARCHAR(128), `_citta` VARCHAR(128), `_host` INT, `_max_ospiti` TINYINT(2), `_prezzo_notte` FLOAT) RETURNS INT(11) BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `insert_commento` (`_prenotazione` INT, `_titolo` VARCHAR(64), `_commento` VARCHAR(512), `_votazione` TINYINT(1)) RETURNS INT(11) BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `insert_occupazione` (`_utente` INT, `_annuncio` INT, `_numospiti` INT(2), `di` DATE, `df` DATE) RETURNS INT(11) BEGIN
    -- controllo correttezza delle date
    IF DATEDIFF(df, di) <= 0 THEN
      RETURN -1;
    END IF;

    -- Controllo presenza altre occupazioni
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

    IF ROW_COUNT() = 0 THEN -- Modifica non effettuata
        RETURN -3;
    ELSE
        RETURN LAST_INSERT_ID();
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `registrazione` (`_nome` VARCHAR(32), `_cognome` VARCHAR(32), `_username` VARCHAR(32), `_mail` VARCHAR(191), `_password` VARCHAR(48), `_data` DATE, `_img_profilo` VARCHAR(48), `_telefono` VARCHAR(18)) RETURNS INT(11) BEGIN
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
  `user_name` varchar(32) COLLATE utf8_bin NOT NULL,
  `password` varchar(48) COLLATE utf8_bin NOT NULL,
  `mail` varchar(191) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
  `titolo` varchar(32) COLLATE utf8_bin NOT NULL,
  `descrizione` varchar(512) COLLATE utf8_bin NOT NULL,
  `img_anteprima` varchar(48) COLLATE utf8_bin NOT NULL DEFAULT 'house_image.png',
  `indirizzo` varchar(128) COLLATE utf8_bin NOT NULL,
  `citta` varchar(128) COLLATE utf8_bin NOT NULL,
  `host` int(11) NOT NULL,
  `stato_approvazione` tinyint(1) NOT NULL DEFAULT 0,
  `max_ospiti` int(2) NOT NULL DEFAULT 1,
  `prezzo_notte` float NOT NULL,
  `desc_anteprima` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT 'descrizione anteprima'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `annunci`
--

INSERT INTO `annunci` (`id_annuncio`, `titolo`, `descrizione`, `img_anteprima`, `indirizzo`, `citta`, `host`, `stato_approvazione`, `max_ospiti`, `prezzo_notte`, `desc_anteprima`) VALUES
(1, 'Casa Loreto', 'defualt descirptio', '../immagini/borgoricco.jpg', 'Via dell\'Accoglienza x', 'roma', 11, 1, 2, 78, 'descrizione anteprima'),
(3, 'Casa Celeste', 'defualt descirptio', '../immagini/borgoricco.jpg', 'Via Comune Catanzaro (civico 1 per i senza tetto, civico 2 per i senza fissa dimora)', 'roma', 9, 1, 5, 82, 'descrizione anteprima'),
(4, 'Casa Aquila', 'defualt descirptio', '../immagini/borgoricco.jpg', 'Via Casa Comunale', 'roma', 2, 1, 1, 100, 'descrizione anteprima'),
(5, 'Casa Amore', 'defualt descirptio', '../immagini/monselice.jpg', 'Via dell\'Olmo 1', 'roma', 16, 1, 5, 60, 'descrizione anteprima'),
(6, 'Appartamento Aran', 'defualt descirptio', '../immagini/limena.jpeg', 'Via del Palazzo Comunale ', 'roma', 7, 1, 2, 128, 'descrizione anteprima'),
(7, 'Appartamento Flann', 'defualt descirptio', '../immagini/monselice.jpg', 'Via del Palazzo Comunale ', 'roma', 16, 1, 6, 136, 'descrizione anteprima'),
(8, 'Appartamento Glaucia', 'defualt descirptio', '../immagini/borgoricco.jpg', 'Via del Comune', 'roma', 16, 2, 6, 25, 'descrizione anteprima'),
(9, 'Appartamento Nollaig', 'defualt descirptio', '../immagini/borgoricco.jpg', 'Via dell\'Orologio', 'roma', 2, 1, 6, 122, 'descrizione anteprima'),
(10, 'Appartamento Amore', 'defualt descirptio', '../immagini/vicenza.jpg', 'Via del Domicilio', 'roma', 4, 1, 5, 128, 'descrizione anteprima'),
(11, 'Santa Barbara', 'casa nuova', '../immagini/padova.jpg', 'asdasioudaos', 'padova', 10, 1, 1, 0, 'descrizione anteprima'),
(12, 'Casa dei mostri\r\n', 'Casa nuova', '../immagini/borgoricco.jpg', 'via trieste', 'padova', 12, 0, 5, 12, 'descrizione anteprima');

-- --------------------------------------------------------

--
-- Struttura della tabella `commenti`
--

CREATE TABLE `commenti` (
  `prenotazione` int(11) NOT NULL,
  `data_pubblicazione` datetime DEFAULT current_timestamp(),
  `titolo` varchar(64) COLLATE utf8_bin NOT NULL,
  `commento` varchar(512) COLLATE utf8_bin NOT NULL,
  `votazione` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `commenti`
--

INSERT INTO `commenti` (`prenotazione`, `data_pubblicazione`, `titolo`, `commento`, `votazione`) VALUES
(9, '2019-12-14 16:18:39', 'la casa è orrenda', 'non mi è piaciuto per nulla soggiornare qui', 1),
(12, '2019-12-18 15:17:29', 'Estremamente bello', 'Prossima volta che saro in citta, prenotero di nuovo qui', 5),
(13, '2019-12-18 15:20:22', 'Brutto', 'Casa orribilis', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `occupazioni`
--

CREATE TABLE `occupazioni` (
  `id_occupazione` int(11) NOT NULL,
  `utente` int(11) NOT NULL,
  `annuncio` int(11) DEFAULT NULL,
  `num_ospiti` int(2) NOT NULL DEFAULT 1,
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `occupazioni`
--

INSERT INTO `occupazioni` (`id_occupazione`, `utente`, `annuncio`, `num_ospiti`, `data_inizio`, `data_fine`) VALUES
(1, 1, 1, 1, '2019-11-02', '2019-11-07'),
(2, 2, 1, 1, '2019-11-11', '2019-11-15'),
(3, 16, 1, 1, '2019-11-21', '2019-11-25'),
(4, 9, 1, 1, '2019-11-14', '2019-11-19'),
(5, 5, 3, 1, '2019-11-07', '2019-11-09'),
(6, 16, 3, 1, '2019-11-14', '2019-11-16'),
(7, 6, 3, 1, '2019-11-28', '2019-11-30'),
(8, 7, 4, 1, '2019-11-04', '2019-11-07'),
(9, 9, 4, 1, '2019-11-19', '2019-11-22'),
(10, 16, 6, 1, '2019-12-14', '2019-12-31'),
(11, 9, 7, 1, '2019-11-04', '2019-11-07'),
(12, 10, 12, 1, '2019-11-10', '2019-11-13'),
(13, 10, 12, 1, '2019-12-23', '2019-12-26'),
(14, 16, 9, 1, '2019-11-06', '2019-11-09'),
(15, 13, 9, 1, '2019-11-19', '2019-11-22'),
(16, 12, 9, 1, '2019-11-24', '2019-11-26'),
(18, 16, 10, 1, '2019-11-13', '2019-11-17'),
(19, 16, 12, 1, '2019-12-27', '2019-12-31'),
(21, 16, 12, 1, '2020-02-11', '2020-02-14'),
(22, 16, 12, 1, '2020-02-18', '2020-02-24'),
(24, 16, 12, 1, '2020-03-09', '2020-03-11'),
(25, 16, 12, 1, '2020-03-18', '2020-03-21'),
(26, 16, 12, 1, '2020-03-22', '2020-03-24'),
(27, 16, 12, 1, '2020-03-26', '2020-03-28'),
(28, 16, 12, 1, '2020-03-29', '2020-03-31'),
(29, 16, 12, 1, '2020-04-01', '2020-04-03'),
(30, 16, 12, 1, '2020-05-11', '2020-05-12'),
(31, 16, 12, 1, '2020-05-18', '2020-05-20'),
(32, 16, 12, 1, '2020-05-22', '2020-05-23'),
(33, 16, 12, 1, '2020-05-24', '2020-05-25'),
(36, 16, 12, 1, '2020-05-31', '2020-06-01'),
(37, 16, 12, 1, '2020-06-02', '2020-06-03'),
(38, 16, 12, 5, '2020-06-18', '2020-06-19'),
(41, 16, 1, 2, '2019-12-30', '2020-01-02');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id_utente` int(11) NOT NULL,
  `nome` varchar(32) COLLATE utf8_bin NOT NULL,
  `cognome` varchar(32) COLLATE utf8_bin NOT NULL,
  `mail` varchar(191) COLLATE utf8_bin NOT NULL,
  `user_name` varchar(32) COLLATE utf8_bin NOT NULL,
  `password` varchar(48) COLLATE utf8_bin NOT NULL,
  `data_nascita` date NOT NULL,
  `img_profilo` varchar(48) COLLATE utf8_bin NOT NULL DEFAULT 'user_image.png',
  `telefono` varchar(18) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id_utente`, `nome`, `cognome`, `mail`, `user_name`, `password`, `data_nascita`, `img_profilo`, `telefono`) VALUES
(1, 'Jolanda', 'Rossi', 'jolanda.rossi@mail.it', 'jolanda.rossi', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(2, 'Melchiorre', 'Ferrari', 'melchiorre.ferrari@mail.it', 'melchiorre.ferrari', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(3, 'Cirino', 'Russo', 'cirino.russo@mail.it', 'cirino.russo', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(4, 'Ignazio', 'Bianchi', 'ignazio.bianchi@mail.it', 'ignazio.bianchi', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(5, 'Elio', 'Romano', 'elio.romano@mail.it', 'elio.romano', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(6, 'Gianni', 'Gallo', 'gianni.gallo@mail.it', 'gianni.gallo', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(7, 'Amore', 'Costa', 'amore.costa@mail.it', 'amore.costa', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(8, 'Marisa', 'Fontana', 'marisa.fontana@mail.it', 'marisa.fontana', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(9, 'Dania', 'Conti', 'dania.conti@mail.it', 'dania.conti', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(10, 'Alberico', 'Esposito', 'alberico.esposito@mail.it', 'alberico.esposito', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(11, 'Lodovico', 'Ricci', 'lodovico.ricci@mail.it', 'lodovico.ricci', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(12, 'Marino', 'Bruno', 'marino.bruno@mail.it', 'marino.bruno', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(13, 'Nella', 'Rizzo', 'nella.rizzo@mail.it', 'nella.rizzo', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(14, 'Pompeo', 'Moretti', 'pompeo.moretti@mail.it', 'pompeo.moretti', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(15, 'Alfonso', 'Marino', 'alfonso.marino@mail.it', 'alfonso.marino', 'password', '1998-01-01', '../uploads/defaultImages/imgProfiloDefault.png', '+390000000000'),
(16, 'user', 'user', 'user@gmail.com', 'user', 'user', '1998-01-01', '../immagini/me.jpg', '+390000000000');

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
  ADD UNIQUE KEY `mail` (`mail`) USING BTREE;

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
  MODIFY `id_occupazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

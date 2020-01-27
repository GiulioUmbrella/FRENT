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

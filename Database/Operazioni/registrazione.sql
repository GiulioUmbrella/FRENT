/*Funzione di registrazione
PRE: _password è la password hashata tramite la funzione PHP
Se il nuovo utente è stato inserito restituisce il suo ID, -1 altrimenti
*/
DROP FUNCTION IF EXISTS registrazione;
DELIMITER |
CREATE FUNCTION registrazione(_nome varchar(32), _cognome varchar(32), _username varchar(32), _mail varchar(191), _password varchar(48), _data date, _img_profilo varchar(48), _telefono varchar(18)) RETURNS INT
BEGIN
  INSERT INTO utenti(nome, cognome, user_name, mail, password, data_nascita, img_profilo, telefono) VALUES (_nome, _cognome, _username, _mail, _password, _data, _img_profilo, _telefono);

  IF ROW_COUNT() = 0 THEN
      RETURN -1;
  ELSE
      RETURN LAST_INSERT_ID();
  END IF;
END |
DELIMITER ;

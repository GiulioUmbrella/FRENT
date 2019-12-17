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
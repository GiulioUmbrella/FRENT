/*Procedura di login per admin
PRE: utente corrisponde a mail o username dell'admin
Restituisce il record relativo all'username/email alla quale si sta provando ad accedere,
in caso non vada a buon fine, verrà restituito un empty set
*/
DROP PROCEDURE IF EXISTS login;
DELIMITER |
CREATE PROCEDURE admin_login(_admin varchar(191), _password varchar(255))
BEGIN
  SELECT *
  FROM amministratori a
  WHERE (u.user_name = _admin OR u.mail = _admin)
  AND u.password = _password;
END |
DELIMITER ;

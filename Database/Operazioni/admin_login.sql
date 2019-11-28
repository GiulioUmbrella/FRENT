/*Procedura di login per admin
PRE: utente corrisponde a mail o username dell'admin
Restituisce il record relativo all'username/email alla quale si sta provando ad accedere,
in caso non vada a buon fine, verrà restituito un empty set
*/
DROP PROCEDURE IF EXISTS admin_login;
DELIMITER |
CREATE PROCEDURE admin_login(_admin varchar(191), _password varchar(255))
BEGIN
  SELECT *
  FROM amministratori a
  WHERE (a.user_name = _admin OR a.mail = _admin)
  AND a.password = _password;
END |
DELIMITER ;

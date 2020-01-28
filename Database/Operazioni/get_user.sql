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

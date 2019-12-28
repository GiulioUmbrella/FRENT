/*Procedura per ottenere i dettagli di un'occupazione
PRE: id è l'identificativo di un annuncio
Restituisce il reocrd relativo all'ID passato come parametro
*/
DROP PROCEDURE IF EXISTS get_occupazione;
DELIMITER |
CREATE PROCEDURE get_occupazione(id int)
BEGIN
    SELECT *
    FROM occupazioni
    WHERE id_occupazione = id;
END |
DELIMITER ;
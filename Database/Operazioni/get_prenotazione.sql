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
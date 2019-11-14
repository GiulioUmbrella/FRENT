/*Procedura per ottenere i dettagli di un annuncio
PRE: id è l'identificativo di un annuncio
Restituisce il reocrd relativo all'ID passato come parametro
*/
DROP PROCEDURE IF EXISTS get_annuncio;
DELIMITER |
CREATE PROCEDURE get_annuncio(id int)
BEGIN
    SELECT *
    FROM annunci
    WHERE id_annuncio = id;
END |
DELIMITER ;

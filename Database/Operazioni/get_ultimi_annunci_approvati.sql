/*Procedura per ottenere i dettagli di un annuncio
PRE: id è l'identificativo di un annuncio
Restituisce il reocrd relativo all'ID passato come parametro
*/
DROP PROCEDURE IF EXISTS get_ultimi_annunci_approvati;
DELIMITER |
CREATE PROCEDURE get_ultimi_annunci_approvati()
BEGIN
    SELECT id_annuncio,titolo, img_anteprima
    FROM annunci
    where stato_approvazione=1
    ORDER BY id_annuncio DESC
    LIMIT 6;
END |
DELIMITER ;


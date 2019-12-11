/*Procedura per ottenere tutti gli annunci da approvare
Resituisce gli annunci non ancora approvati da 
*/
DROP PROCEDURE IF EXISTS admin_get_annunci;
DELIMITER |
CREATE PROCEDURE admin_get_annunci()
BEGIN
    SELECT id_annuncio, titolo, stato_approvazione

    FROM annunci
    WHERE stato_approvazione = 0
    OR stato_approvazione = 2;
END |
DELIMITER ;

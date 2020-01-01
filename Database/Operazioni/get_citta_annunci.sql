/*Procedura per ottenere tutte le città in cui è attivo Frent (ovvero dove ci sono annunci)
Resituisce tutte le città in cui ci sono annunci
*/
DROP PROCEDURE IF EXISTS get_citta_annunci;
DELIMITER |
CREATE PROCEDURE get_citta_annunci()
BEGIN
    SELECT DISTINCT citta
    FROM annunci
    WHERE stato_approvazione = 1;
END |
DELIMITER ;

/* Procedura per ottenere i dettagli di un annuncio
Restituisce i record degli ultimi sei annunci approvati dagli amministratori
*/
DROP PROCEDURE IF EXISTS get_ultimi_annunci_approvati;
DELIMITER |
CREATE PROCEDURE get_ultimi_annunci_approvati(id_utente int)
BEGIN
    SELECT id_annuncio, titolo, img_anteprima
    FROM annunci
    WHERE stato_approvazione=1 and annunci.host!= id_utente
    ORDER BY id_annuncio DESC
    LIMIT 6;
END |
DELIMITER ;


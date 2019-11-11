/*Procedura per ottenere tutte le foto relative ad un annucio
PRE: id è l'identificativo di un annuncio
Resituisce tutti i record relativi alle foto dell'annuncio identificato dall'ID
*/
DROP PROCEDURE IF EXISTS get_foto_annuncio;
DELIMITER |
CREATE PROCEDURE get_foto_annuncio(id int)
BEGIN
    SELECT *
    FROM foto_annunci
    WHERE annuncio = id;
END |
DELIMITER ;

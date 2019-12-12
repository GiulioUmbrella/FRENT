/*Procedura per ottenere tutt i commenti relativi ad un annucio
PRE: id è l'identificativo di un annuncio
Resituisce tutti i record relativi ai commnenti dell'annuncio identificato dall'ID
*/
DROP PROCEDURE IF EXISTS get_commenti_annuncio;
DELIMITER |
CREATE PROCEDURE get_commenti_annuncio(id int)
BEGIN
    SELECT C.*, U.user_name, U.img_profilo 
    FROM occupazioni O INNER JOIN commenti C ON O.id_occupazione = C.prenotazione
    INNER JOIN utenti U ON O.utente = U.id_utente
    WHERE O.annuncio = id;
END |
DELIMITER ;

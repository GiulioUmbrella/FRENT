/*Procedura che permette di ottenere tutte le occupazioni riguradanti un annuncio
Restituisce i record relativi alle occupazioni dell'annuncio il cui ID è passato in input
*/
DROP PROCEDURE IF EXISTS get_occupazioni_annuncio;
DELIMITER |
CREATE PROCEDURE get_occupazioni_annuncio(_id_annuncio int)
BEGIN
    SELECT id_occupazione, utente, prenotazione_guest, num_ospiti, data_inizio, data_fine
    FROM occupazioni
    WHERE annuncio = _id_annuncio
    order by data_inizio;
END |
DELIMITER ;

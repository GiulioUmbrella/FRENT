/*Procedura che permette di ottenere tutte le prenotazioni riguradanti un annuncio
Restituisce i record relativi alle prenotazioni dell'annuncio il cui ID è passato in input
*/
DROP PROCEDURE IF EXISTS get_prenotazioni_annuncio;
DELIMITER |
CREATE PROCEDURE get_prenotazioni_annuncio(_id_annuncio int)
BEGIN
    SELECT id_prenotazione, utente, num_ospiti, data_inizio, data_fine
    FROM prenotazioni
    WHERE annuncio = _id_annuncio
    ORDER BY data_inizio;
END |
DELIMITER ;

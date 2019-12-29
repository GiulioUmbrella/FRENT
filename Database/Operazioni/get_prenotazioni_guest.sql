/*Procedura per ottenere le prenotazioni effettuate da un utente (guest)
PRE: id_utente corrisponde ad un utenti.id_utente
Restituisce i record relativi alle prenotazioni di un utente che ha effettuato come guest
*/
DROP PROCEDURE IF EXISTS get_prenotazioni_guest;
DELIMITER |
CREATE PROCEDURE get_prenotazioni_guest(id_utente int)
BEGIN
    SELECT *
    FROM occupazioni
    WHERE utente = id_utente
#     AND prenotazione_guest = 1
    order by occupazioni.data_inizio;
END |
DELIMITER ;

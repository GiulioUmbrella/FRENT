/*Funzione che elimina un commento
PRE: _id è l'ID di una prenotazione
Cosa restituisce:
  0 in caso il commento venga eliminato correttamente
  -1 altrimenti
*/
DROP FUNCTION IF EXISTS delete_commento;
DELIMITER |
CREATE FUNCTION delete_commento(_id_prenotazione int) RETURNS INT
BEGIN
    delete from commenti where prenotazione = _id_prenotazione;
    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN 0;
    END IF;
END |
DELIMITER ;

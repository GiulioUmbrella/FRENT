/*Funzione che si occupa di eliminare un prenotazione previo controlli
Cosa restituisce:
  0 se l'prenotazione è stata correttamente eliminata
  -1 se l'prenotazione non è eliminabile in quanto prenotazione presente o passata
  -2 in caso l'prenotazione non sia stata eliminata (per esempio per errori nelle chiavi esterne)
*/
DROP FUNCTION IF EXISTS delete_prenotazione;
DELIMITER |
CREATE FUNCTION delete_prenotazione( _id_prenotazione int) RETURNS INT
BEGIN
  DECLARE d_inizio date;

  SELECT data_inizio INTO d_inizio
  FROM prenotazioni
  WHERE id_prenotazione = _id_prenotazione;

  IF CURDATE() >= d_inizio THEN
    RETURN -1;
  END IF;

  DELETE FROM prenotazioni
  WHERE id_prenotazione = _id_prenotazione;

  IF ROW_COUNT() = 0 THEN
    RETURN -2;
  ELSE
    RETURN 0;
  END IF;
END |
DELIMITER ;

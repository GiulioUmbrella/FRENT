/*Funzione che si occupa di eliminare un occupazione previo controlli
Cosa restituisce:
  0 se l'occupazione è stata correttamente eliminata
  -1 se l'occupazione non è eliminabile in quanto prenotazione presente o passata
  -2 in caso l'occupazione non sia stata eliminata (per esempio per errori nelle chiavi esterne)
*/
DROP FUNCTION IF EXISTS delete_occupazione;
DELIMITER |
CREATE FUNCTION delete_occupazione( _id_occupazione int) RETURNS INT
BEGIN
  DECLARE d_inzio date;

  SELECT data_inizio INTO d_inizio
  FROM occupazioni
  WHERE id_occupazione = _id_occupazione;

  IF CURDATE() >= d_inizio THEN
    RETURN -1;
  END IF

  DELETE FROM occupazioni
  WHERE id_occupazione = _id_occupazione;

  IF ROW_COUNT() = 0 THEN
    RETURN -2;
  ELSE
    RETURN 0;
  END IF;
END |
DELIMITER ;

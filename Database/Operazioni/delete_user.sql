/*Funzione per l'eliminazione di un utente
Cosa ritorna:
  0 in caso di successo altrimenti:
  -1 in caso ci siano prenotazioni correnti
  -2 in caso ci siano annuci con prenotazioni correnti o future
  -3 in caso l'operazione di delete abbia fallito (per esempio gli è stato passato un id non valido)
*/
DROP FUNCTION IF EXISTS delete_user;
DELIMITER |
CREATE FUNCTION delete_user(_id_utente int) RETURNS INT
BEGIN

-- Abort in caso di prenotazioni correnti
IF EXISTS (
    SELECT * FROM prenotazioni WHERE utente = _id_utente AND (data_inizio <= CURDATE() AND data_fine >= CURDATE())
  ) THEN
  RETURN -1;
END IF;

-- Abort in caso di annunci con prenotazioni future o in corso
IF EXISTS (
  SELECT * FROM prenotazioni WHERE annuncio IN (
    SELECT id_annuncio FROM annunci WHERE host = _id_utente) AND ((data_inizio <= CURDATE() AND data_fine >= CURDATE()) OR (data_inizio >= CURDATE())
  )
) THEN
  RETURN -2;
END IF;

DELETE FROM utenti WHERE id_utente = _id_utente;

IF ROW_COUNT() = 0 THEN
  RETURN -3;
ELSE
  RETURN 0;
END IF;
END |
DELIMITER ;

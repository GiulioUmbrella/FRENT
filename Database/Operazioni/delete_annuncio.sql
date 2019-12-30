/*Funzione che permette l'eliminazio di un annucio previo controllo
Cosa restituisce:
  0 l'annuncio è stato eliminato e con esso le foto e i commenti
  -1 l'annuncio non è eliminabile perchè ci sono prenotazioni in corso o future
  -2 l'annuncio, i commenti e le foto non sono stati eliminati (per esempio per errori nelle chiavi esterne)
*/
DROP FUNCTION IF EXISTS delete_annuncio;
DELIMITER |
CREATE FUNCTION delete_annuncio(_id_annuncio INT) RETURNS INT
BEGIN
  DECLARE curdate DATE;

  DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        RETURN -2;
    END;

  SET curdate = CURDATE();
  IF _id_annuncio IN (SELECT annuncio
                      FROM occupazioni
                      WHERE (data_inizio <= curdate AND data_fine >= curdate) OR data_inizio > curdate) THEN
    RETURN -1;
  END IF;

  DELETE FROM commenti
  WHERE prenotazione IN ( SELECT id_occupazione
                          FROM occupazioni
                          WHERE annuncio = _id_annuncio);
  -- in occupazioni il campo annuncio viene messo a null dalla politica di reazione
  DELETE FROM annunci WHERE id_annuncio = _id_annuncio;

  IF ROW_COUNT() = 0 THEN
    RETURN -2;
  ELSE
      RETURN 0;
  END IF;
END |
DELIMITER ;

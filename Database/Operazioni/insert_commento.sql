/*Funzione che permette l'inserimento di un commento ad una prenotazione
PRE: _prenotazione è l'ID di una prenotazione, gli altri parametri sono validi
Cosa restituisce:
  ID del commento appena inserito se l'inserimento è andato a buon fine
  -1 in caso di prenotazione inesistente
  -2 in caso di prenotazione già commentata
  -4 se il commento non è stato inserito (per esempio in caso di errori nelle chiavi esterne)
*/
DROP FUNCTION IF EXISTS insert_commento;
DELIMITER |
CREATE FUNCTION insert_commento(_prenotazione int, _titolo varchar(64), _commento varchar(512), _votazione tinyint(1)) RETURNS INT
BEGIN
    -- Ritorna 0 in caso di prenotazione inesistente
    DECLARE EXIT HANDLER FOR 1452
    BEGIN
      RETURN -1;
    END;

    -- Prenotazione già commentata
    IF EXISTS(
        SELECT *
        FROM commenti
        WHERE prenotazione = _prenotazione
    ) THEN
        RETURN -2;
    END IF;

    INSERT INTO commenti(prenotazione, titolo, commento, votazione) VALUES
      (_prenotazione, _titolo, _commento, _votazione);

      -- verifico che il commento sia stato inserito
      IF ROW_COUNT() = 0 THEN
          RETURN -4;
      ELSE
          RETURN _prenotazione; 
      END IF;
END |
DELIMITER ;

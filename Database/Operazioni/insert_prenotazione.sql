/*Funzione che permette la creazione di una nuova funzione controllando la validità dei dati inseriti
Cosa restituisce:
  ID dell'prenotazione appena inserita se tutto è andato a buon fine
  -1 se la data di inizio e la data di fine passate in input non sono ordinate temporalmente
  -2 se ci sono altre prenotazioni nel range di date passate in input
  -3 se l'inserimento è fallito (per esempio a causa di chiavi esterne errate)
  -4 se l'host sta tentando di prenotare presso un suo annuncio
*/
DROP FUNCTION IF EXISTS insert_prenotazione;
DELIMITER |
CREATE FUNCTION insert_prenotazione(_utente int, _annuncio int, _numospiti int(2), di date, df date) RETURNS INT
BEGIN
    -- controllo correttezza delle date
    IF DATEDIFF(df, di) <= 0 THEN
      RETURN -1;
    END IF;

    -- Controllo presenza altre prenotazioni
    IF EXISTS (
        SELECT *
        FROM prenotazioni
        WHERE annuncio = _annuncio AND (
          (di >= data_inizio AND di <= data_fine) OR
          (df >= data_inizio AND df <= data_fine) OR
          (di <= data_inizio AND df >= data_fine) OR
          (di >= data_inizio AND df <= data_fine)
        )
    ) THEN
        RETURN -2;
    END IF;

    IF _utente = (SELECT host FROM annunci WHERE id_annuncio = _annuncio) THEN
       RETURN -4;
    END IF;

    INSERT INTO prenotazioni(utente, annuncio, num_ospiti, data_inizio, data_fine)
    VALUES (_utente, _annuncio, _numospiti, di, df);

    IF ROW_COUNT() = 0 THEN -- Modifica non effettuata
        RETURN -3;
    ELSE
        RETURN LAST_INSERT_ID();
    END IF;
END |
DELIMITER ;

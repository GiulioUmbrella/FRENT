/*Procedura per la ricerca degli annunci secondo alcuni parametri
PRE: _citta e' esistente nel database, di e df sono date valide e di < df
Resituisce i record che soddisfano i criteria di ricerca e di disponibilità
*/
DROP PROCEDURE IF EXISTS ricerca_annunci;
DELIMITER |
CREATE PROCEDURE ricerca_annunci(_citta varchar(128), _num_ospiti int(2), di date, df date)
BEGIN
    SELECT A.id_annuncio, A.titolo, A.descrizione, A.img_anteprima, A.indirizzo, A.prezzo_notte
    FROM annunci A
    WHERE A.bloccato = 0 AND A.stato_approvazione = 1 AND A.citta = _citta
    AND A.max_ospiti >= _num_ospiti
    AND A.id_annuncio NOT IN (
        SELECT annuncio
        FROM occupazioni
        WHERE (
          (di >= data_inizio AND di <= data_fine) OR
          (df >= data_inizio AND df <= data_fine) OR
          (di <= data_inizio AND df >= data_fine) OR
          (di >= data_inizio AND df <= data_fine)
        )
    );
END |
DELIMITER ;

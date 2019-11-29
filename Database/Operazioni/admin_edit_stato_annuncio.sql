/*Funzione che permette la modifica dello stato di approvazione di  un annuncio
Cosa restituisce:
  ID dell'annuncio modificato
  -1 in caso di errori
*/
DROP FUNCTION IF EXISTS admin_edit_stato_annuncio;
DELIMITER |
CREATE FUNCTION admin_edit_stato_annuncio(_id int, _stato tinyint(1)) RETURNS INT
BEGIN
    -- controllo validità
    IF _stato < 0 OR _stato > 2 THEN
        RETURN -1;
    END IF;

    update annunci
    set annunci.stato_approvazione = _stato
    where annunci.id_annuncio= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END |
DELIMITER ;

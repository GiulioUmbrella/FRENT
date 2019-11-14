/*Funzione che permette la modifica di un commento
PRE: _id è l'ID di una prenotazione
Cosa restituisce:
  ID della prenotazione (e quindi del commento) modificato in caso di successo
  -1 in caso ci siano stati problemi durante l'update (per esempio qualche errore con le chiavi esterne)
*/
DROP FUNCTION IF EXISTS edit_commento;
DELIMITER |
CREATE FUNCTION edit_commento(_id int, _titolo varchar(64),_commento varchar(512), _valutazione tinyint(1)) RETURNS INT
BEGIN
    update commenti
    set commenti.titolo = _titolo, commenti.commento= _commento, commenti.votazione= _valutazione
    where  commenti.prenotazione= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END |
DELIMITER ;

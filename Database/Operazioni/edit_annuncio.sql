/*Funzione che permette la modifca di un annuncio
Cosa restituisce:
  ID dell'annuncio modificato
  -1 in caso di errori
*/
DROP FUNCTION IF EXISTS edit_annuncio;
DELIMITER |
CREATE FUNCTION edit_annuncio(_id int, _titolo varchar(32), _descrizione varchar(512),_img_anteprima varchar(48),
     _indirizzo varchar(128), _citta varchar(128),_max_ospiti tinyint(2), _prezzo_notte float) RETURNS INT
BEGIN
    update annunci
    set annunci.titolo = _titolo, annunci.descrizione= _descrizione , annunci.indirizzo=_indirizzo,
        annunci.img_anteprima= _img_anteprima, annunci.citta= _citta, annunci.max_ospiti= _max_ospiti,
        annunci.prezzo_notte =_prezzo_notte, stato_approvazione = 0
    where  annunci.id_annuncio= _id;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN _id;
    END IF;
END |
DELIMITER ;

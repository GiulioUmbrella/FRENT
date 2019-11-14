/*Funzione che si occupa di eliminare una foto relativa ad un annuncio
NB: questa funzione si occupa solo di eliminare il record nel DB non gestisce file
Cosa restituisce:
  0 in caso l'eliminazione sia avventua con successo
  -1 altrimenti
*/
DROP FUNCTION IF EXISTS delete_foto;
DELIMITER |
CREATE FUNCTION delete_foto(_id_foto INT) RETURNS INT
BEGIN
    DELETE FROM foto_annunci WHERE id_foto = _id_foto;

    IF ROW_COUNT() = 0 THEN
      RETURN -1;
    ELSE
      RETURN 0;
    END IF;
END |
DELIMITER ;

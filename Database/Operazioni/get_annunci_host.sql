/*Procedura per ottenre gli annunci inseriti da un utente (host)
Restituisce i record degli annunci creati da un utente (host)
*/
DROP PROCEDURE IF EXISTS get_annunci_host;
DELIMITER |
CREATE procedure get_annunci_host(_id_host int)
BEGIN
    select *
    from annunci
    where host = _id_host;
END |
DELIMITER ;

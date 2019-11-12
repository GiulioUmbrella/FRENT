/*Funzione per l'aggiornamento/modifica dei dati di un utente
PRE: _password è una stringa risultato dell'applicazione di una funzione di hash sulla stringa corrispondente alla password dell'utente
Cosa restituisce:
  l'ID dell'utente modificato in caso di successo
  -1 altrimenti
*/
DROP FUNCTION IF EXISTS edit_user;
DELIMITER |
CREATE FUNCTION edit_user(_id_utente int, _nome varchar(32), _cognome varchar(32), _username varchar(32), _mail varchar(255), _password varchar(48), _datanascita date, _imgprofilo varchar(48), _telefono varchar(18)) RETURNS INT
BEGIN
    UPDATE utenti
    SET utenti.nome = _nome,
    utenti.cognome = _cognome,
    utenti.user_name = _username,
    utenti.mail = _mail,
    utenti.password = _password,
    utenti.data_nascita = _datanascita,
    utenti.img_profilo = _imgprofilo,
    utenti.telefono = _telefono
    WHERE utenti.id_utente = _id_utente;

    IF ROW_COUNT() = 0 THEN
        RETURN -1;
    ELSE
        RETURN _id_utente;
    END IF;
END |
DELIMITER ;

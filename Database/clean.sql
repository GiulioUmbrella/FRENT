-- drop delle tabelle
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS amministratori, utenti, annunci, foto_annunci, occupazioni, commenti;
SET FOREIGN_KEY_CHECKS=1;

-- template comando drop
-- DROP PROCEDURE IF EXISTS nome_procedura;
-- DROP FUNCTION IF EXISTS nome_funzione;
DROP PROCEDURE IF EXISTS login;
DROP PROCEDURE IF EXISTS ricerca_annunci;
DROP PROCEDURE IF EXISTS dettagli_annuncio;
DROP PROCEDURE IF EXISTS foto_annuncio;
DROP PROCEDURE IF EXISTS commenti_annuncio;
DROP PROCEDURE IF EXISTS prenotazioni_effettuate_guest;
DROP PROCEDURE IF EXISTS modifica_commento;
DROP PROCEDURE IF EXISTS elimina_commento;
DROP PROCEDURE IF EXISTS list_annunci_host;
DROP PROCEDURE IF EXISTS modifica_annuncio;
DROP PROCEDURE IF EXISTS occupazioni_annuncio;
DROP FUNCTION IF EXISTS registrazione;
DROP FUNCTION IF EXISTS modifica_dati_utente;
DROP FUNCTION IF EXISTS eliminazione_utenza;
DROP FUNCTION IF EXISTS effettua_prenotazione;
DROP FUNCTION IF EXISTS eliminare_prenotazione;
DROP FUNCTION IF EXISTS pubblica_commento;
DROP FUNCTION IF EXISTS elimina_commento;
DROP FUNCTION IF EXISTS elimina_annuncio;
DROP FUNCTION IF EXISTS aggiungi_foto;
DROP FUNCTION IF EXISTS rimozione_foto;

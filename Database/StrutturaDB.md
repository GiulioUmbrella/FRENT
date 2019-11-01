# Lista tabelle
- **annunci** (**id_annuncio**, titolo, descrizione, img_anteprima, indirizzo, citta, _host_, stato_approvazione, max_ospiti, prezzo_notte)
- **utenti** (**id_utente**, nome, cognome, user_name, mail, password, data_nascita, img_profilo, telefono)
- **commenti** (**_prenotazione_**, data_pubblicazione, titolo, commento, votazione)
- **foto_annunci** (**id_foto**, file_path, descrizione, _annuncio_)
- **occupazioni** (**id_occupazione**, _utente_, _annuncio_, prenotazione_guest, num_ospiti, data_inizio, data_fine)
- **amministratori** (**id_amministratore**, user_name, password, mail)

# Vincoli
## Utenti
- occupazioni.utente e utenti.id_utente
- annunci.host e utenti.id_utente
## Occupazioni
- commenti.prenotazione e occupazioni.id_occupazione
## Annunci
- occupazioni.annuncio e annunci.id_annuncio
- foto_annunci.annuncio e annunci.id_annuncio

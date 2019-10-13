# Lista tabelle
- annunci (**id_annuncio**, titolo, descrizione, img_anteprima, indirizzo, citta, cap, _proprietario_, approvazione, max_ospiti, prezzo_base, prezzo_persona)
- utenti (**id_utente**, nome, cognome, user_name, mail, password, data_nascita, livello_utenza, nazionalita, img_profilo, telefono)
- commenti (**_prenotazione_**, data_pubblicazione, titolo, commento, likes, dislike, votazione}
- preferiti (**id_preferito**, _annuncio_, _utente_)
- segnalazioni (**_prenotazione_**, titolo, motivazione, data)
- foto_annunci (**id_foto**, file_path, descrizione, _annuncio_)
- indisponibilita (**id_indisponibilita**, _annuncio_, data_inizio, data_fine)
- prenotazioni (**id_prenotazione**, _prenotante_, _periodo_, num_ospiti)
- amministratori (**id_amministratore**, user_name, password, mail)

# Vincoli
## Utenti
- prenotazioni.prenotante e utenti.id_utente
- annunci.proprietario e utenti.id_utente
- preferiti.utente e utenti.id_utente
## Prenotazioni
- commenti.prenotazione e prenotazioni.id_prenotazione
- segnalazioni.prenotazioni e prenotazioni.id_prenotazione
## Annunci
- indisponibilita.annuncio e annunci.id_annuncio
- preferiti.annuncio e annunci.id_annuncio
- foto_annunci.annuncio e annunci.id_annuncio
## Indisponibilità
- prenotazioni.periodo e indisponibilita.id_indisponibilita

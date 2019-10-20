## Glossario
Termini utilizzati nella documentazione, nei commenti, nelle issue di questa repository.
**N.B.**: I termini in _corsivo_ indicano il concetto espresso dall'entità corrispondente.

# Amministratore
Figura interna all'organizzazione del sito con compiti di gestione degli _annunci_, moderazione dei _commenti_, gestione degli _utenti_. Non è considerato un _utente_ (dal punto di vista della struttura del database è un'entità distinta).

# Utente
Utilizzatore del sito. Può far parte di due diverse categorie: "proprietario" e "affittuario". Un _utente_ "proprietario" possiede almeno un _annuncio_. Un _utente_ "affittuario" effettua _prenotazioni_ in uno o più _annunci_ in periodi limitati di tempo. Vi è una sola registrazione (e quindi un'unica utenza) per entrambi i ruoli.

# Annuncio
Un _annuncio_ riguarda la disponbilità di un singolo appartamento o casa e ne contiene tutte le caratteristiche (indirizzo, numero ospiti, ecc.). Ogni _annuncio_ deve avere un solo proprietario (_utente_). L'_annuncio_ non ha una data di scadenza. Se un proprietario (_utente_) non vuole rendere disponibile in uno o più determinati periodi un _annuncio_ di sua proprietà può creare un'_indisponibilità_.

# Prenotazione
Per affittare un appartamento o casa relativo ad un _annuncio_ in un periodo limitato di tempo un _utente_ deve creare una _prenotazione_, a cui sarà collegato un periodo di _indisponibiltà_ (dell'_annuncio_ scelto).

# Indisponibilità
Un periodo di _indisponibilità_ riguarda un unico _annuncio_ e un unico periodo di tempo, con una data di inizio e una data di fine (in cui data di inizio < data di fine). In un tale periodo l'_annuncio_ non può avere più periodi di _indisponibilità_ sovrapposti (quindi due periodi di _indisponibilità_ per uno stesso _annuncio_ sono validi solo se la data di fine del primo è al massimo uguale alla data di inizio del secondo) con la conseguenza che ad un periodo di _indisponibilità_ valido può corrispondere al massimo una _prenotazione_, perchè:
1. se è relativo ad una _prenotazione_, in quel periodo la casa è occupata da un _utente_ del sito.
2. se non è relativo ad una _prenotazione_, in quel periodo il proprietario (_utente_) dell'_annuncio_ ha dichiarato che la casa o appartamento non è disponibile per _prenotazioni_.

# Commento
Un _utente_ può lasciare al massimo un _commento_ sull'_annuncio_, a partire dal giorno successivo all'ultimo giorno del suo soggiorno (periodo di _indisponibilità_ relativo alla _prenotazione_ effettuata dall'_utente_).

# Segnalazione
~~Gli utenti possono fare segnalazioni per eventuali problemi.~~
Gli _utenti_ possono inoltrare delle _segnalazioni_ all'_amministratore_ del sito tramite l'apertura del software di posta elettronica installato nel proprio dispositivo (tramite la pressione di un pulsante).

# Preferito
Gli _utenti_ affittuari possono decidere di segnare un annuncio come preferito per salvarlo in una lista collegata allo stesso, facilmente raggiungibile grazie all'interfaccia del sito.

# Foto
_Foto_ relativa ad un _annuncio_, presente nella galleria collegata a quest'ultimo. La foto di anteprima dell'_annuncio_ non rientra in questa categoria, in quanto parte dell'entità _annuncio_.

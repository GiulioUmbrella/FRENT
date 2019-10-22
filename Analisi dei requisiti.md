# Analisi Requisiti

## Glossario
**CASA**: unità immobiliare che viene affittata. SINONIMI: appartamento.

**UTENTE**: utilizzatore del sito. Prima distinzione fra utente generico e utente loggato. Non si fa distinzione fra proprietari di case e non. Per esigenze di comprensibilità’ definiamo due figure: proprietario e affittuario
Il proprietario (host): un proprietario ha postato almeno un annuncio.
L’affittuario (guest): tutti gli utenti registrati sono affittuari.
Vi è una sola registrazione (e quindi un'unica utenza) per entrambi i ruoli.

**ANNUNCIO**: un annuncio riguarda la disponibilità di un singolo appartamento o casa e ne contiene tutte le caratteristiche (indirizzo, numero ospiti, ecc.). Un annuncio riguarda una e una sola casa. Un host e’ proprietario di un annuncio.

**AMMINISTRATORE**: personale che gestisce il sito. Funzioni da definire.

**INDISPONIBILITÀ**: un periodo di indisponibilità riguarda un unico annuncio e un unico periodo di tempo, con una data di inizio e una data di fine (in cui data di inizio < data di fine). In un tale periodo l'annuncio non può avere più periodi di indisponibilità sovrapposti.
Termini utilizzati nella documentazione, nei commenti, nelle issue di questa repository.

### Descrizione Entità Coinvolte
#### Amministratore
Figura interna all'organizzazione del sito con compiti di gestione degli annunci, moderazione dei commenti, gestione degli utenti. Non è considerato un utente (dal punto di vista della struttura del database è un'entità distinta).
Approva gli annunci pubblicati dagli host.

**Politiche**
  - Per rimuovere un annuncio non ci devo essere indisponibilità'
  - Per rimuovere un utente non ci devo essere annunci a lui collegati


#### Utente
**Utente generico**
  - Ricerca e visualizzazione di annunci.
  - Puo’ registrarsi

**Utente loggato**
Eredita le operazioni di utente generico. Segue la divisione logica imposta prima

  - Operazioni dell' utente "Guest"
    - Fare una prenotazione.
    - Cancellare una prenotazione.
    - Commentare prenotazioni passate.
    - Aggiungere ai preferiti.
    - Visualizzare le prenotazioni proprie.
    - Modificare i propri commenti
    - Cancellare i propri commenti

  - **Eliminazione:** Quando un guest lascia la piattaforma (eliminazione dell'utente):
    - Vengono eliminati i suoi preferiti
    - Rimane lo storico delle prenotazioni passate
    - Rimane lo storico dei commenti
    - Non vengono cancellate le prenotazioni passate
    - Vengono cancellate le prenotazioni future

  - Operazioni dell'utente "Host"
    - Creare un annuncio.
    - Modificare un annuncio (con modifiche alle foto collegate).
    - Creare un indisponibilità relativa ad un annuncio
    - Eliminare un indisponibilità
    - Bloccare un annuncio.
    - Visualizzare le prenotazioni relative ai miei annunci.

  - **Eliminazione:** Quando un host lascia la piattaforma (tentativo di eliminazione dell'utente):
    - Se ci sono prenotazioni future relative ai suoi annunci => NON puo’ lasciare la piattaforma
    - Se non ci sono prenotazioni future gli annunci relativi al quel proprietario e tutto quello che e’ collegato viene cancellato.

#### Annuncio
Un annuncio riguarda una casa e ne contiene tutte le caratteristiche (indirizzo, numero ospiti, ecc.).
Ogni annuncio deve avere un host.

Un annuncio può essere eliminato se e solo se non ha prenotazioni future collegate. Se un annuncio viene eliminato  tutte le informazioni in Prenotazioni passate, Galleria e Indisponibilità vengono eliminate. Nella lista dei preferiti compare un messaggio che il link non e' presente.

**Eliminazione**
Se un annuncio viene cancellato si scantena un trigger che porta a due possibili esiti:
1. Se non ci sono prenotazioni future collegate =>
  - Annuncio eliminabile
  - Eliminazione delle foto collegate
  - Eliminazione delle indisponibilità collegate
  - I preferiti rimangono ma non sono più accessibili (l’accessibilità di un preferito è visto graficamente nel sito)
  - Eliminazione delle prenotazioni collegate (non si può più calcolare la spesa, per cui non ha senso tenerlo)
2. Se ci sono prenotazioni future collegate => L’annuncio non è eliminabile

**Semplificazioni**
Controllo indirizzi: non controlliamo indirizzi immessi dagli utenti
Consideriamo solo case in italia (per via della struttura del form per inserire un annuncio)

#### Indisponibilità
Un periodo di indisponibilità riguarda un unico annuncio e un unico periodo di tempo, con una data di inizio e una data di fine (in cui data di inizio < data di fine). In un tale periodo l'annuncio non può avere più periodi di indisponibilità sovrapposti. Ad un periodo di indisponibilità valido può corrispondere al massimo una prenotazione, perchè:
- se è relativo ad una prenotazione, in quel periodo la casa è occupata da un utente del sito.
- se non è relativo ad una prenotazione, in quel periodo il proprietario (utente) dell'annuncio ha dichiarato che la casa o appartamento non è disponibile per prenotazioni.

**Eliminazione**
  - Eliminazione di un annuncio di cui sopra.
  - Eliminazione della prenotazione relativa ad un record di indisponibilità: lo si elimina.
  - Eliminazione di un record di indisponibilità inserito dall’host: lo si elimina.

L’host può generare una indisponibilità' ma solo se nel periodo scelto non ci sono altre indisponibilità' che si accavallano (causate da prenotazioni dei guest o da altre indisponibilità impostate dall’host)

#### Prenotazione
Per affittare un appartamento o casa relativo ad un annuncio in un periodo limitato di tempo un utente deve creare una prenotazione, a cui sarà collegato un periodo di indisponibilità (dell' annuncio scelto).

Le operazioni di prenotazione e indisponibilità hanno un orizzonte temporale limitato( x giorni a partire dalla data corrente)

**Semplificazioni**
  - Prenotazioni: non simuliamo pagamenti/penali

**Eliminazione**
  - Una prenotazione può sempre essere cancellata dal guest che l’ha creata.
  - Una prenotazione viene eliminata quando viene elimianto l'annuncio ad essa collegato

#### Commento
Un utente può lasciare al massimo un commento sull'annuncio. a partire dal giorno successivo all'ultimo giorno del suo soggiorno (periodo di indisponibilità relativo alla prenotazione effettuata dall' utente). I guest possono modificare i propri commenti

Un commento può essere cancellato per due motivi:
  - Cancellazione da guest direttamente
  - Cancellato se viene rimosso l’annuncio.

Preferiti: funzionalità non verrà implementata perché se un utente vuole salvare un annuncio in preferito, si salva il link dell’annuncio come un link preferito.

#### Foto
Foto relativa ad un annuncio, presente nella galleria collegata a quest'ultimo. La foto di anteprima dell'annuncio non rientra in questa categoria, in quanto parte dell'entità annuncio.

**Eliminazione**
  - Dal proprietario di un annuncio
  - Quando un annuncio viene cancellato dall’host;

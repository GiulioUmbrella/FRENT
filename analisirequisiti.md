# Analisi Requisiti
## Glossario dei termini
Termini utilizzati nella documentazione, nei commenti, nel codice sorgente e nelle issue su GitHub di questo progetto.
- **Casa**: unità immobiliare che viene affittata, attraverso la prenotazione di un annuncio. _Sinonimo_: appartamento.
- **Utente**: soggetto che utilizza il portale. C'è una prima distinzione fra utente generico e utente loggato:
    - l'utente generico naviga nel portale, consultando ciò che gli è concesso, e non è in possesso di credenziali per l'accesso,
    - l'utente loggato naviga nel portale in libertà (nei limiti delle operazioni che gli sono concesse, descritte successivamente), è in possesso di credenziali per l'accesso che si è autonomamente creato nel processo di registrazione al portale.
    
    L'utente loggato a sua volta viene suddiviso in due categorie: host (proprietario di una casa che rende affittabile attraverso un annuncio) e guest (persona che affitta una casa, prenotandola attraverso il relativo annuncio). Queste due categorie sono solo logiche e non fisiche, infatti vi è un solo processo di registrazione (e quindi un'unica utenza) per entrambi i ruoli.
    Di conseguenza, si parla di utente (loggato):
    - host, quando ha pubblicato almeno un annuncio,
    - guest, sempre (tutti gli utenti registrati hanno la possibilità di affittare).

- **Annuncio**: un annuncio riguarda la disponibilità di una singola casa (o appartament) e ne descrive tutte le caratteristiche (indirizzo, città, numero massimo di ospiti, ecc.). Un annuncio riguarda una e una sola casa.
- **Amministratore**: soggetto facente parte del personale che gestisce il sito. Non è considerato un utente (dal punto di vista della struttura del database è un'entità distinta). Ha compiti di gestione e moderazione all'interno del portale.
- **Indisponibilità**: un periodo di indisponibilità definisce l'intervallo temporale in cui un annuncio è reso indisponibile, ovvero non prenotabile. Riguarda un unico annuncio e un unico periodo temporale, definito da una data di inizio e una data di fine (in cui la data di inizio precede la data di fine). In un tale periodo l'annuncio non può avere più periodi di indisponibilità sovrapposti.
- **Commento**: recensione dell'utente sul suo soggiorno presso una casa di un preciso annuncio. Un utente può lasciare al massimo un commento sull'annuncio, a partire dal giorno successivo all'ultimo giorno del suo soggiorno (corrispondente al periodo di indisponibilità relativo alla prenotazione effettuata dall'utente).
- **Foto**: foto relativa ad un annuncio, presente nella galleria collegata a quest'ultimo. La foto di anteprima dell'annuncio non rientra in questa categoria, in quanto parte dell'entità Annuncio.

## Descrizione entità coinvolte
**N.B.**: Per **Politiche** si intendono i vincoli di integrità referenziale, vincoli intrarelazionali e di dominio.
### Amministratore
**Operazioni**
1. Visualizza gli annunci pubblicati e modificati dagli host.
2. Approva gli annunci pubblicati.
3. Blocca gli annunci.


### Utente
#### Utente generico

**Operazioni**
1. Ricerca e visualizza, aggiungendo dei filtri per cercare ciò che gli è necessario, gli annunci pubblicati dagli altri utenti (host) del portale. 
2. Può registrarsi al portale.

#### Utente loggato
Eredita le operazioni che può compiere l’utente generico. Segue la divisione logica imposta nel [Glossario](##Glossario).

**Operazioni (guest/host)**
1. Eliminare il proprio account.

**Operazioni (guest)**
1. Effettuare una prenotazione ad un annuncio.
2. Cancellare una prenotazione ad un annuncio.
3. Commentare gli annunci di prenotazioni passate.
4. Visualizzare le proprie prenotazioni (passate, correnti e future). 
5. Modificare i propri commenti.
6. Cancellare i propri commenti.

**Politiche (guest)**

Un guest può lasciare la piattaforma (ovvero richiedere la rimozione del proprio account). Vengono attuate le politiche che seguono.
1. Rimane lo storico delle prenotazioni effettuate passate (si perderà il riferimento all'utente).
2. Rimane lo storico dei commenti agli annunci pubblicati.
3. Vengono cancellate le prenotazioni future.
4. In presenza di prenotazioni correnti, la rimozione del proprio profilo viene negata.

**Operazioni (host)**
1. Creare un annuncio (con aggiunta delle foto che faranno parte della galleria dell'annuncio).
2. Modificare un annuncio (incluse le modifiche alle foto collegate a questo).
3. Creare un periodo di indisponibilità relativo ad un annuncio di proprietà dell’host.
4. Eliminare un’indisponibilità creato dall’host stesso.
5. Bloccare un annuncio, corrispondente a una indisponibilità a tempo indeterminato, fino a quando viene sbloccato.
6. Visualizzare le prenotazioni (presenti, passate, future) relative ai propri annunci.

**Politiche (host)**
Un host può lasciare la piattaforma (ovvero richiedere la rimozione del proprio account). Vengono attuate le politiche che seguono.
1. In presenza di prenotazioni correnti e/o future relative ai suoi annunci, la rimozione del proprio profilo viene negata.
2. Se non ci sono prenotazioni future, gli annunci relativi al quel proprietario, le foto e i commenti vengono rimosse. Rimangono tutte le indisponibilità (campo annuncio settato null) e le prenotazioni per lasciare ai guest uno storico delle prenotazioni.

### Annuncio
Ogni annuncio deve avere un host. Un annuncio può essere prenotabile solo se si trova nello stato VA, definito nelle politiche.

**Politiche**
Un annuncio si può assumere diversi stati:
- **VA**: annuncio **V**isualizzato e **A**pprovato. L'amministratore del portale ha visualizzato l'annuncio, lo ha ritenuto idoneo al portale e quindi lo ha reso disponibile a ricevere prenotazioni.
- **VNA**: annuncio **V**isualizzato e **N**on **A**pprovato. L'amministratore del portale ha visualizzato l'annuncio, ma non lo ha ritenuto idoneo al portale. Non è più di sua competenza fino a quando l'host che ha pubblicato l'annuncio non lo modificherà. A quel punto l'annuncio tornerà nello stato **NVNA**.
- **NVNA**: annuncio **N**on **V**isualizzato e **N**on **A**pprovato.
L'amministratore del portale non ha ancora visualizzato l'annuncio, quindi potrà approvarlo oppure no (l'annuncio potrà passare nello stato **VA** oppure **NVA**).
- **NVA**: annuncio **N**on **V**isualizzato e **A**pprovato. Possibilità non contemplata.

Un annuncio può essere eliminato se e solo se non ha prenotazioni correnti e future collegate. Vengono attuate le politiche che seguono.
1. Tutte le foto vengono rimosse.
2. Tutti i commenti vengono eliminati.

**Semplificazioni**
1. Non viene controllata la correttezza (ovvero l'esistenza) degli indirizzi immessi dagli utenti, ma solo il formato (esempio: viene verificato che sia un testo e non un indirizzo email).
2. Vengono considerati solo annunci pubblicati per case in Italia (per rendere più semplice la struttura del form attraverso cui inserire un annuncio).

### Indisponibilità
Ad un periodo di indisponibilità valido può corrispondere al massimo una prenotazione, perchè:
- se è relativo ad una prenotazione, in quel periodo la casa è occupata da un utente del sito.
- se non è relativo ad una prenotazione, in quel periodo l'host dell'annuncio ha dichiarato che la casa o appartamento non è disponibile per prenotazioni.

**Politiche**
Ci sono due modi per eliminare un'indisponibilità:
1. Eliminazione della prenotazione relativa ad un record di indisponibilità: eliminata di conseguenza.
2. Eliminazione di un record di indisponibilità inserito dall’host: lo si elimina.

Creazione di periodi di indisponibilità:
1. L’host può generare un'indisponibilità (temporanea) solo se nel periodo scelto non ci sono altre indisponibilità che si accavallano (causate da prenotazioni dei guest o da altre indisponibilità impostate dall’host).

### Prenotazione
Per affittare una casa o un appartamento relativi ad un annuncio in un periodo limitato di tempo, un utente deve creare una prenotazione, a cui sarà collegato un periodo di indisponibilità (dell'annuncio scelto).

**Operazioni**
1. È possibile effettuare una prenotazione e generare un'indisponibilità entro un orizzonte temporale limitato (un numero _x_ prefissato di giorni a partire dalla data corrente) 

**Semplificazioni**
1. Non vengono simulati pagamenti per le prenotazioni e nemmeno il pagamento delle penali per le cancellazioni.

Una prenotazione può sempre essere cancellata dal guest che l’ha creata.

### Commento

**Politiche**
Un commento può essere cancellato per due motivi:
1. Cancellazione da parte del guest che lo ha pubblicato.
2. Cancellazione se viene rimosso l’annuncio.

### Foto
**Politiche**
Una foto può essere cancellata:
1. Dal proprietario di un annuncio.
2. Quando un annuncio viene cancellato dall’host.

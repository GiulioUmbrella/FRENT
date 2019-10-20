# Glossario
Termini utilizzati nella documentazione, nei commenti, nelle issue di questa repository.
**N.B.**: I termini in _corsivo_ indicano il concetto espresso dall'entità corrispondente.

## Amministratore
Figura interna all'organizzazione del sito con compiti di gestione degli _annunci_, moderazione dei _commenti_, gestione degli _utenti_. Non è considerato un _utente_ (dal punto di vista della struttura del database è un'entità distinta).
1. Puo' rimuovere le prenotazioni 
2. Puo' rimuovere gli annunci. 
3. Puo' rimuovre le indisponibilita'
4. Un amministratore puo' rimuovere i commenti
5. Un amministratore puo' rimuovere un utente

Politiche
Per rimuovere un annuncio non ci devo essere indisponibilita'
Per rimuovere un utente non ci devo essere annunci a lui collegati


## Utente
Utilizzatore del sito. Non si fa distinzione fra proprietari di case e non. Vi è una sola registrazione (e quindi un'unica utenza) per entrambi i ruoli. Le pagine per la ricerca di una casa da affittare
sono tenute separate.  
1. Le operazioni di prenotazione e indisponibilita' hanno un
orizzonte temporale limitato( x giorni a partire dalla data corrente) 
2. Un utente puo' annullare la sua prenotazione quando vuole
(per semplicita' non ci sono penali)
3. Un utente puo' generare una indisponibilita' ma solo se nel
periodo scelto non ci sono altre indisponibilita' che si accavallano
o altre prenotazioni.
4. Un utente puo' aggiungere un nuovo annuncio. Il controllo
viene delegato all'amministratore. 

Semplificazioni
1. Prenotazioni: non simuliamo pagamenti/penali
2. Controllo indirizzi: non controlliamo indirizzi immessi dagli utent
3. Consideriamo solo case in italia (per via della struttura del form per inserire un annuncio)
4. Un utente puo' togliere/inserire il suo annuncio per rimuovere 
commenti negativi. Il controllo di queste prassi e' delegato all'amministratore. 

Politiche
Un utente puo' cancellarsi. Per farlo deve rimuovere tutti le sue 
prenotazioni e i suoi annunci. Se e' presente una prenotazione 
deve contattare un amministratore.

## Annuncio
Un _annuncio_ riguarda la disponbilità di un singolo appartamento o casa e ne contiene tutte le caratteristiche (indirizzo, numero ospiti, ecc.). 
1. Ogni _annuncio_ deve avere un solo proprietario (_utente_).
2. L'_annuncio_ non ha una data di scadenza. 


Politiche
Non si possono rimuovere annunci che hanno prenotazioni.
Non si possono modificare i proprietari di una casa.
Se si rimuovere un annuncio tutte le informazioni in Commenti, Galleria, Indisponibilita' vengono perse. Nella lista 
dei preferiti compare un messaggio che il link non e' presente.


## Prenotazione
Per affittare un appartamento o casa relativo ad un _annuncio_ in un periodo limitato di tempo un _utente_ deve creare una _prenotazione_, a cui sarà collegato un periodo di _indisponibiltà_ (dell'_annuncio_ scelto).

## Indisponibilità
Un periodo di _indisponibilità_ riguarda un unico _annuncio_ e un unico periodo di tempo, con una data di inizio e una data di fine (in cui data di inizio < data di fine). In un tale periodo l'_annuncio_ non può avere più periodi di _indisponibilità_ sovrapposti (quindi due periodi di _indisponibilità_ per uno stesso _annuncio_ sono validi solo se la data di fine del primo è al massimo uguale alla data di inizio del secondo) con la conseguenza che ad un periodo di _indisponibilità_ valido può corrispondere al massimo una _prenotazione_, perchè:
- se è relativo ad una _prenotazione_, in quel periodo la casa è occupata da un _utente_ del sito.
- se non è relativo ad una _prenotazione_, in quel periodo il proprietario (_utente_) dell'_annuncio_ ha dichiarato che la casa o appartamento non è disponibile per _prenotazioni_.

## Commento
Un _utente_ può lasciare al massimo un _commento_ sull'_annuncio_, a partire dal giorno successivo all'ultimo giorno del suo soggiorno (periodo di _indisponibilità_ relativo alla _prenotazione_ effettuata dall'_utente_).
Dobbiamo decidere se permettere la modifica dei commenti.


## Segnalazione
Relazione rimossa, inseriamoun meccanismo per comunicare con 
l'amministratore.

## Preferito
Gli _utenti_ affittuari possono decidere di segnare un annuncio come preferito per salvarlo in una lista collegata allo stesso, facilmente raggiungibile grazie all'interfaccia del sito.

## Foto
_Foto_ relativa ad un _annuncio_, presente nella galleria collegata a quest'ultimo. La foto di anteprima dell'_annuncio_ non rientra in questa categoria, in quanto parte dell'entità _annuncio_.

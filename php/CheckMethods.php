<?php

/**
 * Class Amministratore
 * 1. metodo per verificare la correttezza delle mail e della sua lunghezza
 * 2. metodo per controllare la correttezza della lunghezza delle stringhe
 * 3. metodo per la data
 * 4. metodo per controllare il range dei interi
 *      id delle entita
 *      numMaxOspiti di un annuncio
 *
 * 5. metodo per controllare che lo stato degli annunci sia 0,1 o 2
 * 6. controllare che in una stringa NON ci siano gli numeri per nome e cognome, citta
 * 7. controllare che la durata sia maggiore
 * 8. verificare il numero di telefono
 *
 */

function checkMail($mail, $lunghezza):bool{
    return is_string($mail) and  filter_var($mail, FILTER_VALIDATE_EMAIL) and strlen($mail)<$lunghezza;
}
function checkDate($format,$date):bool {
    return date($format,strtotime($date));
}

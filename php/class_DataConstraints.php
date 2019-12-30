<?php

/**
 * Class DataConstraints
 * Contiene solo mappe di coppie (chiave, valore) con le dimensioni massime dei campi dati nel database.
 * - Se il campo nel database è una stringa, l'intero positivo segnato indica il numero massimo di caratteri.
 * - Se il campo nel database è un numero, l'intero positivo segnato indica il valore massimo assumibile.
 * - Se in questo file non è segnato un campo che invece è presente nel database significa che non ci sono veri e propri limiti imposti.
 */
class DataConstraints {
    /**
     * Tabella amministratori: dimensioni massime dei campi
     */
    const amministratori = array(
        "user_name" => 32, // string
        "password" => 48, // string
        "mail" => 191 // string
    );

    /**
     * Tabella utenti: dimensioni massime dei campi
     */
    const utenti = array(
        "nome" => 32, // string
        "cognome" => 32, // string
        "user_name" => 32, // string
        "mail" => 191, // string
        "password" => 48, // string
        "img_profilo" => 48, // string
        "telefono" => 18 // string
    );

    /**
     * Tabella annunci: dimensioni massime dei campi
     */
    const annunci = array(
        "titolo" => 32, // string
        "descrizione" => 512, // string
        "img_anteprima" => 48, // string
        "desc_anteprima" => 256, // string
        "indirizzo" => 128, // string
        "citta" => 128, // string
        "stato_approvazione" => 2, // int
        "max_ospiti" => 99 // int
    );

    /**
     * Tabella occupazioni: dimensioni massime dei campi
     */
    const occupazioni = array(
        "num_ospiti" => 99 // int
    );

    /**
     * Tabella commenti: dimensioni massime dei campi
     */
    const commenti = array(
        "titolo" => 64, // string
        "commento" => 512, // string
        "votazione" => 5 // int
    );
}
<?php

/**
 * Class Amministratore
 * 1.
 * 2.
 * 3.
 * 4. metodo per controllare il range dei interi
 *      id delle entita
 *      numMaxOspiti di un annuncio
 *
 * 5.
 * 6.
 * 7. controllare che la durata sia maggiore
 * 8.
 *
 */
//metodo per verificare la correttezza delle mail e della sua lunghezza done
/**
 * Controlla se la $mail è una mail valida ovvero ha qualcosa@qualunquecosa.qualunque cosa e
 * che sia di lunghezza inferiore a $lunghezza
 * @param $mail string mail da controllare
 * @param $lunghezza int lunghezza massima che la mail può assumere
 * @return bool sse $mail è un indirizzo email valida e strlen(mail) <= lunghezza
 */
function checkIsValidMail($mail, $lunghezza=191): bool{
    return is_string($mail) and filter_var(trim($mail), FILTER_VALIDATE_EMAIL) and strlen(trim($mail)) < $lunghezza;
}


/**
 * Controlla se in una stringa è contenuto il carattere SPAZIO
 * @param $str string la stringa da controllare
 * @return bool restituisce true sse la stringa non contiene nessun spazio
 */
function checkStringContainsNoSpace($str): bool {
    return is_string($str) and !preg_match("/^\S{6,}\z/",trim($str));
}


/**
 * Controlla se il valore passato è una stringa e se è di lunghezza <= a $len
 * @param $str string stringa da controllare
 * @param $len int indica la lunghezza massima che la stringa può avere.
 * @return bool se str è stringa e len è un intero e la stringa ha la lunghezza <= a int, altrimenti false;
 */
function checkStringLen($str, $len): bool{
    return is_string($str) and is_int($len) and strlen($str) <= $len;
}


/**
 * controlla se il valore $date contiene una data valida secondo il formato specificato.
 * @param $date string la stringa che dovrebbe contenere la data.
 * @param string $format il formato in cui è rappresentato la data.
 * @return bool restituisce true sse $data è una data valida rispetto al formato specificato.
 */
function checkIsValidDate($date, $format="Y-m-d"){
    return is_string($date) and date("Y-m-d",strtotime($date));

}


/**
 * La funzione controlla che in una stringa non siano presenti i caratteri numerici
 * @param $str string è la stringa da controllare
 * @return bool restituisce true sse la stringa non contiene nessun carattere numerico.
 */
function checkStringNoNumber($str):bool {
    return is_string($str) and  preg_match('~^[\p{L}\p{Z}]+$~u', $str);
}

//todo da controllare se funziona questo confronto.
/**
 * @param $dataI
 * @param $dataF
 * @return bool restuisce true sse la dataI è antecedente alla dataF
 */
function checkDateBeginAndEnd($dataI, $dataF): bool {
    return is_string($dataI) and is_string($dataF) and $dataI<= $dataF;
}
//verificare il numero di telefono
/**
 * verifica se la stringa $telefono contiene un numero di telefono valido.
 * @param $telefono string numero da controllare.
 * @return bool
 */
function checkPhoneNumber($telefono): bool {
    return is_string($telefono) and preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $telefono);
}

<?php
require_once "./class_Database.php";
require_once "./class_CredenzialiDB.php";
require_once "./class_Frent.php";

/**
 * Costruisce un blocco HTML di testo che racconta l'esecuzione del test.
 * @param string $title descrive il test. Deve essere esplicativo. Se $passed === TRUE sarà di colore verde, altrimenti sarà rosso
 * @param int|array $expected è una variabile intera o un array che ci si aspetta come risultato del test
 * @param int|array $gotten è una varabile intera o un array che si ottiene dall'esecuzione del metodo
 * @param string $content descrive come è andato il test, in particolare, elenca gli errori (se ce ne sono stati)
 * @param bool $passed è TRUE se $content === "All good.", altrimenti false
 * @return string blocco HTML in cui è descritto l'andamento del test 
 */
function testReportBuild($title, $expected, $gotten, $content, $passed) {
    $color = $passed ? "green" : "red";
    return "<h1 style=\"color: $color\">" . $title . "</h1>" .
            "<h2>Expected (not expected if assertNotEquals was invoked)</h2>" .
            "<p>" . print_r($expected, TRUE) . "</p>" .
            "<h2>Gotten</h2>" .
            "<p>" . print_r($gotten, TRUE) . "</p>" .
            "<h2>Test execution</h2>" .
            "<p>" . $content . "</p>";
}

/**
 * Test in cui ci si aspetta l'uguaglianza fra $expected e $gotten. Stampa il risultato del test.
 * @param string $description descrive il test. Deve essere esplicativo. Se $passed === TRUE sarà di colore verde, altrimenti sarà rosso
 * @param int $expected è una variabile intera che ci si aspetta come risultato del test
 * @param int $gotten è una varabile intera che si ottiene dall'esecuzione del metodo
 */
function assertEquals($description, $expected, $gotten) {
    $output = "";
    if($expected !== $gotten) {
        $output .= "\$expected is:<br/>" . print_r($expected, TRUE) . "<br/>\$gotten is:<br />" . print_r($gotten, TRUE);
    } else {
        $output .= "All good.";
    }
    echo testReportBuild($description, $expected, $gotten, $output, $output === "All good." ? TRUE : FALSE);
}

/**
 * Test in cui ci si aspetta la NON uguaglianza fra il contenuto di $notExpected e $gotten. Stampa il risultato del test.
 * @param string $description descrive il test. Deve essere esplicativo. Se $passed === TRUE sarà di colore verde, altrimenti sarà rosso
 * @param array $notExpected è una array di variabili intere che ci non ci si vuole aspettare come risultato del test
 * @param int $gotten è una varabile intera che si ottiene dall'esecuzione del metodo
 */
function assertNotEquals($description, $notExpected, $gotten) {
    $output = "";
    if(in_array($gotten, $notExpected, TRUE)) {
        $output .= "\$expected is:<br/>" . print_r($notExpected, TRUE) . "<br/>\$gotten is:<br />" . print_r($gotten, TRUE);
    } else {
        $output .= "All good.";
    }
    echo testReportBuild($description, $notExpected, $gotten, $output, $output === "All good." ? TRUE : FALSE);
}

/**
 * Test in cui ci si aspetta l'uguaglianza fra i due array $expected e $gotten. Stampa il risultato del test.
 * @param string $description descrive il test. Deve essere esplicativo. Se $passed === TRUE sarà di colore verde, altrimenti sarà rosso
 * @param array $expected è un array di oggetti che ci si aspetta come risultato del test
 * @param array $gotten è un array di oggetti che si ottiene dall'esecuzione del metodo
 */
function assertArrayEquals($funName, $expected, $gotten) {
    $output = "";
    if(count($expected) === count($gotten)) {
        for($i = 0; $i < $expected; $i++) {
            if($expected[$i] != $gotten[$i]) {
                $output .= "In position $i:<br />\$expected is:<br/>" . print_r($expected[$i], TRUE) . "<br/>\$gotten is:<br />" . print_r($gotten[$i], TRUE);
            }
        }
        if($output === "") {
            $output .= "All good.";
        }
    } else {
        $output .= "Length of \$expected ($expected) is different from length of \$gotten ($gotten).";
    }

    echo testReportBuild($funName, $expected, $gotten, $output, $output === "All good." ? TRUE : FALSE);
}


$db = new Database(
    CredenzialiDB::DB_ADDRESS,
    CredenzialiDB::DB_USER,
    CredenzialiDB::DB_PASSWORD,
    CredenzialiDB::DB_NAME."_test"
);

/**
 * Test funzionalità utente non loggato
 */
$frent = new Frent($db);

assertNotEquals("Registrazione a buon fine", array(-1, -2), $frent->registrazione("Gino", "Pasticcio", "ginop", "ginopast@gmail.com", "password1", "1998-01-01", "foto.png", "3343343340"));
assertEquals("Registrazione non a buon fine, mail duplicata", -2, $frent->registrazione("Gino", "Pasticcio", "ginopasticcio", "ginopast@gmail.com", "password1234", "1998-01-01", "foto2.png", "3343343340"));



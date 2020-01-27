<?php


class Eccezione extends Exception {
    /**
     * Costruttore di Eccezione, il cui messaggio (parametro $message) verrà modificato convertendo tutti i caratteri speciali in entità HTML.
     * @param string $message messaggio
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct(htmlentities($message), $code, $previous);
    }
}
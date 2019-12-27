<?php

require_once("./class_Eccezione.php");
define("MB", 1048576);

/**
 * Class ImageManager
 * Classe che si occupa di gestire il caricamento delle immagini sul server,
 * di nominarle secondo un formato predefinito e di reperirli su richiesta.
 */
class ImageManager {
    private $targetFolder;
    private $targetFileName;
    private $targetFileExtension;
    private $tempFileName;
    private $maxFileSize;

    /**
     * Costruttore di ImageManager.
     * @param string $targetFolder path della cartella destinazione radice in cui caricare i file
     * @param int $maxFileSize dimensione massima di un file caricato
     */
    public function __construct($targetFolder, $maxFileSize = 2) {
        // valori costanti una volta inizializzati
        $this->targetFolder = $targetFolder;
        $this->maxFileSize = $maxFileSize;

        $this->targetFileName = "";
        $this->targetFileExtension = "";
        $this->tempFileName = "";
    }

    /**
     * Recupera il file da caricare sul server da $_SERVER. Il file deve avere una delle seguenti estensioni: jpg, png, jpeg.
     * @param string $img_name_attr valore dell'attributo name dell'elemento <input type="file" ..></file>
     * nel form da cui si sta recuperando l'immagine
     * @param string $outFileName nome da assegnare al file una volta caricato, senza estensione (se non assegnato viene preso il nome del file caricato) (default: "")
     * @param int $fileIndex indice del file nell'array $_FILES (default: -1)
     * @throws Eccezione se il file caricato non è un'immagine, se il file supera la dimensione massima specificata nella costruzione e se l'estensione non è valida; se il file non è stato caricato
     */
    public function setFile($img_name_attr, $outFileName = "", $fileIndex = -1) {
        // mi assicuro che i campi dati del file siano azzerati
        $this->unsetFile();

        /**
         * Se $fileIndex === -1 l'utente ha richiesto il caricamento di un file singolo, ovvero ha utilizzato un tag:
         * <input type="file" ... />.
         * Se $fileIndex >= 0 l'utente ha richiesto il caricamento di più file, ovvero ha utilizzato un tag:
         * <input type="file" ... multiple />
         */

        // verifico che sia stato caricato un file o più
        if(!isset($_FILES[$img_name_attr]) || $_FILES[$img_name_attr]["error"] === 4 || $_FILES[$img_name_attr]["error"][0] === 4) {
            throw new Eccezione("Non è stato caricato alcun file." . print_r($_FILES, TRUE));   
        }
        
        // verifico che l'indice del file chiesto sia nei limiti
        if($fileIndex !== -1 && $fileIndex > $this->countFiles($img_name_attr)) {
            throw new Eccezione("È stato richiesto di accedere ad un file non caricato.");
        }

        // recupero il nome del file caricato
        $name = $fileIndex !== -1 ? $_FILES[$img_name_attr]["name"][$fileIndex] : $_FILES[$img_name_attr]["name"];
        
        // file size
        $size = $fileIndex !== -1 ? $_FILES[$img_name_attr]["size"][$fileIndex] : $_FILES[$img_name_attr]["size"];
        
        // recupero l'estensione del file
        $this->targetFileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        
        // verifico se è stato richiesto un nome particolare per il file di output
        $this->targetFileName = $this->targetFolder . ($outFileName === "" ? $name : $outFileName . "." . $this->targetFileExtension);
        
        // directory in cui è salvato il file temporaneo
        $this->tempFileName = $fileIndex !== -1 ? $_FILES[$img_name_attr]["tmp_name"][$fileIndex] : $_FILES[$img_name_attr]["tmp_name"];
        
        // verifico che l'estensione sia corretta
        if ($this->targetFileExtension != "jpg" &&
            $this->targetFileExtension != "png" &&
            $this->targetFileExtension != "jpeg"
        ) {
            throw new Eccezione("Il file caricato non ha un'estensione valida.");
        }
        
        // verifico che il file caricato sia effetivamente un file imamgine (verifica i metadati)
        if (getimagesize($this->tempFileName) === false) {
            throw new Eccezione("Il file caricato non è un'immagine.");
        }
        
        // verifico che il file non superi la dimensione massima consentita
        if ($size > $this->maxFileSize * MB) {
            throw new Eccezione("Il file caricato supera la dimensione massima di " . $this->maxFileSize . "MB.");
        }
    }

    /**
     * Restituisce il nome del file (con estensione) da caricare o caricato.
     * @return string nome del file (con estensione) da caricare o caricato
     * @throws Eccezione se il file non è stato ancora impostato
     */
    public function fileName(): string {
        $this->verifyFilePresence();

        return pathinfo($this->targetFileName, PATHINFO_BASENAME);
    }

    /**
     * Restituisce l'estensione del file da caricare o caricato.
     * @return string estensione del file da caricare o caricato
     * @throws Eccezione se il file non è stato ancora impostato
     */
    public function fileExtension(): string {
        $this->verifyFilePresence();

        return $this->targetFileExtension;
    }

    /**
     * Sposta il file caricato nella posizione richiesta durante la creazione dell'istanza di ImageManager.
     * @return bool TRUE se è stato salvato nella posizzione corretta il file, FALSE altrimenti
     * @throws Eccezione se il file non è stato ancora impostato
     */
    public function saveFile(): bool {
        $this->verifyFilePresence();
        return move_uploaded_file($this->tempFileName, $this->targetFileName);
    }

    /**
     * Restituisce il numero dei file caricati tramite il form. Il metodo deve venire invocato se è stato abilitato il caricamento multiplo.
     * @param string $img_name_attr valore dell'attributo name dell'elemento <input type="file" ..></file>
     * nel form da cui si sta recuperando l'immagine.
     */
    public function countFiles($img_name_attr): int {
        return count($_FILES[$img_name_attr]["name"]);
    }

    /**
     * Rimuove un file immagine dato un nome di file (comprensivo di path).
     * @param string $fileName nome del file immagine da rimuovere comprensivo di path.
     * @return bool TRUE se il file è stato rimosso, FALSE se ci sono stati problemi o l'estensione del file non è accettata.
     */
    public static function deleteFile($fileName): bool {
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        return ($fileExtension === "jpg" || $fileExtension === "png" || $fileExtension === "jpeg") ? unlink($fileName) : FALSE;
    }

    /**
     * Verifica che il file sia stato impostato. Se lo è stato non fa nulla, altrimenti lancia un'eccezione.
     * @throws Eccezione se il file non è stato ancora impostato
     */
    private function verifyFilePresence() {
        if (strlen($this->targetFileExtension) === 0 || strlen($this->targetFileName) === 0) {
            throw new Eccezione("Non è stato ancora selezionato un file.");
        }
    }

    /**
     * Si assicura che i campi dati siano azzerati prima di impostare un file. Se non lo sono, li azzera.
     */
    private function unsetFile() {
        $this->targetFileName = "";
        $this->targetFileExtension = "";
        $this->tempFileName = "";
    }
}

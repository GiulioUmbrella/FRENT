<?php

require_once("Eccezione.php");
define("MB", 1048576);
/**
 * Class ImageManager
 * Classe che si occupa di gestire il caricamento delle immagini sul server,
 * di nominarle secondo un formato predefinito e di reperirli su richiesta.
 */
class ImageManager {
    private $targetFolder;
    private $targetFile;
    private $tempFile;
    private $maxFileSize;

    /**
     * Costruttore di ImageManager.
     * @param string $targetFolder path della cartella destinazione radice in cui caricare i file
     * @param int $maxFileSize dimensione massima di un file caricato
     */
    public function ImageManager($targetFolder, $maxFileSize = 2) {
        $this->targetFolder = $targetFolder;
        $this->targetFile = "";
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * Recupera il file da caricare sul server da $_SERVER. Il file deve avere una delle seguenti estensioni: jpg, png, jpeg.
     * @param string $img_name_attr valore dell'attributo name dell'elemento <input type="file" ..></file>
     * nel form da cui si sta recuperando l'immagine
     * @param string $fileName nome da assegnare al file una volta caricato (se non assegnato viene preso il nome del file caricato)
     * @throws Eccezione se il file caricato non è un'immagine, se il file supera la dimensione massima specificata nella costruzione e se l'estensione non è valida.
     */
    public function setFile($img_name_attr, $fileName = "") {
        $file = $_FILES[$img_name_attr];
        $this->tempFile = $file["tmp_name"];
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);

        if (getimagesize($this->tempFile) === false) {
            throw new Eccezione("Il file caricato non è un'immagine.");
        }

        if ($extension != "jpg" && $extension != "png" && $extension != "jpeg") {
            throw new Eccezione("Il file caricato non ha un'estensione valida.");
        }

        if ($file["size"] > $this->maxFileSize * MB) {
            throw new Eccezione("Il file caricato supera la dimensione massima di " . $this->maxFileSize . "MB.");
        }
        
        if($fileName === "") {
            $this->targetFile = $this->targetFolder . basename($_SERVER[$img_name_attr]["name"]);
        } else {
            $this->targetFile = $this->targetFolder . basename("$fileName.$extension");
        }
    }

    /**
     * Restituisce il nome del file (con estensione) da caricare o caricato.
     * @return string nome del file (con estensione) da caricare o caricato
     * @throws Eccezione se il file non è stato ancora impostato
     */
    public function fileName(): string {
        if (strlen($this->targetFile) === 0) {
            throw new Eccezione("Non è stato ancora selezionato un file.");
        }

        return $this->targetFile;
    }

    /**
     * Restituisce l'estensione del file da caricare o caricato.
     * @return string estensione del file da caricare o caricato
     * @throws Eccezione se il file non è stato ancora impostato
     */
    public function fileExtension(): string {
        if (strlen($this->targetFile) === 0) {
            throw new Eccezione("Non è stato ancora selezionato un file.");
        }

        return strtolower(pathinfo($this->targetFile, PATHINFO_EXTENSION));
    }

    /**
     * Sposta il file caricato nella posizione richiesta durante la creazione dell'istanza di ImageManager.
     * @return bool TRUE se è stato salvato nella posizzione corretta il file, FALSE altrimenti
     * @throws Eccezione se il file non è stato ancora impostato
     */
    public function saveFile(): bool {
        if (strlen($this->targetFile) === 0) {
            throw new Eccezione("Non è stato ancora selezionato un file.");
        }

        return move_uploaded_file($this->tempFile, $this->targetFile);
    }
}

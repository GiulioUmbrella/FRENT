<?php

require_once("./Eccezione.php");

/**
 * Class ImageManager
 * Classe che si occupa di gestire il caricamento delle immagini sul server,
 * di nominarle secondo un formato predefinito e di reperirli su richiesta.
 */
class ImageManager {
    private $targetFolder;
    private $targetFile;
    /**
     * Costruttore di ImageManager.
     * @param string $targetFolder path della cartella destinazione radice in cui caricare i file
     */
    public function ImageManager($targetFolder) {
        $this->targetFolder = $targetFolder;
        $this->targetFile = "";
    }

    /**
     * Recupera il file da caricare sul server da $_SERVER
     * @param string $img_name_attr valore dell'attributo name dell'elemento <input type="file" ..></file>
     * nel form da cui si sta recuperando l'immagine
     * @return string nome del file con estensione
     */
    public function setFile($img_name_attr): string {
        $file = $_FILES[$img_name_attr];
        var_dump($file); // per test
        if(getimagesize($file["tmp_name"]) === false) {
            throw new Eccezione("Il file caricato non è un'immagine.");
        }

        if ($file["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        return $this->targetFolder . basename($_FILES[$img_name_attr]["name"]);
    }

    /**
     * Restituisce il nome del file (con estensione) da caricare o caricato.
     * @return string nome del file (con estensione) da caricare o caricato
     * @throws Eccezione se il file non è stato ancora impostato
     */
    public function fileName(): string {
        if(strlen($this->targetFile) === 0) {
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
        if(strlen($this->targetFile) === 0) {
            throw new Eccezione("Non è stato ancora selezionato un file.");
        }

        return strtolower(pathinfo($this->targetFile, PATHINFO_EXTENSION));
    }

    
}
<?php

require_once "../CheckMethods.php";

class Foto
{
    private $id_foto;
    private $file_path;
    private $descrizione;

    public function __construct($id, $des = "Questa foto non &egrave; stato commentato", $path = "deada")
    {
        $this->setIdFoto($id);
        $this->setFilePath($path);
        $this->setDescrizione($des);

    }

    public function getIdFoto(): int
    {
        return $this->id_foto;
    }

    public function setIdFoto($id_foto): void{
        if (is_int($id_foto) and $id_foto>0) {
            $this->id_foto = $id_foto;
        } else{
            throw new Eccezione(htmlentities("ID della foto non è valido"));
        }
    }

    public function getFilePath(): string
    {
        return $this->file_path;
    }

    public function setFilePath($file_path): void
    {
        if(is_string($file_path) and checkStringLen($file_path,128)){
            $file_path= trim($file_path);
            $file_path= str_replace(" ","_",$file_path);
            $this->file_path = $file_path;
        }else{
            throw new Eccezione(htmlentities("Path della foto non è valida!"));
        }
    }

    public function getDescrizione(): string
    {
        return $this->descrizione;
    }

    public function setDescrizione($descrizione): void
    {

        if(is_string($descrizione) and checkStringLen($descrizione,128)){
            $this->$descrizione = $descrizione;
        }else{
            throw new Eccezione(htmlentities("Descrizione non è valida!"));
        }
    }

}
<?php


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
        if (is_string($id_foto)) {
            $this->id_foto = $id_foto;
        } else {
//          gestire l'eccezione
        }
    }

    public function getFilePath(): string
    {
        return $this->file_path;
    }

    public function setFilePath($file_path): void
    {
        if(is_string($file_path)){
            $this->file_path = $file_path;
        }else{
            //gestire l'eccezione
        }
    }

    public function getDescrizione(): string
    {
        return $this->descrizione;
    }

    public function setDescrizione($descrizione): void
    {
        $this->descrizione = $descrizione;
    }

}
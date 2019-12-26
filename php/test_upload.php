<?php
require_once("./class_ImageManager.php");

if(isset($_POST['submit'])) {
    $imageManager = new ImageManager("../uploads/");
    try {
        // caricamento singolo
        $imageManager->setFile("uploadSingolo", "test");
        echo "<p>File name: " . $imageManager->fileName() . ", " . "file extension: " . $imageManager->fileExtension() . ".</p>";
        echo $imageManager->saveFile() === false ? "<p>File has not been saved correctly.</p>" : "<p>File has been saved correctly.</p>";

        // caricamento multiplo
        for($i = 0, $numFiles = $imageManager->countFiles("uploadMultiplo"); $i < $numFiles; $i++) {
            $imageManager->setFile("uploadMultiplo", "test$i", $i);
            echo "<p>File name: " . $imageManager->fileName() . ", " . "file extension: " . $imageManager->fileExtension() . ".</p>";
            echo $imageManager->saveFile() === false ? "<p>File has not been saved correctly.</p>" : "<p>File has been saved correctly.</p>";
        }
    } catch(Eccezione $exc) {
        echo "<p>".$exc->getMessage()."</p>";
    }
} else {
    echo "<p>There was a problem.</p>";
}
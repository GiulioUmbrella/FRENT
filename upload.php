<?php
require_once("php/class_ImageManager.php");

if(isset($_POST['submit'])) {
    $imageManager = new ImageManager("uploads/");
    try {
        for($i = 0, $numFiles = $imageManager->countFiles("fileToUpload"); $i < $numFiles; $i++) {
            $imageManager->setFile("fileToUpload", "test$i", $i);
            echo "File name: " . $imageManager->fileName() . ", " . "file extension: " . $imageManager->fileExtension() . ".<br />";
            echo $imageManager->saveFile() === false ? "File has not been saved correctly.<br />" : "File has been saved correctly.<br />";
        }
    } catch(Eccezione $exc) {
        echo $exc->getMessage();
    }
} else {
    echo "There was a problem.";
}
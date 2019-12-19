<?php
require_once("php/class_ImageManager.php");

if(isset($_POST['submit'])) {
    $imageManager = new ImageManager("uploads/");
    
    try {
        $imageManager->setFile("fileToUpload", "test");
        echo "File name: " . $imageManager->fileName() . PHP_EOL . "File extension: " . $imageManager->fileExtension() . PHP_EOL;
    
        echo $imageManager->saveFile() ? "File has been saved correctly." : "File has not been saved correctly.";
    } catch(Eccezione $exc) {
        echo $exc->getMessage();
    }
} else {
    echo "There was a problem.";
}
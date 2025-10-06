<?php
// Utilisez cette fonction pour supprimer le contenu d'un dossier spécifique
//$directoryPath = 'chemin/vers/votre/dossier'; // Remplacez par le chemin de votre dossier
//deleteDirectoryContents($directoryPath);
/*
function deleteDirectoryContents($dirPath) {
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDirectoryContents($file);
        } else {
            unlink($file);
        }
    }
}
*/
?>


<?php
function getLastPDFsForClient($clientActif, $directoryPath) {
    $pdfFiles = glob($directoryPath . $clientActif . '_*.pdf');
    if (empty($pdfFiles)) {
        return []; // Aucun fichier PDF trouvé
    }

    // Trouver la date la plus récente parmi tous les fichiers, sans tenir compte de l'heure
    $mostRecentDate = 0;
    foreach ($pdfFiles as $file) {
        $fileModificationTime = filemtime($file);
        $dateOnly = strtotime(date('Y-m-d', $fileModificationTime));

        if ($dateOnly > $mostRecentDate) {
            $mostRecentDate = $dateOnly;
        }
    }

    $latestPDFs = [];
    foreach ($pdfFiles as $file) {
        $filename = basename($file);
        $parts = explode('_', str_replace('__', '_', $filename));

        if (count($parts) < 2) {
            continue; // Ignore les fichiers mal nommés
        }

        $analytique = $parts[1];
        $fileModificationTime = filemtime($file);
        $dateOnly = strtotime(date('Y-m-d', $fileModificationTime));

        // Ignorer les fichiers créés avant la date la plus récente
        if ($dateOnly < $mostRecentDate) {
            continue;
        }

        // Vérifie si ce fichier est plus récent que le précédent pour cet analytique
        if (!isset($latestPDFs[$analytique]) || $fileModificationTime > $latestPDFs[$analytique]['timestamp']) {
            $latestPDFs[$analytique] = [
                'file' => $file,
                'timestamp' => $fileModificationTime
            ];
        }
    }

    // Retourne uniquement les chemins des fichiers
    return array_map(function ($entry) {
        return $entry['file'];
    }, $latestPDFs);
}

?>
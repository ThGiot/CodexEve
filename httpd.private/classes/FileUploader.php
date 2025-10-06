<?php
class FileUploader {
    private $file;
    private $maxFileSize;
    private $allowedMimeTypes;
    private $uploadDir;

    public function __construct($file, $uploadDir, $maxFileSize = 2 * 1024 * 1024, $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif','image/jpg']) {
        $this->file = $file;
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = $maxFileSize;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    public function upload() {
        $this->checkFileError();
        $this->checkFileSize();
        $this->checkFileType();
        $this->checkUploadedFile();

        $newFileName = $this->generateFileName();
        $destPath = $this->uploadDir . $newFileName;

        $this->moveUploadedFile($destPath);
        
        return $newFileName;
    }

    private function checkFileError() {
        if ($this->file['error'] !== 0) {
            throw new Exception("Erreur lors du téléchargement du fichier.");
        }
    }

    private function checkFileSize() {
        if ($this->file['size'] >= $this->maxFileSize) {
            throw new Exception("Le fichier est trop volumineux.");
        }
    }

    private function checkFileType() {
        $realMimeType = mime_content_type($this->file['tmp_name']);
        if (!in_array($realMimeType, $this->allowedMimeTypes)) {
            throw new Exception("Type de fichier non autorisé.");
        }
    }

    private function checkUploadedFile() {
        if (!is_uploaded_file($this->file['tmp_name'])) {
            throw new Exception("Le fichier n'a pas été téléchargé via HTTP POST.");
        }
    }

    private function generateFileName() {
        return uniqid('avatar_', true) . '.' . strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION));
    }

    private function moveUploadedFile($destPath) {
        if (!move_uploaded_file($this->file['tmp_name'], $destPath)) {
            throw new Exception("Erreur lors du déplacement du fichier téléchargé.");
        }
        // Ensure the file does not have execute permissions
        chmod($destPath, 0644);
    }
}

?>
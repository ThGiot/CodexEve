<?php
class ClearInput {
    public static function prepare(string $input): string {
        // Suppression des espaces en début et en fin de chaîne
        $input = trim($input);

        // Suppression des balises HTML
        $input = strip_tags($input);

        // Conversion des caractères spéciaux en entités HTML
        $input = htmlspecialchars($input);

        // Application d'autres filtres si nécessaire

        return $input;
    }
}
?>
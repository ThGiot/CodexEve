<?php
class PhoneNumberHandler {
    private $defaultCountryCode;

    public function __construct($defaultCountryCode = '+32') {
        $this->defaultCountryCode = $defaultCountryCode;
    }

    public function formatNumber($phoneNumber) {
        // Supprime les espaces, tirets et autres caractères non numériques
        $cleanedNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Si le numéro commence par '00', remplacez-le par '+'
        if (substr($cleanedNumber, 0, 2) === "00") {
            return "+" . substr($cleanedNumber, 2);
        }
        
        // Traitement des numéros français commençant par '06'
        if (substr($cleanedNumber, 0, 2) === "06") {
            return "+33" . substr($cleanedNumber, 1);
        }
        
        // Si le numéro commence par '0' (cas des numéros locaux belges comme 0498272849)
        if ($cleanedNumber[0] === "0") {
            return $this->defaultCountryCode . substr($cleanedNumber, 1);
        }
        
        // Si le numéro n'a ni '+' ni '0' comme premier caractère et a une longueur de 9 chiffres (longueur typique d'un numéro belge sans préfixe), considérez-le comme un numéro belge
        if ($cleanedNumber[0] !== "+" && $cleanedNumber[0] !== "0" && strlen($cleanedNumber) === 9) {
            return $this->defaultCountryCode . $cleanedNumber;
        }

        // Sinon, retournez le numéro tel quel (après nettoyage)
        return $cleanedNumber;
    }
}
?>
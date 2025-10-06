<?php
function calculerMontantTotal(&$facture) {
    // Parcourir chaque analytique
    foreach ($facture as $analytiqueId => &$analytiqueData) {
        // Parcourir chaque prestataire sous cet analytique
        foreach ($analytiqueData as $p_id => &$prestataireData) {
            $montantTotal = 0; // Initialiser le montant total à 0 pour ce prestataire

            // Parcourir chaque prestation du prestataire
            foreach ($prestataireData['prestations'] as $prestation) {
                $montantTotal += $prestation['montant']; // Additionner le montant de la prestation
            }

            // Mettre à jour le montant total pour ce prestataire
            $prestataireData['total_montant'] = $montantTotal;
        }
    }
}

?>
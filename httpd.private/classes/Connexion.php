<?php
class Connexion {
    private $dbh; // Instance de PDO
    
    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }
   
    public function login($login, $password) {
        // Préparer la requête pour vérifier l'existence du couple identifiant/mot de passe
        $query = "SELECT * FROM user WHERE login = :login";
        $stmt = $this->dbh->prepare($query);
        
        // Exécuter la requête en liant les paramètres
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        
        // Récupérer le hash du mot de passe stocké en base de données
        $resultat= $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($resultat)) {
            // Aucun utilisateur trouvé avec ce login
            return 100;
        }
        if($resultat[0]['validated'] != 1){
            
            return 101;
        }
        $resultat = $resultat[0];
        $hashedPassword = $resultat['password'];
        // Vérifier si le couple identifiant/mot de passe existe
        if (password_verify($password, $hashedPassword)) {
            // Authentification réussie
            return $resultat;
        } else {
            // Authentification échouée
            return 100;
        }
    }
}


?>
<?php
class Tag {
    private $pdo;

   
    public function __construct($db) {
        $this->pdo = $db;
    }

    public function ajouterTag($nom) {
        try {
            $query = "INSERT INTO tag (nom) VALUES (:nom)";
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);

            $stmt->execute();

           
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
}
?>

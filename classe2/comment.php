<?php
class Commentaire {
    private $pdo;
    private $idCommentaire;
    private $contenu;
    private $dateCommentaire;
    private $idArticle;
    private $idClient;

    public function __construct($db) {
        $this->pdo = $db;
    }

    private function setAttributes($contenu, $dateCommentaire, $idArticle, $idClient) {
        $this->contenu = $contenu;
        $this->dateCommentaire = $dateCommentaire;
        $this->idArticle = $idArticle;
        $this->idClient = $idClient;
    }

    public function ajouterComment($contenu, $idArticle, $idClient) {
        try {
            $dateCommentaire = date('Y-m-d H:i:s'); 

            $this->setAttributes($contenu, $dateCommentaire, $idArticle, $idClient);

            $query = "INSERT INTO commentaire (contenu, dateCommentaire, idArticle, idClient) 
                      VALUES (:contenu, :dateCommentaire, :idArticle, :idClient)";
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(':contenu', $this->contenu, PDO::PARAM_STR);
            $stmt->bindParam(':dateCommentaire', $this->dateCommentaire, PDO::PARAM_STR);
            $stmt->bindParam(':idArticle', $this->idArticle, PDO::PARAM_INT);
            $stmt->bindParam(':idClient', $this->idClient, PDO::PARAM_INT);

            $stmt->execute();

            // Set the idCommentaire attribute with the last inserted ID
            $this->idCommentaire = $this->pdo->lastInsertId();

            return "Commentaire ajouté avec succès! ID du commentaire : " . $this->idCommentaire;
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function modifierComment($idCommentaire, $contenu) {
        try {
            $query = "UPDATE commentaire SET contenu = :contenu WHERE idCommentaire = :idCommentaire";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
            $stmt->bindParam(':idCommentaire', $idCommentaire, PDO::PARAM_INT);

            $stmt->execute();

            return "Commentaire mis à jour avec succès!";
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }

    public function supprimerComment($idCommentaire) {
        try {
            $query = "DELETE FROM commentaire WHERE idCommentaire = :idCommentaire";
            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(':idCommentaire', $idCommentaire, PDO::PARAM_INT);

            $stmt->execute();

            return "Commentaire supprimé avec succès!";
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
}
?>

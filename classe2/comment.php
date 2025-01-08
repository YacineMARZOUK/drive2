<?php
class Commentaire {
    private $pdo;

  
    public function __construct($db) {
        $this->pdo = $db;
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

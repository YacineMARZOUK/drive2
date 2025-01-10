<?php
class Tag {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getArticleTags($articleId) {
        try {
            $sql = "SELECT t.* 
                    FROM tag t 
                    JOIN article_tag at ON t.idTag = at.id_tag 
                    WHERE at.id_article = :articleId";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['articleId' => $articleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting article tags: " . $e->getMessage());
            return [];
        }
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

    public function getAllTags() {
        try {
            $sql = "SELECT * FROM tag";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting tags: " . $e->getMessage());
            return [];
        }
    }
}
?>
<?php
class ArticleTag {
    private $pdo;

   
    public function __construct($db) {
        $this->pdo = $db;
    }


    public function filtrerParTag($idTag) {
        try {
           
            $query = "
                SELECT article.*
                FROM article
                JOIN ArticleTag ON article.idArticle = ArticleTag.idArticle
                WHERE ArticleTag.idTag = :idTag";
            
            $stmt = $this->pdo->prepare($query);


            $stmt->bindParam(':idTag', $idTag, PDO::PARAM_INT);


            $stmt->execute();

  
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
}
?>
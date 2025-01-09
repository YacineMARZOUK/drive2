<?php

class Article {
    protected $idArticle; 
    protected $titre;  
    protected $contenu;  
    protected $datePubl; 
    protected $idClient; 
    protected $idTheme;
    protected $imgsrc;

    public function __construct($titre, $contenu, $datePubl, $idClient, $idTheme, $imgsrc) {
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->datePubl = $datePubl;
        $this->idClient = $idClient;
        $this->idTheme = $idTheme;
        $this->imgsrc = $imgsrc;
    }

    /**
     * Ajouter un article à la base de données.
     */
    public function ajouterArticle($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO article ( titre, contenu, datePublication, idClient, idTheme, imgsrc) 
                                   VALUES (:titre, :contenu, :datePublication, :idClient, :idTheme, :imgsrc)");
            $stmt->bindParam(':titre', $this->titre, PDO::PARAM_STR);
            $stmt->bindParam(':contenu', $this->contenu, PDO::PARAM_STR);
            $stmt->bindParam(':datePublication', $this->datePubl, PDO::PARAM_STR);
            $stmt->bindParam(':idClient', $this->idClient, PDO::PARAM_INT);
            $stmt->bindParam(':idTheme', $this->idTheme, PDO::PARAM_INT);
            $stmt->bindParam(':imgsrc', $this->imgsrc, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "Article ajouté avec succès.";
            } else {
                echo "Erreur lors de l'ajout de l'article.";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    public static function rechercher($pdo, $motCle) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM article WHERE titre LIKE :motCle");
            $motCle = '%' . $motCle . '%';
            $stmt->bindParam(':motCle', $motCle, PDO::PARAM_STR);
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($articles)) {
                return $articles;
            } else {
                return "Aucun article trouvé.";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>

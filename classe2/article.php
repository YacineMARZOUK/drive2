<?php
require_once("../classes/Database.php");

class Article {
    protected $idArticle; 
    protected $titre;  
    protected $contenu;  
    protected $datePubl; 
    protected $idClient; 
    protected $idTheme;

    public function __construct($idArticle, $titre, $contenu, $datePubl, $idClient, $idTheme) {
        $this->idArticle = $idArticle;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->datePubl = $datePubl;
        $this->idClient = $idClient;
        $this->idTheme = $idTheme;
    }

    /**
     * Ajouter un article à la base de données.
     */
    public function ajouterArticle($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO article (idArticle, titre, contenu, datePublication, idClient, idTheme) 
                                   VALUES (:idArticle, :titre, :contenu, :datePublication, :idClient, :idTheme)");
            $stmt->bindParam(':idArticle', $this->idArticle, PDO::PARAM_INT);
            $stmt->bindParam(':titre', $this->titre, PDO::PARAM_STR);
            $stmt->bindParam(':contenu', $this->contenu, PDO::PARAM_STR);
            $stmt->bindParam(':datePublication', $this->datePubl, PDO::PARAM_STR);
            $stmt->bindParam(':idClient', $this->idClient, PDO::PARAM_INT);
            $stmt->bindParam(':idTheme', $this->idTheme, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "Article ajouté avec succès.";
            } else {
                echo "Erreur lors de l'ajout de l'article.";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    /**
     * Rechercher des articles dans la base de données par titre.
     */
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

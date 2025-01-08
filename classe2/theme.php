<?php
class Theme {
    protected $idTheme;
    protected $theme;
    protected $db;

    // Constructor
    public function __construct($db) {
        $this->db = $db;
    }

    // Getter for idTheme
    public function getIdTheme() {
        return $this->idTheme;
    }

    // Setter for idTheme
    public function setIdTheme($idTheme) {
        $this->idTheme = $idTheme;
    }

    // Getter for theme
    public function getTheme() {
        return $this->theme;
    }

    // Setter for theme
    public function setTheme($theme) {
        $this->theme = $theme;
    }

    // Method to add a new theme
    public function AjouterTheme() {
        try {
            $sql = "INSERT INTO theme (theme) VALUES (:theme)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':theme', $this->theme, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                
                echo "Erreur lors de l'ajout du thème.";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>


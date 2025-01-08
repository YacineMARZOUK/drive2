<?php
require_once("classes/Database.php");
require_once("classe2/theme.php");
require_once("classe2/tag.php");

$database = new Database(); 
$pdo = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['theme'])) {
        $theme = new Theme($pdo);
        $theme->setTheme($_POST['theme_name']);
        $theme->AjouterTheme();
    }}
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_tag'])) {
        $tag = new Tag($pdo);
        echo $tag->ajouterTag($_POST['nom_tag']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Thèmes et Tags</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .text-custom { color: #ff0000; } /* Red text */
        .bg-custom { background-color: #ff0000; } /* Red background */
        .hover\:bg-custom:hover { background-color: #cc0000; } /* Darker red on hover */
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto flex items-center justify-between py-4 px-6">
            <div class="flex items-center">
                <img src="img/logo.png" alt="Logo" class="h-10 mr-3">
                <a href="#" class="text-lg font-bold text-custom">Drive & Loc</a>
            </div>
            <ul class="hidden md:flex space-x-6 text-gray-800">
                <li><a href="admin/allReservation.php" class="text-black hover:text-custom">Add a vehicule </a></li>
                
            </ul>
        </div>
    </nav>

    <!-- Add Theme Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-custom mb-8 text-center">Ajouter un Thème</h2>

            <form method="POST" action="" class="mx-auto bg-gray-100 p-8 rounded-lg shadow-lg" style="max-width: 500px;">
                <div class="mb-4">
                    <label for="theme" class="block text-lg font-semibold text-gray-700">Nom du Thème</label>
                    <input type="text" id="theme" name="theme_name" class="w-full px-4 py-2 mt-2 border border-gray-300 text-black rounded-lg" placeholder="Entrez le nom du thème" required>
                </div>
                <button type="submit" name="theme" class="w-full px-6 py-3 bg-custom text-white rounded-full hover:bg-gray-700">Ajouter</button>
            </form>
        </div>
    </section>

    <!-- Add Tag Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-custom mb-8 text-center">Ajouter un Tag</h2>

            <form method="POST" action="" class="mx-auto bg-white p-8 rounded-lg shadow-lg" style="max-width: 500px;">
                <div class="mb-4">
                    <label for="nom_tag" class="block text-lg font-semibold text-gray-700">Nom du Tag</label>
                    <input type="text" id="nom_tag" name="nom_tag" class="w-full px-4 py-2 mt-2 border border-gray-300 text-black rounded-lg" placeholder="Entrez le nom du tag" required>
                </div>
                <button type="submit" name="ajouter_tag" class="w-full px-6 py-3 bg-custom text-white rounded-full hover:bg-gray-700">Ajouter</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white py-8">
        <div class="container mx-auto flex flex-col md:flex-row justify-between">
            <div class="mb-4">
                <h3 class="text-xl font-bold">Thèmes & Tags</h3>
                <p>Gérez vos thèmes et tags facilement.</p>
            </div>
            <ul class="flex space-x-6">
                <li><a href="#" class="hover:text-custom">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-custom">Terms of Service</a></li>
                <li><a href="#" class="hover:text-custom">Contact Us</a></li>
            </ul>
            <div class="flex space-x-4">
                <a href="#"><i class="fa-brands fa-facebook text-2xl"></i></a>
                <a href="#"><i class="fa-brands fa-x-twitter text-2xl"></i></a>
                <a href="#"><i class="fa-brands fa-instagram text-2xl"></i></a>
            </div>
        </div>
    </footer>

</body>
</html>

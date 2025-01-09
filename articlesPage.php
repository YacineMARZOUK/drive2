<?php 
    session_start();

    if (!isset($_SESSION['idClient'])) {

        header("Location: login.php");
        exit();
    }
    
    $idClient = $_SESSION['idClient'];
    
    

    require_once 'classes/Database.php';
    require_once "classe2/article.php";
  
    $db = new Database();
    $pdo = $db->getConnection();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérification et traitement de l'image
        $imgsrc = null;
        if (isset($_FILES['article_image']) && $_FILES['article_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/'; // Répertoire où les images seront enregistrées
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imgsrc = $uploadDir . basename($_FILES['article_image']['name']);
            if (!move_uploaded_file($_FILES['article_image']['tmp_name'], $imgsrc)) {
                echo "Erreur lors de l'upload de l'image.";
                exit;
            }
        }
    
        // Données du formulaire
        $title = $_POST['article_title'];
        $content = $_POST['article_content'];
        $datePublished = date("Y-m-d H:i:s");
        $idClientt = $idClient; // Utilisateur connecté
        $idTheme = 11; // Thème sélectionné
    
        // Création d'une instance d'Article
        $article = new Article($title, $content, $datePublished, $idClientt, $idTheme, $imgsrc);
    
        // Ajout dans la base de données
        $article->ajouterArticle($pdo);
    
        // Redirection après succès
        header("Location: success_page.php");
        exit;
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Articles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        #add-article-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 50;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 700px;
            max-height: 90%;
            overflow-y: auto;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            background-color: #e2e8f0;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
            margin: 0.25rem;
        }

        .tag span {
            margin-right: 0.5rem;
        }

        .tag i {
            cursor: pointer;
        }
        .text-custom { color: #ff0000; }
        .bg-custom { background-color: #ff0000; }
        .hover\:bg-custom:hover { background-color: #cc0000; }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <h1 class="text-3xl font-bold text-custom">Drive & Loc Blog</h1>
            <nav class="hidden md:flex space-x-6">
                <a href="classes/client.php" class="bg-custom text-white py-2 px-4 rounded-lg hover\:bg-custom:hover transition">book Now</a>
                <a href="#explore" class="text-black hover:text-custom">Explore</a>
                <a href="#features" class="text-black hover:text-custom">Features</a>
                <a href="#contact" class="text-black hover:text-custom">Contact</a>
            </nav>
            <?php
                if(!empty($idClient)){
                    echo '<a href="../pages/dashboard.php" class="bg-custom text-white py-2 px-4 rounded-lg hover\:bg-custom:hover transition">Dashboard</a>';
                }else{
                    echo '<a href="../login/login.php" class="bg-custom text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Login</a>';
                }
            ?>
        </div>

        <div class="bg-gray-100 border-t border-b">
            <div class="container mx-auto flex justify-between items-center py-3 px-6 overflow-x-auto">
                <div class="flex space-x-4">
                    <div class="filter-item text-gray-700 bg-white py-2 px-4 rounded-lg shadow-sm hover:bg-blue-100 transition cursor-pointer" onclick="filterArticles('All')">All</div>
                    <div class="filter-item text-gray-700 bg-white py-2 px-4 rounded-lg shadow-sm hover:bg-blue-100 transition cursor-pointer" onclick="filterArticles('Technology')">Technology</div>
                    <div class="filter-item text-gray-700 bg-white py-2 px-4 rounded-lg shadow-sm hover:bg-blue-100 transition cursor-pointer" onclick="filterArticles('Travel')">Travel</div>
                    <div class="filter-item text-gray-700 bg-white py-2 px-4 rounded-lg shadow-sm hover:bg-blue-100 transition cursor-pointer" onclick="filterArticles('Health')">Health</div>
                    <div class="filter-item text-gray-700 bg-white py-2 px-4 rounded-lg shadow-sm hover:bg-blue-100 transition cursor-pointer" onclick="filterArticles('Education')">Education</div>
                    <div class="filter-item text-gray-700 bg-white py-2 px-4 rounded-lg shadow-sm hover:bg-blue-100 transition cursor-pointer" onclick="filterArticles('Lifestyle')">Lifestyle</div>
                </div>

                <div class="relative">
                    <input 
                        type="text" 
                        id="search-input" 
                        placeholder="Search articles..." 
                        class="py-2 px-4 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-600 focus:outline-none w-64"
                        oninput="searchArticles()"
                    >
                    <i class="fa fa-search absolute right-3 top-2.5 text-gray-400"></i>
                </div>
            </div>
        </div>
    </header>

    <section class="py-16 bg-gray-100">
        <div class="container mx-auto">
            <div class="flex justify-between items-center mb-12 px-10">
                <h2 class="text-4xl font-bold text-gray-800">Articles</h2>
                <button onclick="openModal()" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">Add Article</button>
            </div>
            <div id="articles-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 px-8">
                <!-- Articles will be dynamically inserted here -->
            </div>
            
            <div class="flex justify-center mt-8 space-x-2" id="pagination">
                <!-- Pagination buttons will be dynamically inserted here -->
            </div>
        </div>
    </section>

    <!-- Add Article Modal -->
    <div id="overlay"></div>
    <div id="add-article-modal">
        <h3 class="text-2xl font-bold mb-6">Add New Article</h3>
    
        <form id="add-article-form" action="" method="POST" method="POST" enctype="multipart/form-data">
            <div class="mb-6">
                <label for="article-image" class="block text-gray-700 font-semibold mb-2">Article Image</label>
                <input id="article-image" name="article_image" type="file" accept="image/*" class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
            <div class="mb-6">
                <label for="article-title" class="block text-gray-700 font-semibold mb-2">Title</label>
                <input id="article-title" name="article_title" type="text" class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none" placeholder="Enter the article title" required>
            </div>
            <div class="mb-6">
                <label for="article-content" class="block text-gray-700 font-semibold mb-2">Content</label>
                <textarea id="article-content" name="article_content" rows="10" class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none" placeholder="Write your article here..." required></textarea>
            </div>
            <div class="mb-6">
    <label for="tag-select" class="block text-gray-700 font-semibold mb-2">Tags</label>
    <div id="tag-container" class="flex flex-wrap border p-4 rounded-lg">
        <select id="tag-select" class="flex-grow p-2 focus:outline-none">
            <option value="" disabled selected>Select a tag</option>
            <option value="Technology">Technology</option>
            <option value="Travel">Travel</option>
            <option value="Health">Health</option>
            <option value="Education">Education</option>
            <option value="Lifestyle">Lifestyle</option>
        </select>
    </div>
    <small class="text-gray-500">Select a tag from the dropdown</small>
    <input type="hidden" id="tags-hidden" name="tags" value="">
</div>
            <div class="flex justify-end space-x-4">
                <button type="button" class="bg-gray-300 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-400 transition" id="cancel-button">Cancel</button>
                <button type="submit" class=" bg-custom text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition">Add Article</button>
            </div>
        </form>
    </div>

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

    <script>
    // Modal elements and state management
    const overlay = document.getElementById('overlay');
    const modal = document.getElementById('add-article-modal');
    const cancelButton = document.getElementById('cancel-button');
    const tagContainer = document.getElementById('tag-container');
    const tagInput = document.getElementById('tag-input');
    const tagsHidden = document.getElementById('tags-hidden');
    let tags = new Set();

    // Sample articles data (replace with your actual data)
    const articles = Array.from({ length: 50 }, (_, i) => ({
        id: i + 1,
        title: `Article ${i + 1}`,
        author: 'John Doe',
        date: 'Jan 7, 2025',
        content: 'Sample content...',
        image: 'https://via.placeholder.com/400x250'
    }));

    // Pagination settings
    const itemsPerPage = 9;
    let currentPage = 1;

    // Modal functions
    function openModal() {
        modal.style.display = 'block';
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
    }

    function closeModal() {
        modal.style.display = 'none';
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
        resetForm();
    }

    function resetForm() {
        document.getElementById('add-article-form').reset();
        tags.clear();
        updateHiddenInput();
        // Remove all tags except the input
        while (tagContainer.firstChild) {
            if (tagContainer.firstChild !== tagInput) {
                tagContainer.removeChild(tagContainer.firstChild);
            } else {
                break;
            }
        }
    }

    // Tag handling functions
    function handleTagInput(event) {
        if (event.type === 'blur' || event.key === 'Enter' || event.key === ',') {
            event.preventDefault();
            const value = tagInput.value.trim();
            if (value) {
                const tagValues = value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
                tagValues.forEach(addTag);
                tagInput.value = '';
            }
        }
    }

    function addTag(tag) {
        if (!tags.has(tag) && tag !== '') {
            tags.add(tag);
            const tagElement = document.createElement('div');
            tagElement.className = 'tag';
            tagElement.innerHTML = `
                <span>${tag}</span>
                <i class="fas fa-times text-gray-500" onclick="removeTag(this, '${tag}')"></i>
            `;
            tagContainer.insertBefore(tagElement, tagInput);
            updateHiddenInput();
        }
    }

    function removeTag(element, tag) {
        tags.delete(tag);
        element.parentElement.remove();
        updateHiddenInput();
    }

    function updateHiddenInput() {
        tagsHidden.value = Array.from(tags).join(',');
    }

    // Article display and pagination functions
    function displayArticles(page) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedArticles = articles.slice(start, end);
        
        const container = document.getElementById('articles-container');
        container.innerHTML = paginatedArticles.map(article => `
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition overflow-hidden">
                <img src="${article.image}" alt="Article Image" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">${article.title}</h3>
                    <p class="text-sm text-gray-600 mb-4">By ${article.author} | ${article.date}</p>
                    <p class="text-gray-700 mb-4">${article.content}</p>
                    <button class="bg-custom text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Read More</button>
                </div>
            </div>
        `).join('');
        
        updatePagination();
    }

    function updatePagination() {
        const totalPages = Math.ceil(articles.length / itemsPerPage);
        const pagination = document.getElementById('pagination');
        
        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `
            <button 
                onclick="changePage(${currentPage - 1})" 
                class="px-4 py-2 rounded-lg ${currentPage === 1 ? 'bg-gray-300 cursor-not-allowed' : 'bg-custom text-white hover:bg-blue-700'}"
                ${currentPage === 1 ? 'disabled' : ''}
            >
                Previous
            </button>
        `;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || 
                i === totalPages || 
                (i >= currentPage - 1 && i <= currentPage + 1)
            ) {
                paginationHTML += `
                    <button 
                        onclick="changePage(${i})" 
                        class="px-4 py-2 rounded-lg ${currentPage === i ? 'bg-custom text-white' : 'bg-gray-200 hover:bg-gray-300'}"
                    >
                        ${i}
                    </button>
                `;
            } else if (
                i === currentPage - 2 || 
                i === currentPage + 2
            ) {
                paginationHTML += `<span class="px-2">...</span>`;
            }
        }
        
        // Next button
        paginationHTML += `
            <button 
                onclick="changePage(${currentPage + 1})" 
                class="px-4 py-2 rounded-lg ${currentPage === totalPages ? 'bg-gray-300 cursor-not-allowed' : 'bg-custom text-white hover:bg-blue-700'}"
                ${currentPage === totalPages ? 'disabled' : ''}
            >
                Next
            </button>
        `;
        
        pagination.innerHTML = paginationHTML;
    }

    function changePage(page) {
        const totalPages = Math.ceil(articles.length / itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            displayArticles(currentPage);
        }
    }

    // Filter articles function
    function filterArticles(category) {
        // Add your filtering logic here
        console.log('Filtering by:', category);
    }

    // Search articles function
    function searchArticles() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        // Add your search logic here
        console.log('Searching for:', searchTerm);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the page
        displayArticles(currentPage);
        
        // Set up event listeners
        tagInput.addEventListener('keydown', handleTagInput);
        tagInput.addEventListener('blur', handleTagInput);
        
        cancelButton.addEventListener('click', closeModal);
        
        overlay.addEventListener('click', closeModal);
        
        document.getElementById('add-article-form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Handle form submission here
            console.log('Tags:', tagsHidden.value);
            closeModal();
        });
    });
</script>
</body>
</html>
<?php 
session_start();
$idClient = isset($_SESSION['idClient']) ? $_SESSION['idClient'] : null;

require_once 'classes/Database.php';
require_once "classe2/article.php";
require_once "classe2/theme.php";
require_once "classe2/Tag.php";
require_once "classe2/comment.php";

$db = new Database();
$pdo = $db->getConnection();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = $_GET['id'];
    try {
        $article = Article::GetArticleDetails($pdo, $article_id);
        if (!$article) {
            header("Location: blog.php");
            exit();
        }
        // Get article tags
        $tagObj = new Tag($pdo);
        $articleTags = $tagObj->getArticleTags($article_id);
    } catch (Exception $e) {
        error_log("Error fetching article: " . $e->getMessage());
        header("Location: blog.php");
        exit();
    }
}

// Handle comments
$commentaire = new Commentaire($pdo);

// Handle comments
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!$idClient) {
        header("Location: login/login.php");
        exit();
    }

    if (isset($_POST['addComment'])) {
        $content = htmlspecialchars(trim($_POST['content']));
        if (!empty($content)) {
            try {
                $result = $commentaire->ajouterComment($content, $article_id, $idClient);
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $article_id);
                exit();
            } catch (Exception $e) {
                error_log("Error adding comment: " . $e->getMessage());
            }
        }
    }

    if (isset($_POST['editComment'])) {
        $content = htmlspecialchars(trim($_POST['newContent']));
        $commentId = htmlspecialchars(trim($_POST['editComment']));
        if (!empty($content) && !empty($commentId)) {
            try {
                $result = $commentaire->modifierComment($commentId, $content);
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $article_id);
                exit();
            } catch (Exception $e) {
                error_log("Error editing comment: " . $e->getMessage());
            }
        }
    }

    if (isset($_POST['delete'])) {
        $commentId = htmlspecialchars(trim($_POST['delete']));
        if (!empty($commentId)) {
            try {
                $result = $commentaire->supprimerComment($commentId);
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $article_id);
                exit();
            } catch (Exception $e) {
                error_log("Error deleting comment: " . $e->getMessage());
            }
        }
    }
}

// Fetch comments - Add this method to your Commentaire class
try {
    $stmt = $pdo->prepare("
        SELECT c.*, cl.nom 
        FROM commentaire c 
        JOIN client cl ON c.idClient = cl.idClient 
        WHERE c.idArticle = ? 
        ORDER BY c.dateCommentaire DESC
    ");
    $stmt->execute([$article_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching comments: " . $e->getMessage());
    $comments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['titre']); ?> - Drive & Loc Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        #comment-modal {
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
            max-width: 500px;
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
        .text-custom { color: #ff0000; }
        .bg-custom { background-color: #ff0000; }
        .hover\:bg-custom:hover { background-color: #cc0000; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <h1 class="text-3xl font-bold text-custom">Drive & Loc Blog</h1>
            <nav class="hidden md:flex space-x-6">
            <a href="articlesPage.php" class="bg-custom text-white py-2 px-4 rounded-lg hover\:bg-custom:hover transition">Articles</a>
                <a href="../blog.php" class="text-gray-700 hover:text-custom transition">Articles</a>
                <a href="../../index.php#features" class="text-gray-700 hover:text-custom transition">Features</a>
                <a href="../../index.php#contact" class="text-gray-700 hover:text-custom transition">Contact</a>
            </nav>
            <?php if($idClient): ?>
                <a href="../pages/dashboard.php" class="bg-custom text-white py-2 px-4 rounded-lg hover:bg-custom transition">Dashboard</a>
            <?php else: ?>
                <a href="../login/login.php" class="bg-custom text-white py-2 px-4 rounded-lg hover:bg-custom transition">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Article Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8">
                <!-- Article Image -->
                <img src="<?php echo htmlspecialchars($article['imgsrc'] ?? '../../img/imgArticles/default.jpg'); ?>" 
                     alt="Article Image" 
                     class="w-full h-72 object-cover rounded-md mb-6">
                
                <!-- Article Metadata -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($article['titre']); ?></h1>
                    <p class="text-sm text-gray-600">Published on <?php echo date('F j, Y', strtotime($article['datePublication'])); ?></p>
                </div>
                
                <!-- Tags -->
                <?php if (!empty($articleTags)): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Tags:</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach($articleTags as $tag): ?>
                            <span class="bg-blue-100 text-blue-600 py-1 px-3 rounded-full text-sm">
                                <?php echo htmlspecialchars($tag['nom']); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Article Content -->
                <div class="text-gray-700 leading-relaxed mb-8">
                    <p class="mb-4"><?php echo nl2br(htmlspecialchars($article['contenu'])); ?></p>
                </div>

                <!-- Comment Section -->
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Comments</h3>
                    
                    <?php if($idClient): ?>
                    <div class="mt-8 mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Add a Comment:</h4>
                        <form method="POST">
                            <textarea name="content" class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none mb-4" 
                                      placeholder="Write your comment here..." rows="4" required></textarea>
                                      <button name="addComment" class="bg-custom text-white py-2 px-4 rounded-lg hover:bg-custom transition">
                                        Submit
                                      </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <!-- Existing Comments -->
                    <div class="space-y-6" id="comments-section">
    <?php if (!empty($comments)): ?>
        <?php foreach($comments as $comment): ?>
            <div class="border-b pb-4 flex justify-between items-start">
                <div>
                    <p class="text-gray-800">
                        <span class="font-bold"><?php echo htmlspecialchars($comment['nom']); ?></span> - 
                        <?php echo htmlspecialchars($comment['contenu']); ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Posted on <?php echo date('F j, Y', strtotime($comment['dateCommentaire'])); ?>
                    </p>
                </div>
                <?php if($idClient && $comment['idClient'] == $idClient): ?>
                    <div class="flex space-x-4">
                        <button type="button" class="text-blue-600 hover:underline" 
                                onclick="openModal('<?php echo htmlspecialchars($comment['nom']); ?>', 
                                                  '<?php echo htmlspecialchars($comment['contenu']); ?>', 
                                                  <?php echo $comment['idCommentaire']; ?>)">
                            Modify
                        </button>
                        <form method="POST" class="inline">
                            <button type="submit" name="delete" value="<?php echo $comment['idCommentaire']; ?>" 
                                    class="text-red-600 hover:underline">
                                Remove
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-600">No comments yet. Be the first to comment!</p>
    <?php endif; ?>
</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for Comment Modification -->
    <div id="overlay"></div>
    <div id="comment-modal">
        <h3 class="text-xl font-bold mb-4">Modify Comment</h3>
        <form method="POST" id="modify-comment-form">
            <textarea name="newContent" id="comment-text" class="w-full p-4 border rounded-lg focus:ring-2 focus:ring-blue-600 focus:outline-none mb-4" 
                      rows="4" required></textarea>
            <div class="flex justify-end space-x-4">
                <button type="button" class="bg-gray-300 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-400 transition" 
                        onclick="closeModal()">Cancel</button>
                <button type="submit" name="editComment" id="getButton" 
                        class="bg-custom text-white py-2 px-6 rounded-lg hover:bg-custom transition">Save</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-black text-white py-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 Drive & Loc Blog. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        const overlay = document.getElementById('overlay');
        const modal = document.getElementById('comment-modal');
        const commentText = document.getElementById('comment-text');
        const getButton = document.getElementById('getButton');

        function openModal(name, comment, id) {
            commentText.value = comment;
            getButton.value = id;
            modal.style.display = 'block';
            overlay.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
            overlay.style.display = 'none';
        }

        // Close modal when clicking outside
        overlay.addEventListener('click', closeModal);
    </script>
</body>
</html>
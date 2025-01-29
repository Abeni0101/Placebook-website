<?php
include_once 'model.class.php';
class Controller {
    // User-related methods
    public function registerUser($fullname, $email, $password, $profileImage = null, $country) {
        $userModel = new User();
        return $userModel->registerUser($fullname,$email, $password, $profileImage, $country);
    }

    public function loginUser($email, $password) {
        session_start();
        $userModel = new User();
        $adminModel = new Admin();
        $user = $userModel->authenticateUser($email, $password);
        $admin = $adminModel->authenticateAdmin($email, $password);
        if ($user) {
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['status'] = $user['status'];
            header('location:index.php?page=feed');
             exit;
        }
        elseif($admin){
            $_SESSION['admin_id'] = $admin['admin_id'];
            header('location:index.php');
             exit;
        }
        return false;
    }
    protected function getLoggedInUser()
{
    $userId = $_SESSION['user_id']; // Get the logged-in user's ID
    $userModel = new User();
    return $userModel->getUserById($userId); // Fetch user details
}

    protected function logoutUser() {
        session_destroy();
        return true;
    }

    protected function getUserProfile($id) {
        $userModel = new User();
        return $userModel->getUserById($id);
    }

    protected function updateUserProfile($userId, $fullname, $bio, $profileImage) {
        $userModel = new User();
    
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            // Handle Profile Image Upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profile_image']['tmp_name'];
                $fileName = uniqid() . '-' . basename($_FILES['profile_image']['name']); // Ensure a unique filename
                $destination = './public/images/uploads/profiles/' . $fileName; // Use relative path from the executed script
    
                // Move uploaded file to the destination directory
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $profileImage = $fileName; // Update with the new file name
                } else {
                    echo "Error: Could not save the uploaded file.";
                }
            }
        }
    
        // Update the profile information in the database
        $userModel->updateUser($userId, $fullname, $bio, $profileImage);
        header("Location: index.php");
    }
    

    protected function deleteUser($id) {
        $userModel = new User();
        return $userModel->deleteUser($id);
    }

    // Post-related methods
    protected function listPosts() {
        $postModel = new Post();
        return $postModel->getAllPosts();
    }

    protected function viewPost($postId) {
        $postModel = new Post();
        return $postModel->getPostById($postId);
    }

    protected function createPost($userId, $content, $imagePath, $location) {
        $postModel = new Post();
        if (isset($_POST['submit'])) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                // Directory where uploaded files will be saved
                $targetDir = realpath(__DIR__ . '/../public/images/uploads/posts/') . '/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
                }
    
                $error = "";
                $success = "";
    
                // Check if the file was uploaded without errors
                if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                    $imagePath = uniqid() . "_" . basename($_FILES["image"]["name"]);
                    $targetFilePath = $targetDir . $imagePath;
                    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
                    // Validate file type
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($fileType, $allowedTypes)) {
                        // Validate file size (max 5MB)
                        if ($_FILES["image"]["size"] <= 5 * 1024 * 1024) {
                            // Move the uploaded file to the target directory
                            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                                $success = "The file has been uploaded successfully.";
                            } else {
                                $error = "Error: There was an error uploading your file.";
                            }
                        } else {
                            $error = "Error: The file is too large. Max size is 5MB.";
                        }
                    } else {
                        $error = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
                    }
                } else {
                    $error = "Error: File upload error code " . $_FILES["image"]["error"];
                }
    
                if (empty($error)) {
                    $result = $postModel->addPost($userId, $content, $imagePath, $location);
                    if ($result === "success") {
                        echo $success;
                         header("Location: ../views/main.php");
                        exit();
                    } else {
                        $error = $result;
                    }
                }
    
                if (!empty($error)) {
                    echo $error;
                }
    
            } else {
                echo "Error: Invalid request.";
            }
        }
    }
    protected function getProfilePosts($userId) {
        $postModel = new Post(); // Assuming you have a `Post` model
        return $postModel->getUserPosts($userId);
    }
    protected function deletePost($postId) {
        $postModel = new Post();
        return $postModel->deletePost($postId);
    }

    // Like-related methods
    protected function likePost($postId, $userId) {
        $likeModel = new Like();
        return $likeModel->addLike($postId, $userId);
    }

    protected function unlikePost($postId, $userId) {
        $likeModel = new Like();
        return $likeModel->removeLike($postId, $userId);
    }

    protected function getPostLikes($postId) {
        $likeModel = new Like();
        return $likeModel->getLikesByPostId($postId);
    }

    protected function hasUserLiked($postId, $userId) {
        $likeModel = new Like();
        return $likeModel->hasLiked($postId, $userId);
    }
    // save posts
    protected function savePost($user_id, $post_id) {
        $postModel = new Post();
        return $postModel->savePost($user_id, $post_id);
    }
    protected function isPostSaved($user_id, $post_id) {
        $postModel = new Post();
        return $postModel->isPostSaved($user_id, $post_id);
    }
    
    protected function unsavePost($user_id, $post_id) {
        $postModel = new Post();
        return $postModel->unsavePost($user_id, $post_id);
    }
    
    protected function getSavedPosts($user_id) {
        $postModel = new Post();
        return $postModel->getSavedPosts($user_id);
    }
    
    // Comment-related methods
    protected function addComment($postId, $userId, $content) {
        $commentModel = new Comment();
        return $commentModel->addComment($postId, $userId, $content);
    }

    protected function deleteComment($commentId) {
        $commentModel = new Comment();
        return $commentModel->deleteComment($commentId);
    }

    protected function listComments($postId) {
        $commentModel = new Comment();
        return $commentModel->getCommentsByPostId($postId);
    }

    // Friend-related methods
    protected function sendFriendRequest($userId, $friendId) {
        $friendModel = new Friend();
        return $friendModel->sendFriendRequest($userId, $friendId);
    }

    protected function acceptFriendRequest($requestId) {
        $friendModel = new Friend();
        return $friendModel->acceptFriendRequest($requestId);
    }

    protected function listFriends($userId) {
        $friendModel = new Friend();
        return $friendModel->getFriendsByUserId($userId);
    }

    // Message-related methods
    public function fetchAllUsers($userId) {
        $userModel = new User();
        return $userModel->getAllUsersExcept($userId);
    }

    // Fetch messages between the logged-in user and a selected user
    public function fetchMessages($userId, $friendId) {
        $messageModel = new Message();
        return $messageModel->getMessages($userId, $friendId);
    }

    // Send a message
    public function sendMessage($userId, $friendId, $content) {
        $messageModel = new Message();
        $messageModel->sendMessage($userId, $friendId, $content);
    }
    // Admin-related methods
    protected function blockUser($userId)
{
    $adminModel = new Admin();
    return $adminModel->blockUser($userId);
}

protected function unblockUser($userId)
{
    $adminModel = new Admin();
    return $adminModel->unblockUser($userId);
}

protected function getDashboardStats()
{
    $adminModel = new Admin();
    return $adminModel->getDashboardStats();
}

protected function getAllUsers()
{
    $adminModel = new Admin();
    return $adminModel->getAllUsers();
}


    // Trending-related methods
    public function trending() {
        $exploreModel = new Trending();
        return $exploreModel->getTopLocations();
        
        
    }
}

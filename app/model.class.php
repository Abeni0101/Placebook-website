<?php
include_once 'db.class.php';
class User extends DB {
    // Fetch user by ID
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $result = $this->executePrepared($sql, [$id]);
        if ($result) {
            return $result->fetch_assoc(); // Return a single user as an associative array
        }
        return null; // Return null if no user is found
    }
    public function getAllUsersExcept($userId) {
        $sql = "SELECT user_id, fullname, profile_image FROM users WHERE user_id != ?";
        return $this->executePrepared($sql, [$userId])->fetch_all(MYSQLI_ASSOC);
    }
    // Register a new user
    public function registerUser($fullname, $email, $password, $profile_image = null, $country) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (fullname, email, password, profile_image, country) VALUES (?, ?, ?, ?, ?)";
        return $this->executePrepared($sql, [$fullname, $email, $hashedPassword, $profile_image, $country]);
    }

    // Authenticate user login
    public function authenticateUser($email, $password) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $result = $this->executePrepared($sql, [$email]);
        if ($result) {
            $user = $result->fetch_assoc();
            if ($user && password_verify($password, $user['password'])) {
                return $user; // Return user details if authenticated
            }
        }
        return false; // Return false if authentication fails
    }

    // Update user profile
    public function updateUser($id, $fullname, $bio, $profile_image = null) {
        $sql = "UPDATE users SET fullname = ?, bio = ?, profile_image = ? WHERE user_id = ?";
        return $this->executePrepared($sql, [$fullname, $bio, $profile_image, $id]);
    }

    // Delete user by ID
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE user_id = ?";
        return $this->executePrepared($sql, [$id]);
    }
}

class Post extends DB {
    // Fetch all posts with user details, likes, and comments count
    public function getAllPosts() {
        $sql = "SELECT 
                    posts.post_id, 
                    posts.content, 
                    posts.location, 
                    posts.image_path, 
                    posts.created_at, 
                    users.fullname, 
                    users.profile_image, 
                    COUNT(DISTINCT likes.like_id) AS total_likes, 
                    COUNT(DISTINCT comments.comment_id) AS total_comments
                FROM posts
                JOIN users ON posts.user_id = users.user_id
                LEFT JOIN likes ON posts.post_id = likes.post_id
                LEFT JOIN comments ON posts.post_id = comments.post_id
                GROUP BY posts.post_id
                ORDER BY posts.created_at DESC";
        $result = $this->executePrepared($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC); // Return all posts as an associative array
        }
        return [];
    }

    // Fetch a single post by ID
    public function getPostById($id) {
        $sql = "SELECT * FROM posts WHERE post_id = ?";
        $result = $this->executePrepared($sql, [$id]);
        if ($result) {
            return $result->fetch_assoc(); // Return the post as an associative array
        }
        return null;
    }

    public function getUserPosts($userId) {
        $sql = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
        return $this->executePrepared($sql, [$userId]);
    }

    // Add a new post
    public function addPost($user_id, $content, $image_path, $location) {
        $sql = "INSERT INTO posts (user_id, content, image_path, location) VALUES (?, ?, ?, ?)";
        return $this->executePrepared($sql, [$user_id, $content, $image_path, $location]);
    }

    // Delete a post by ID
    public function deletePost($id) {
        $sql = "DELETE FROM posts WHERE post_id = ?";
        return $this->executePrepared($sql, [$id]);
    }

    // save post
    public function savePost($user_id, $post_id) {
        $sql = "INSERT INTO saved_posts (user_id, post_id) VALUES (?, ?)";
        return $this->executePrepared($sql, [$user_id, $post_id]);
    }

    // Remove a saved post from the user's favorites
    public function unsavePost($user_id, $post_id) {
        $sql = "DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?";
        return $this->executePrepared($sql, [$user_id, $post_id]);
    }

    // Check if a post is saved by the user
    public function isPostSaved($user_id, $post_id) {
        $sql = "SELECT id FROM saved_posts WHERE user_id = ? AND post_id = ?";
        $result = $this->executePrepared($sql, [$user_id, $post_id]);
        return $result->num_rows > 0; // Returns true if a row exists
    }

    // Get all saved posts for a user
    public function getSavedPosts($user_id) {
        $sql = "SELECT 
            sp.id AS save_id,
            p.post_id,
            p.content,
            p.image_path,
            p.location,
            p.created_at AS post_created_at,
            u.user_id AS post_owner_id,
            u.fullname AS post_owner_name,
            u.profile_image AS post_owner_image,
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id) AS total_likes,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) AS total_comments
        FROM saved_posts sp
        JOIN posts p ON sp.post_id = p.post_id
        JOIN users u ON p.user_id = u.user_id
        WHERE sp.user_id = ?
        ORDER BY sp.created_at DESC;
    ";
        return $this->executePrepared($sql, [$user_id]);
    }
}

class Like extends DB {
    // Add a like to a post
    public function addLike($post_id, $user_id) {
        $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
        return $this->executePrepared($sql, [$post_id, $user_id]);
    }

    // Remove a like from a post
    public function removeLike($post_id, $user_id) {
        $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
        return $this->executePrepared($sql, [$post_id, $user_id]);
    }

    // Check if a user has liked a post
    public function hasLiked($post_id, $user_id) {
        $sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
        $result = $this->executePrepared($sql, [$post_id, $user_id]);
        return $result && $result->num_rows > 0; // Return true if a like exists
    }

    // Get the total number of likes for a post
    public function getLikesByPostId($post_id) {
        $sql = "SELECT COUNT(*) AS total_likes FROM likes WHERE post_id = ?";
        $result = $this->executePrepared($sql, [$post_id]);
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total_likes'] ?? 0;
        }
        return 0;
    }
}

class Comment extends DB {
    // Add a comment to a post
    public function addComment($post_id, $user_id, $content) {
        $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
        return $this->executePrepared($sql, [$post_id, $user_id, $content]);
    }

    // Delete a comment
    public function deleteComment($comment_id) {
        $sql = "DELETE FROM comments WHERE id = ?";
        return $this->executePrepared($sql, [$comment_id]);
    }

    // Get comments for a post
    public function getCommentsByPostId($post_id) { 
        $sql = "SELECT c.comment, c.created_at, u.fullname AS user_name, u.user_id 
        FROM comments c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at";
        $result = $this->executePrepared($sql, [$post_id]);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

class Admin extends DB {
    // Get total dashboard statistics
    public function getDashboardStats() {
        $stats = [
            [
                'title' => 'Total Users',
                'icon' => 'fas fa-users',
                'count' => $this->executePrepared("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count']
            ],
            [
                'title' => 'Total Posts',
                'icon' => 'fas fa-file-alt',
                'count' => $this->executePrepared("SELECT COUNT(*) AS count FROM posts")->fetch_assoc()['count']
            ],
            [
                'title' => 'Total Comments',
                'icon' => 'fas fa-comments',
                'count' => $this->executePrepared("SELECT COUNT(*) AS count FROM comments")->fetch_assoc()['count']
            ],
            [
                'title' => 'Total Likes',
                'icon' => 'fas fa-thumbs-up',
                'count' => $this->executePrepared("SELECT COUNT(*) AS count FROM likes")->fetch_assoc()['count']
            ],
            [
                'title' => 'Location Shared',
                'icon' => 'fas fa-map-marker-alt',
                'count' => $this->executePrepared("SELECT COUNT(DISTINCT location) AS count FROM posts WHERE location IS NOT NULL")->fetch_assoc()['count']
            ],
            [
                'title' => 'Messages Count',
                'icon' => 'fas fa-envelope',
                'count' => $this->executePrepared("SELECT COUNT(*) AS count FROM messages")->fetch_assoc()['count']
            ]
        ];

        return $stats;
    }

    // Get all users
    public function getAllUsers() {
        $sql = "SELECT user_id, fullname, email, status FROM users";
        $result = $this->executePrepared($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Block a user
    public function blockUser($user_id) {
        $sql = "UPDATE users SET status = 'blocked' WHERE user_id = ?";
        return $this->executePrepared($sql, [$user_id]);
    }

    // Unblock a user
    public function unblockUser($user_id) {
        $sql = "UPDATE users SET status = 'active' WHERE user_id = ?";
        return $this->executePrepared($sql, [$user_id]);
    }
    public function authenticateAdmin($email, $password) {
        $sql = "SELECT * FROM admin WHERE email = ?";
        $result = $this->executePrepared($sql, [$email]);
        if ($result) {
            $admin = $result->fetch_assoc();
            if ($admin) {
                return $admin; // Return admin details if authenticated
            }
        }
        return false; // Return false if authentication fails
    }
}


class Friend extends DB {
    // Send a friend request
    public function sendFriendRequest($user_id, $friend_id) {
        $sql = "INSERT INTO friend_requests (user_id, friend_id) VALUES (?, ?)";
        return $this->executePrepared($sql, [$user_id, $friend_id]);
    }

    // Accept a friend request
    public function acceptFriendRequest($request_id) {
        $sql = "UPDATE friend_requests SET status = 'accepted' WHERE id = ?";
        return $this->executePrepared($sql, [$request_id]);
    }

    // Get friends of a user
    public function getFriendsByUserId($user_id) {
        $sql = "SELECT * FROM friends WHERE user_id = ? OR friend_id = ?";
        $result = $this->executePrepared($sql, [$user_id, $user_id]);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

class Message extends DB {
    public function getMessages($userId, $friendId) {
        $sql = "SELECT * FROM messages 
                WHERE (sender_id = ? AND receiver_id = ?) 
                OR (sender_id = ? AND receiver_id = ?) 
                ORDER BY created_at ASC";
        return $this->executePrepared($sql, [$userId, $friendId, $friendId, $userId])->fetch_all(MYSQLI_ASSOC);
    }

    // Insert a new message into the database
    public function sendMessage($senderId, $receiverId, $content) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
        return $this->executePrepared($sql, [$senderId, $receiverId, $content]);
    }
}

class Trending extends DB {
    // Get trending posts
    public function getTopLocations() {
        $sql = "SELECT location, COUNT(*) AS post_count
                FROM posts
                WHERE location IS NOT NULL 
                AND created_at >= NOW() - INTERVAL 7 DAY
                GROUP BY location
                HAVING COUNT(*) >= 2
                ORDER BY post_count DESC";

        return $this->executePrepared($sql);
    }
}

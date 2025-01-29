# Social Media Website

## Overview
This project is a feature-rich social media platform where users can interact, share posts, explore content, and communicate with each other. The platform includes separate functionalities for regular users and administrators, ensuring a seamless and secure experience for everyone.

---

## Features

### User Authentication
- **User Login:** Users can log in using their email and password.
- **Admin Login:** Administrators can log in using their credentials.
- **Session Management:** Ensures that only logged-in users or admins can access the platform. Redirects unauthorized access to the login page.

### User Features
1. **Feed:**
   - Users can view posts from their connections or people they follow.

2. **Explore:**
   - Users can discover trending content and posts from other users.

3. **Favorites:**
   - Users can save posts they like for future reference.

4. **Direct Messaging:**
   - Users can engage in one-on-one conversations with their friends.
   - Includes dynamic loading of conversations based on `friend_id` in the URL.

5. **User Profiles:**
   - Users can view and update their profiles, including uploading profile pictures.

6. **Posting:**
   - Users can create new posts with optional images.
   - Supports file validation for image uploads (e.g., size, type).

7. **Post Likes and Comments:**
   - Users can like/unlike posts.
   - Users can comment on posts to engage with others.

8. **Friends Management:**
   - Users can send and accept friend requests.
   - Users can view their list of friends.

### Admin Features
1. **User Management:**
   - Admins can block or unblock users.
   - Admins can view all registered users.

2. **Dashboard Stats:**
   - Admins can view platform analytics such as total users, posts, and other key metrics.

### Trending
- Displays trending posts or locations based on popularity metrics.

---

## Technical Details

### Backend
- **PHP:** The platform's logic and API functionality are powered by PHP.
- **MySQL:** Used as the database to store user information, posts, messages, and other platform data.

### Database Design
- **Users Table:** Stores user information (e.g., name, email, hashed password, country).
- **Admin Table:** Stores admin-specific credentials.
- **Posts Table:** Contains user-generated posts.
- **Likes Table:** Tracks likes on posts by users.
- **Comments Table:** Contains comments on posts.
- **Messages Table:** Handles direct messages between users.
- **Friends Table:** Manages friend relationships.

### Frontend
- **HTML/CSS:** Provides a clean and user-friendly interface.
- **JavaScript:** Handles dynamic interactions (e.g., active navigation).

---

## Code Highlights

### Controller Class
Handles the main logic of the platform, including user authentication, post management, message handling, and admin features.

#### Example: User Login Logic
```php
public function loginUser($email, $password) {
    $userModel = new User();
    $adminModel = new Admin();
    $user = $userModel->authenticateUser($email, $password);
    $admin = $adminModel->authenticateAdmin($email, $password);

    if ($user) {
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['status'] = $user['status'];
        header('location:index.php?page=feed');
        exit;
    } elseif ($admin) {
        session_start();
        $_SESSION['admin_id'] = $admin['admin_id'];
        header('location:index.php');
        exit;
    }
    return false;
}
```

#### Example: Feed Navigation
```php
$page = $_GET['page'] ?? 'feed';  // Default to 'feed' if no page is set

switch ($page) {
    case 'feed':
        $view->feed();
        break;
    case 'explore':
        $view->explore();
        break;
    case 'favorites':
        $view->favorites();
        break;
    case 'direct':
        $view->message();
        break;
    case 'profile':
        $view->profile();
        break;
    default:
        $view->feed();
        break;
}
```

---

## Installation
1. Clone the repository.
2. Set up a local server environment (e.g., XAMPP or WAMP).
3. Import the provided SQL file to set up the database.
4. Update database connection settings in the configuration file.
5. Start the local server and navigate to the project URL.

---

## Future Enhancements
- Add real-time notifications for likes, comments, and messages.
- Implement a search functionality for users and posts.
- Optimize the database queries for faster performance.

---

## Conclusion
This social media website combines essential user interaction features with a robust admin panel. It serves as a foundation for creating a scalable and engaging platform for online communities.


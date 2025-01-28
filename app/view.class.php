<?php
include_once 'controller.class.php';
include 'timeAgo.php';
class View extends Controller{
    public function header(){
        $id = $_SESSION['user_id'];
        $user = $this->getUserProfile($id);
        ?>
        <aside class="sidebar">
                <!-- Profile Section -->
                <a href="?page=profile"><div class="profile">
                  <img src="./public/images/uploads/profiles/<?=$user['profile_image']?>" alt="Profile">
                  <h3><?= ucfirst($user['fullname'])?></h3>
                </a>
                <!-- Navigation Menu -->
                <nav>
                <ul>
                    <a href="?page=feed" class="nav-link">
                        <li><i class="fas fa-home"></i> Feed</li>
                    </a>
                    <a href="?page=explore" class="nav-link">
                        <li><i class="fas fa-compass"></i> Explore</li>
                    </a>
                    <a href="?page=favorites" class="nav-link">
                        <li><i class="fas fa-heart"></i> My Favorites</li>
                    </a>
                    <a href="?page=direct" class="nav-link">
                        <li><i class="fas fa-paper-plane"></i> Direct</li>
                    </a>
                    </ul>

                </nav>
                <div class="sidebar-footer">
                  <div class="logo"></div> 
                  <div class="site-name">Placebook</div>
                  <p>©2025</p>
                </div>
              </aside>
<script>
  
  document.addEventListener("DOMContentLoaded", function() {
    // Get all navigation links
    const navLinks = document.querySelectorAll('.nav-link');

    // Get the current page from the URL
    const currentPage = new URLSearchParams(window.location.search).get('page');

    // Loop through each navigation link
    navLinks.forEach(link => {
      // Check if the link corresponds to the current page
      const pageTarget = link.getAttribute('href').split('=')[1];

      // Add 'active' class if this link matches the current page
      if (currentPage === pageTarget) {
        link.querySelector('li').classList.add('active');
      } else {
        link.querySelector('li').classList.remove('active');
      }
    });
  });
</script>

              <?php
    }
    public function feed()
{ ?>
    <main class="main-content">
        <?php 
        // Handle post creation
        
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
              if (isset($_POST['submit'])) {
                $userId = $_SESSION['user_id'];
                $content = $_POST['content'];
                $location = $_POST['location'];
                $imagePath = null;

                $this->createPost($userId, $content, $imagePath, $location);
            }

            // Handle comment submission
        if (isset($_POST['submitd'])) {
          $postId = $_POST['postId'] ?? null;
          $commenting = $_POST['commenting'] ?? null;
          $userId = $_SESSION['user_id'];

          if ($postId && $userId && !empty(trim($commenting))) {
              $this->addComment($postId, $userId, $commenting);
          }
      
         }
         if (isset($_POST['action'], $_POST['post_id']) && !empty($_SESSION['user_id'])) {
          $user_id = $_SESSION['user_id'];
          $post_id = $_POST['post_id'];
          $action = $_POST['action'];
  
          if ($action === 'save') {
              $this->savePost($user_id, $post_id); // Save post
          } elseif ($action === 'unsave') {
              $this->unsavePost($user_id, $post_id); // Unsave post
          }
      }
      if (isset($_POST['like_action'], $_POST['post_id']) && !empty($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $post_id = $_POST['post_id'];
        $like_action = $_POST['like_action'];

        if ($like_action === 'like') {
            $this->likePost($post_id, $user_id);
        } elseif ($like_action === 'unlike') {
            $this->unlikePost($post_id, $user_id);
        }
    }
        
        }

        
        ?>

        <!-- Share Box -->
        <form method="post" enctype="multipart/form-data" action="">
            <div class="share-box">
                <textarea name="content" class="share-textarea" placeholder="Based on your preferences, share your moments and experiences about places you've visited..." required></textarea>
                <div class="location-section">
                    <input type="text" name="location" id="location" class="location-input" placeholder="Enter a location..." />
                    <a href="#" id="google-map-link" class="location-btn" target="_blank">View on Google Maps</a>
                </div>
                <div class="upload-container">
                    <label for="image" class="file-label">Add Image</label>
                    <input type="file" name="image" id="image" accept="image/*">
                    <button type="submit" name="submit" class="upload-btn"><i class="fas fa-upload"> </i> Upload</button>
                </div>
            </div>
        </form>

        <!-- Feeds Section -->
        <div class="feeds">
            <?php 
            $posts = $this->listPosts();
            foreach ($posts as $post): ?>
                <div class="post">
                    <div class="post-header">
                        <img src="./public/images/uploads/profiles/<?php echo htmlspecialchars($post['profile_image']); ?>" alt="">
                        <div class="user-info">
                            <h4><?php echo htmlspecialchars($post['fullname']); ?></h4>
                        </div>
                        <span class="post-time"><?php echo timeAgo(strtotime($post['created_at'])); ?></span>
                    </div>
                    <?php if ($post['image_path'] != null): ?>
                        <img src="./public/images/uploads/posts/<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Content">
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <div class="post-footer">
                        <div class="actions">
                        <form method="POST" action="">
                                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                <?php if ($this->hasUserLiked( $post['post_id'], $_SESSION['user_id'])): ?>
                                    <input type="hidden" name="like_action" value="unlike">
                                    <button type="submit" style="background: none; border: none; font-size:larger; color: red;"><i class="fas fa-heart"></i></button>
                                <?php else: ?>
                                    <input type="hidden" name="like_action" value="like">
                                    <button type="submit" style="background: none; border:none; font-size: larger; color: #555;"><i class="fas fa-heart"></i></button>
                                <?php endif; ?>
                            </form>
                            <?php echo $post['total_likes']; ?>
                            <i class="fas fa-comment comment-toggle"></i> <?php echo $post['total_comments']; ?>
                            <a href="#" onclick="postLocation('<?php echo htmlspecialchars($post['location']); ?>')" target="_blank">
                                <i class="fas fa-map-marker-alt"></i> View on Map
                            </a>
                        </div>
                        <!-- Save/Unsave Button -->
            <?php if ($this->isPostSaved($_SESSION['user_id'], $post['post_id'])): ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="unsave">
                    <input type="hidden" name="post_id" value="<?= $post['post_id']; ?>">
                    <button type="submit" class="save-btn unsave">Unsave</button>
                </form>
            <?php else: ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="post_id" value="<?= $post['post_id']; ?>">
                    <button type="submit" class="save-btn save">Save</button>
                </form>
            <?php endif; ?>
                    </div>

                    <!-- Comment Box -->
                    <div class="comment-box" style="display: none;">
                        <div class="comments">
                            <?php $comments = $this->listComments($post['post_id']); ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment bubble">
                                    <span class="comment-user"><?php if ($comment['user_id'] === $_SESSION['user_id']) echo "You"; else echo htmlspecialchars($comment['user_name']); ?></span>
                                    <span class="comment-text"><?= htmlspecialchars($comment['comment']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="postId" value="<?= htmlspecialchars($post['post_id']); ?>" />
                            <input type="text" name="commenting" class="comment-input" placeholder="Add a comment..." required />
                            <button type="submit" name="submitd" class="comment-submit">Post</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
<?php }

    

    public function explore(){
      ?>
      <main class="main-content">
    <div class="content">
        <div id="explore-page">
            <h1 class="trend" >Trending Places in Placebook</h1> <!-- Updated headline -->

            <?php
            // Check if trending locations exist in the 
            $locations = $this->trending();
            if (!empty($locations)) {
                foreach ($locations as $location) {
                    ?>

                    <div class="tourism-place" data-name="<?= htmlspecialchars($location['location']); ?>">
                        <img src="default-image.jpg" alt="<?= htmlspecialchars($location['location']); ?>"> <!-- You can link to a location image if available -->
                        <div class="place-info">
                            <h2><?= htmlspecialchars($location['location']); ?></h2>
                            <p>Posts: <?= $location['post_count']; ?> recent posts.</p>
                            <a href="https://www.google.com/maps?q=<?= urlencode($location['location']); ?>" target="_blank" class="google-maps-link">View on Google Maps</a>
                        </div>
                    </div>
                    
                    <?php
                }?><h1 calss="hot">Hot Places You Should Visit</h1>
                <?php
            
                // If no trending locations, display predefined places
                
                $places = [
                    ["name" => "Eiffel Tower, France", "description" => "The iconic symbol of Paris, offering breathtaking views of the city.", "image" => "eiffel_tower.jpg", "link" => "https://www.google.com/maps?q=Eiffel+Tower,France"],
                    ["name" => "Great Wall of China, China", "description" => "A wonder of the world, this ancient wall stretches over 13,000 miles.", "image" => "great_wall_china.jpg", "link" => "https://www.google.com/maps?q=Great+Wall+of+China,China"],
                    ["name" => "Niagara Falls, Canada", "description" => "A majestic natural wonder located on the border of the USA and Canada.", "image" => "niagara_falls.jpg", "link" => "https://www.google.com/maps?q=Niagara+Falls,Canada"],
                    ["name" => "Taj Mahal, India", "description" => "A symbol of love, this white marble mausoleum is a UNESCO World Heritage Site.", "image" => "taj_mahal.jpg", "link" => "https://www.google.com/maps?q=Taj+Mahal,India"],
                    ["name" => "Machu Picchu, Peru", "description" => "An ancient Incan citadel set high in the Andes Mountains.", "image" => "machu_picchu.jpg", "link" => "https://www.google.com/maps?q=Machu+Picchu,Peru"]
                ];

                // Loop through the predefined places and display them
                foreach ($places as $place) {
                    ?>
                    <div class="tourism-place" data-name="<?= htmlspecialchars($place['name']); ?>">
                        <img src="path/to/images/<?= htmlspecialchars($place['image']); ?>" alt="<?= htmlspecialchars($place['name']); ?>">
                        <div class="place-info">
                            <h2><?= htmlspecialchars($place['name']); ?></h2>
                            <p><?= htmlspecialchars($place['description']); ?></p>
                            <a href="<?= htmlspecialchars($place['link']); ?>" target="_blank" class="google-maps-link">View on Google Maps</a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

        </div>
    </div>
</main>

        </div>
      </div>

      </main>

     <?php 
    }
    public function profile()
{
    $user = $this->getLoggedInUser(); // Fetch the logged-in user details
    ?>
    <main class="main-content">
    <div class="profile-page">
        <div class="profile-header">
          <img src="./public/images/post1.jpg" alt="Cover Photo" class="profile-cover">
          <div class="profile-information">
            <img src="./public/images/uploads/profiles/<?= htmlspecialchars($user['profile_image'] ?? './public/uploads/images/default-profile.jpg'); ?>" alt="Profile Picture" class="profile-pic">
            <h2><?= htmlspecialchars($user['fullname']); ?></h2>
            <p class="username">@<?= htmlspecialchars($user['fullname']); ?></p>
            <div class="profile-stats">
              <div>
                <h3>204</h3>
                <p>Following</p>
              </div>
              <div>
                <h3>1.2M</h3>
                <p>Followers</p>
              </div>
            </div>
            <p class="profile-bio"><?= htmlspecialchars($user['bio']); ?></p>
            <div class="profile-actions">
              <button class="btn follow-btn edit-profile-btn" onclick="openEditProfile()">Edit Profile</button>
              <button class="btn logout-btn" style="background-color: #f12f2f; color:white;" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
          </div>
        </>
       <?php 
       
       $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
      $posts = $this->getProfilePosts($userId); // Call the controller method to fetch posts
      ?>
        <div class="profile-gallery">
            <?php if (!empty($posts)) : ?>
                <?php foreach ($posts as $post) : ?>
                    <img src="./public/images/uploads/posts/<?= htmlspecialchars($post['image_path']); ?>" alt="Post Image">
                <?php endforeach; ?>
            <?php else : ?>
                <p>No posts to display.</p>
            <?php endif; ?>
        </div>
      </div>
      <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $userId = $_SESSION['user_id'];
    $fullname = $_POST['fullname'];
    $bio = $_POST['bio'];

    // Pass the current image from the user array to handle no-upload cases
    $profileImage = $user['profile_image'];

    // Call the controller method to handle the update
    $this->updateUserProfile($userId, $fullname, $bio, $profileImage);
}
?>
<div id="edit-profile-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <form method="POST" enctype="multipart/form-data" action="">
            <h3>Edit Profile</h3>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']); ?>" required placeholder="Full Name">
            <br>
            <input type="text" name="bio" value="<?= htmlspecialchars($user['bio']); ?>" required placeholder="BIO">
            <br>
            <label for="profile-image">Change Profile Image</label>
            <input type="file" name="profile_image" id="profile-image" accept="image/*">
            <button type="submit" name="update_profile">Save Changes</button>
        </form>
        <button onclick="closeEditProfile()" class="close-modal">Cancel</button>
    </div>
</div>

    </main>

    <script>
        function openEditProfile() {
            document.getElementById('edit-profile-modal').style.display = 'block';
        }
        function closeEditProfile() {
            document.getElementById('edit-profile-modal').style.display = 'none';
        }
    </script>
    <?php
}


    public function message()
    {
      $chatController = new Controller();

      // Get the current user ID (logged-in user)
      $userId = $_SESSION['user_id'];
      
      // Fetch all users except the logged-in user
      $users = $chatController->fetchAllUsers($userId);
      
      // Handle message sending
      $currentFriendId = $_GET['friend_id'] ?? null;
      if ($currentFriendId) {
          $messages = $chatController->fetchMessages($userId, $currentFriendId);
      } else {
          $messages = [];
      }
      $currentFriendId = isset($_GET['friend_id']) ? $_GET['friend_id'] : null;
      // Handle message submission
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_content'])) {
          $content = trim($_POST['message_content']);
          if (!empty($content) && $currentFriendId) {
              $chatController->sendMessage($userId, $currentFriendId, $content);
              header("Location: ?page=direct&friend_id=$currentFriendId"); // Refresh the chat
              exit;
          }
      }
      ?>
      
      <main class="main-content">
          <div class="messenger-container">
              <!-- Main chat section -->
              <div class="chat-section">
                  <div class="messages-header">
                      <div class="profile-info">
                          <?php if ($currentFriendId): ?>
                              <?php 
                                  // Fetch the friend's information
                                  $friend = (new User())->getUserById($currentFriendId);
                              ?>
                              <img src="./public/images/uploads/profiles/<?= htmlspecialchars($friend['profile_image'] ?? 'default.jpg'); ?>" alt="Profile Picture" />
                              <span><?= htmlspecialchars($friend['fullname']); ?></span>
                          <?php else: ?>
                              <span>Select a user to start chatting</span>
                          <?php endif; ?>
                      </div>
                  </div>
      
                  <!-- Chat messages -->
                  <div class="messages">
                      <?php if ($currentFriendId): ?>
                          <?php foreach ($messages as $message): ?>
                              <div class="message <?= $message['sender_id'] == $userId ? 'sent' : 'received'; ?>">
                                  <?= htmlspecialchars($message['content']); ?>
                              </div>
                          <?php endforeach; ?>
                      <?php else: ?>
                          <div class="no-messages">No conversation selected.</div>
                      <?php endif; ?>
                  </div>
      
                  <!-- Message input area -->
                  <?php if ($currentFriendId): ?>
                      <div class="message-input">
                          <form method="POST" action="?friend_id=<?= $currentFriendId; ?>">
                              <input type="text" name="message_content" placeholder="Type a message..." required />
                              <button type="submit"><i class="fas fa-paper-plane"></i></button>
                          </form>
                      </div>
                  <?php endif; ?>
              </div>
      
              <!-- Navigation bar (List of all users) -->
              <div class="nav-bar">
                  <div class="new-conversation">All Users</div>
                  <ul>
                      <?php foreach ($users as $user): ?>
                          <li class="contact <?= $user['user_id'] == $currentFriendId ? 'active' : ''; ?>">
                              <a href="?friend_id=<?= $user['user_id']; ?>">
                              <img src="./public/images/uploads/profiles/<?= htmlspecialchars($user['profile_image'] ?? 'default.jpg'); ?>" alt="User">

                                  <span><?= htmlspecialchars($user['fullname']); ?></span>
                              </a>
                          </li>
                      <?php endforeach; ?>
                  </ul>
              </div>
          </div>
      </main>


<?php
    }
  public function favorites()
  {
    if (isset($_POST['action'], $_POST['post_id']) && !empty($_SESSION['user_id'])) {
      $user_id = $_SESSION['user_id'];
      $post_id = $_POST['post_id'];
      $action = $_POST['action'];

      if ($action === 'unsave') {
          $this->unsavePost($user_id, $post_id); // Unsave post
      }
  }
    
      $user_id = $_SESSION['user_id'];
      $savedPosts = $this->getSavedPosts($user_id);
      ?>
    <main class="main-content">
    <div class="feeds">
        <h2>Saved Posts</h2>
        <?php if (!empty($savedPosts)): ?>
            <?php foreach ($savedPosts as $post): ?>
                <div class="post">
                    <div class="post-header">
                        <img src="./public/images/uploads/profiles/<?= htmlspecialchars($post['post_owner_image']); ?>" alt="">
                        <div class="user-info">
                            <h4><?= htmlspecialchars($post['post_owner_name']); ?></h4>
                        </div>
                        <span class="post-time"><?= timeAgo(strtotime($post['post_created_at'])); ?></span>
                    </div>
                    <?php if ($post['image_path'] != null): ?>
                        <img src="./public/images/uploads/posts/<?= htmlspecialchars($post['image_path']); ?>" alt="Post Image">
                    <?php endif; ?>
                    <p><?= htmlspecialchars($post['content']); ?></p>
                    <div class="post-footer">
                        <div class="actions">
                            <i class="fas fa-heart"></i> <?= $post['total_likes']; ?>
                            <i class="fas fa-comment"></i> <?= $post['total_comments']; ?>
                            <a href="#" onclick="postLocation('<?php echo htmlspecialchars($post['location']); ?>')" target="_blank">
                                <i class="fas fa-map-marker-alt"></i> View on Map
                            </a>
                        </div>
                      <form method="POST" action="">
                    <input type="hidden" name="action" value="unsave">
                    <input type="hidden" name="post_id" value="<?= $post['post_id']; ?>">
                    <button type="submit" class="save-btn unsave">Unsave</button>
                </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No saved posts found.</p>
        <?php endif; ?>
    </div>
</main>

  <?php
  }

  public function admin()
  {
    $stats = $this->getDashboardStats();
    $users = $this->getAllUsers();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
        $userId = $_POST['user_id'];
        $action = $_POST['action'];

        if ($action === 'block') {
            $this->blockUser($userId);
        } elseif ($action === 'unblock') {
            $this->unblockUser($userId);
        }

  }
  ?>
     <div class="container">
            <header>
                <div class="dashboard-header">
                    <h1>Welcome, Boss!</h1>
                    <div class="user-info">
                        <img src="./public/images/placebook_c1.svg" alt="User Avatar">
                    </div>
                    <div class="buttons">
                        <button style="background-color: #f12f2f; color:white;" onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </div>
                </div>
            </header>

            <!-- Cards Overview -->
            <div class="dashboard-cards">
                <?php foreach ($stats as $stat): ?>
                    <div class="card">
                    <h2><i class="<?= htmlspecialchars($stat['icon']); ?>"></i> <?= htmlspecialchars($stat['title']); ?></h2>
                    <p><?= htmlspecialchars($stat['count']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Users List -->
            <div class="users-list">
                <h3>Users List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['user_id']); ?></td>
                                <td><?= htmlspecialchars($user['fullname']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td><?= htmlspecialchars($user['status']); ?></td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="block">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']); ?>">
                                            <button type="submit" class="block-btn">Block</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="unblock">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']); ?>">
                                            <button type="submit" class="unblock-btn">Unblock</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


<?php
}
}
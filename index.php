<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include_once './includes/autoloader.inc.php';
$view = new View();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./public/css/all.min.css">
    <?php
    if(isset($_SESSION['user_id']))
    {?>
    <link rel="stylesheet" href="./public/css/message.css">
    <link rel="stylesheet" href="./public/css/p.css">
    <link rel="stylesheet" href="./public/css/style.css">
    <link rel="stylesheet" href="./public/css/explore.css">
    <?php
    }
    elseif(isset($_SESSION['admin_id']))
    {?>
        <link rel="stylesheet" href="./public/css/admin.css">
    <?php
    }
    ?>
  

</head>
</html>
<body>
<div class="container">

<?php
if(isset($_SESSION['user_id'])){
    if($_SESSION['status']== 'active'){

$view->header();
$page = $_GET['page'] ?? 'direct';  // Default to 'feed' if no page is set
$friendId = $_GET['friend_id'] ?? null; // Get the friend_id if it's set
if($page == 'feed')
{
    $view->feed();
}
elseif($page == 'explore')
{
    $view->explore();
}
elseif($page == 'favorites')
{
    $view->favorites();
}
elseif($page == 'direct') 
{
    $view->message();
}elseif($friendId) 
{
    $view->message();
}
elseif($page == 'profile') 
{
    $view->profile();
}
}


else{
    echo '<h1> Sorry You are blocked by Admin</h1><br>';
    echo '<a href=logout.php>Logout</a>';
}
}


?>

    <script src="./public/js/script.js"></script>
</div>
<?php
if(isset($_SESSION['admin_id'])){
$view->admin();
}
?>
</body>
</html>

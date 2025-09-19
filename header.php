<?php 

    require_once "./handlers/config.php";

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }

?>

<header>
    <div class="search-container">
        <input type="text" placeholder="Search product, supplier, order">
        <span class="material-symbols-outlined">search</span>
    </div>
    <div class="profile">
        <span class="material-symbols-outlined">notifications</span>
        <div class="profile-photo">
            <p id="userName"><?php echo htmlspecialchars($_SESSION["first_name"]) . " " . htmlspecialchars($_SESSION["last_name"]); ?> </p>
        </div>
    </div>
</header>

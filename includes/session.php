<?php
    session_start(); //start a session
    if (isset($_SESSION['alert']) && !isset($_GET['alert'])){
        unset($_SESSION['alert']);
    }
    //Check to see if the user has logged in, if they haven't and are in a protected page, warn then and redirect them to the home page
    if (!(isset($_SESSION['username']) && isset($_SESSION['access_type'])) && (basename($_SERVER['PHP_SELF']) == 'set_club.php' || basename($_SERVER['PHP_SELF']) == 'dashboard.php' || basename($_SERVER['PHP_SELF']) == 'set_student.php' || basename($_SERVER['PHP_SELF']) == 'edit_leader.php' || basename($_SERVER['PHP_SELF']) == 'register.php' || basename($_SERVER['PHP_SELF'] == 'account.php'))){
        $_SESSION['alert'] = "You do not have permission to access this page. Please log in.";
        header("Location:index.php?alert=warning");
        exit;
    }
?>
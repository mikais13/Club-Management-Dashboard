<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title of the page shown in the tab -->
    <title>Log In</title>
    <!-- Meta tags for the page -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap links-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php 
        session_start(); // Get the session variables
        if (isset($_SESSION['username']) && isset($_SESSION['access_type'])) { // if already logged in, redirect to dashboard
            header('Location:dashboard.php');
            exit;
        } else { // if not logged in show the menu
            echo '<div class="container mt-4">
                    <div class="row position-absolute top-50 start-50 translate-middle">
                        <div class="col-12"> 
                                <h1>Log In</h1>
                        </div>
                        <div class="col-12">
                            <form class="" action="load.php" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control form-control-lg shadow-sm" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control form-control-lg shadow-sm" id="password" name="password" required>
                                </div>';
                            echo '<input type="submit" class="btn btn-primary shadow" name="log-in"></input>
                                <a href="index.php" class="btn btn-secondary shadow">Return</a>';
                                if (isset($_GET['club'])){ // if meant to log in to a club, add the club id to the form, so the user will be redirected to the club page
                                    echo '<input type="hidden" name="club" value="'.$_GET['club'].'"></input>';
                                }
                            echo '</form>
                        </div>
                    </div>
                </div>';
        }
        include 'includes/alert.php'; // include the alert box for if the user has entered incorrect details etc.
    ?>
</body>
</html>
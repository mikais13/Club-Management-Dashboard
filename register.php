<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title for the page shown in the tab -->
    <title>Register Account</title>
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
    <div class="container mt-5"> <!-- Container for the main section -->
        <div class="row position-absolute top-50 start-50 translate-middle"> <!-- Center the form -->
            <div class="col-12"> 
                <h2>Register New Account</h2> <!-- Title -->
                <form action="load.php" method="POST"> <!-- Form to register a new account -->
                    <div class="mb-3">
                        <!-- Input for the username -->
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <!-- Input for the access type -->
                        <label for="access-type" class="form-label">Access Type</label>
                        <select class="form-select" id="access_type" name="access_type" required>
                            <?php
                                include 'includes/session.php';
                                if ($_SESSION['access_type'] == 0){ // if the user is an admin, show the option to create a new admin account
                                    echo '<option value="0">Admin</option>';
                                }
                                if ($_SESSION['access_type'] <= 1){ // if the user is an admin or teacher, show the option to create a new teacher account
                                    echo '<option value="1">Teacher</option>';
                                }
                                echo '<option value="2">Student</option>'; // always show the option to create a new student account
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <!-- Input for the password -->
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <input type="submit" class="btn btn-primary" name="register" value="Register"></input> <!-- Submit button -->
                    <a href="dashboard.php" class="btn btn-secondary">Return</a> <!-- Return button -->
                </form>
            </div>
        </div>
    </div>
    <?php include 'includes/alert.php'; ?> <!-- Include the alert box for if the user has entered details that match a current user etc. -->
</body>
</html>
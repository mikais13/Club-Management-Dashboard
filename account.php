<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title for the page -->
    <title>Edit Account</title>
    <!-- Meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Custom CSS and JS -->
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="container-md mt-4"> <!-- Container for the page -->
        <?php
            // Include the database configuration and session information
            include 'includes/session.php';
            include 'includes/db_config.php';
            // Set the page name and whether or not to show the search bar
            $page_name = "Edit Account";
            $search_bar = false;
            // Get the id of the user being edited
            if (isset($_GET['user'])){ // If the id is set in the URL
                $return = 'dashboard.php';
                $id = $_GET['user'];
            } else { // If the id is not set in the URL, get the id of the logged in user
                $return = 'index.php';
                if (isset($_SESSION['username'])){ // If the user is logged in
                    // Get the id of the logged in user
                    $sql = "SELECT * FROM users WHERE username = '".$_SESSION['username']."'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1){ // If the user exists
                        $row = $result->fetch_assoc();
                        $id = $row['user_id'];
                    } else { // If the user does not exist
                        $_SESSION['alert'] = "There was an error accessing your account. Please try again.";
                        header("Location: index.php?alert=danger");
                        exit;
                    }
                } else { // If the user is not logged in
                    $_SESSION['alert'] = "You must be logged in to edit your account.";
                    header("Location: index.php?alert=danger");
                    exit;
                }
            }
            // Show the navbar
            include 'includes/navbar.php';
            // Get the user's information
            $sql = "SELECT * FROM users WHERE user_id = ".$id;
            $result = $conn->query($sql);
            if ($result->num_rows == 1){ // If the user exists
                $row = $result->fetch_assoc();
                $username = $row['username'];
                $db_password = $row['password'];
                $access_type = $row['access_type'];
            } else { // If the user does not exist
                $_SESSION['alert'] = "There was an error accessing the account. Please try again.";
                header("Location: index.php?alert=danger");
                exit;
            }
        ?>
        <div class="row">
            <div class="col-md-6">
                <form action="load.php" method="POST"> <!-- Form to edit the user's information -->
                    <div class="row my-3">
                        <div class="col">
                            <!-- Username section -->
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control shadow-sm" id="username" name="username" value="<?php echo $username; ?>" required>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col">
                            <!-- Access type section -->
                            <label for="access_type" class="form-label">Access Type:</label>
                            <select class="form-select mb-3 shadow-sm" id="access_type" name="access_type">
                                <?php
                                    $sql = "SELECT * FROM users WHERE access_type = 1"; // Get all users to check if the user is the last admin
                                    $result = $conn->query($sql);
                                    if ($_SESSION['access_type'] == 0){ // If the logged in user is an admin allow them to set users to be admins
                                        echo '<option value="0"'.($access_type==0?"selected":"").'>Admin</option>';
                                    }
                                    if (($_SESSION['access_type'] == 0 && $result->num_rows > 1) || $_SESSION['access_type'] == 1){ // If the logged in user is an admin or the user is a staff member allow them to set users to be staff members
                                        echo '<option value="1"'.($access_type==1?"selected":"").'>Staff</option>';
                                    }
                                    if (($_SESSION['access_type'] == 0 && $result->num_rows > 1) || $_SESSION['access_type'] == 1 || $_SESSION['access_type'] == 2){ // If the logged in user is an admin, the user is a staff member or the user is a student allow them to set users to be students
                                        echo '<option value="2"'.($access_type==2?"selected":"").'>Student</option>';
                                    }
                                ?>
                            </select>
                            <?php echo ($result->num_rows==1&&$_SESSION['access_type']==0&&$access_type==0)?"<small class='text-muted'>*You cannot change the access type of the last admin</small>":""; ?> <!-- Show a message if the user is the last admin -->
                        </div>
                    </div>
                    <?php
                        if ($_SESSION['username'] == $username){ // If the user is editing their own account, allow them to change their password by adding the password section, this includes a switch for them to decide and a current and new password field to ensure they are the correct user
                            echo '<div class="row my-3">
                                <div class="col">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input shadow-sm" type="checkbox" role="switch" id="change_password" name="change_password">
                                        <label class="form-check-label" for="change_password">Change Password</label>
                                    </div>
                                </div>
                            </div>
                            <div id="password">
                                <div class="row my-3">
                                    <div class="col">
                                        <label for="password" class="form-label">Current Password:</label>
                                        <input type="password" class="form-control shadow-sm" name="current_password" placeholder="Enter current password">
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col">
                                        <label for="password" class="form-label">New Password:</label>
                                        <input type="password" class="form-control shadow-sm" name="new_password" placeholder="Enter new password">
                                    </div>
                                </div>
                            </div>';
                        }
                    ?>
                    <div class="row my-3">
                        <div class="col">
                            <input type="hidden" name="user_id" value="<?php echo $id; ?>"> <!-- Hidden field to pass the user's id to the load.php file -->
                            <input type="submit" class="btn btn-primary shadow" name="edit-user" value="Save Changes"></input> <!-- Button to submit the form -->
                            <a href="<?php echo $return; ?>" class="btn btn-secondary shadow">Return</a> <!-- Button to return to the previous page -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'includes/footer.php';?> <!-- Include the footer -->
    </div>
    <?php include 'includes/alert.php';?> <!-- Include the alert box if required -->
</body>
</html>
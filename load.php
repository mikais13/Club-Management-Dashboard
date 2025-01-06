<!DOCTYPE html>
<html lang="en">
<head>
    <!-- If this page is actually displayed the there must have been an error -->
    <title>Error</title>
    <!-- Meta tags for the page -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php
        session_start(); // start a session
        include 'includes/db_config.php'; // include the database configuration file
        $dsn = 'mysql:dbname=' . $dbname . ';host=' . $db_servername; // set the data source name
        try { // try to connect to the database
            $pdo = new PDO($dsn, $db_username, $db_password );
        } catch (\PDOException $e) { // if there is an error
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        if (isset($_POST['log-in'])){ // if the user is logging in
            $password = $_POST['password']; // get the password
            // get the user's details from the database
            $sql = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "'";
            $result = $conn->query($sql);
            if ($result->num_rows == 1){ // if there is one user with that username
                $row = $result->fetch_assoc(); // get the user's details
                $password_stored = $row['password']; // get the stored password
                if (password_verify($password, $password_stored)){ // if the inputted password matches the stored password set the session variables
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['access_type'] = $row['access_type'];
                } else { // if the password is incorrect
                    session_destroy(); // remove all session variables
                    session_start(); // restart the session
                    $_SESSION['alert'] = "Incorrect password"; // set the alert message
                    header('Location:login.php?alert=danger'); // redirect to the login page
                    exit;
                }
            } else if ($result->num_rows == 0) { // if there is no users with that username then the username is incorrect
                session_destroy(); // remove all session variables
                session_start(); // restart the session
                $_SESSION['alert'] = "Incorrect username"; // set the alert message
                header('Location:login.php?alert=danger'); // redirect to the login page
                exit;
            } else {
                session_destroy(); // remove all session variables
                session_start(); // restart the session
                $_SESSION['alert'] = "An error occurred"; // set the alert message
                header('Location:login.php?alert=danger'); // redirect to the login page
                exit;
            }
            $_SESSION['alert'] = "Logged in successfully"; // set the alert message if the user has logged in successfully
            if (isset($_POST['club'])){ // if the user is logging in from the club page
                header('Location:club.php?club='.$_POST['club'].'&alert=success');
            } else { // if the user is logging in from the dashboard
                header('Location:dashboard.php?alert=success');
            }
            exit;
        } else if (isset($_POST['register'])) { // if the user is registering
            $username = trim($_POST['username']); // get the username and remove whitespace from either end
            $access_type = $_POST['access_type']; // get the access type
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // hash the password
            // check to see if the username already exists
            $sql = "SELECT * FROM users WHERE username = '" . $username . "'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0){ // if the username is present then the username already exists
                $_SESSION['alert'] = "Username already exists"; // set the alert message
                header('Location:register.php?alert=danger'); // redirect to the register page
                exit;
            } else { // if the username is not present then create the account
                // set the sql query
                $sql = "INSERT INTO `users` (`username`, `password`, `access_type`) VALUES ( :username, :password, :access_type)";
                $stmt = $pdo->prepare($sql); // prepare the query
                // bind the parameters
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':access_type', $access_type);
                $result = $stmt->execute(); // execute the query
                $_SESSION['alert'] = "Account created"; // set the alert message
                header('Location:dashboard.php?alert=success'); // redirect to the dashboard
            }
        } else if (isset($_POST['log-out'])){ // if the user is logging out
            session_destroy(); // remove all session variables
            session_start(); // restart the session
            $_SESSION['alert'] = "Logged out successfully"; // set the alert message
            header('Location:index.php?alert=success'); // redirect to the index page
            exit;
        } else if (isset($_POST['edit-user'])){ // if the user is editing a user
            try{
                // get all of the admins
                $sql = "SELECT * FROM users WHERE access_type = 0";
                $result = $conn->query($sql);
                // get the user's details
                $id = $_POST['user_id'];
                $username = $_POST['username'];
                $access_type = $_POST['access_type'];
                if ($result->num_rows == 1){ // if there is only one admin
                    $row = $result->fetch_assoc();
                    if ($row['user_id'] == $id){ // if the user is the only admin
                        $_SESSION['alert'] = "Cannot change access type of only admin user"; // warn the user
                        $warning = true;
                        $access_type = 0; // set the user's access type to admin
                    }
                }
                if (isset($_POST['change_password'])){ // if the user is changing their password
                    $old_password = $_POST['current_password']; // get the inputted old password
                    // get the user's details
                    $sql = "SELECT * FROM users WHERE user_id = '" . $id . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1){ // if there is only one user with that id
                        $row = $result->fetch_assoc();
                        $password_stored = $row['password']; // get the old password
                        if (password_verify($old_password, $password_stored)){ // if the old password matches the inputted old password allow them to change the password
                            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT); // hash the new password
                            // update the user's details
                            $sql = "UPDATE users SET username = :username, password = :password, access_type = :access_type WHERE user_id = :id";
                            $stmt = $pdo->prepare($sql); // prepare the query
                            // bind the parameters
                            $stmt->bindParam(':username', $username);
                            $stmt->bindParam(':password', $new_password);
                            $stmt->bindParam(':access_type', $access_type);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute(); // execute the query
                        }
                    }
                } else { // if not changing the password
                    $sql = "UPDATE users SET username = :username, access_type = :access_type WHERE user_id = :id";
                    $stmt = $pdo->prepare($sql); // prepare the query
                    // bind the parameters
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':access_type', $access_type);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute(); // execute the query
                }
                // if user is editing own account change session values
                $sql = "SELECT * FROM users WHERE user_id = '" . $id . "'";
                $result = $conn->query($sql);
                if ($result->num_rows == 1){
                    $row = $result->fetch_assoc();
                    if ($row['username'] == $_SESSION['username']){ // if the user is editing their own account
                        // match the session variables
                        $_SESSION['username'] = $username;
                        $_SESSION['access_type'] = $access_type;
                    }
                }
                if ($warning){ // if the user is the only admin
                    $_SESSION['alert'] .= "<br>Account updated";
                    header('Location:dashboard.php?alert=warning'); // redirect to the dashboard
                } else {
                    $_SESSION['alert'] = "Account updated";
                    header('Location:dashboard.php?alert=success'); // redirect to the dashboard
                }
            } catch (PDOException $e){ // if there is an error
                $_SESSION['alert'] = "Error updating account. Please try again."; // set the alert message
                header('Location:dashboard.php?alert=danger'); // redirect to the dashboard
            }
            exit;
        } else if (isset($_POST['delete-user'])){ // if the user is deleting a user
            try{
                if (isset($_POST['user_id'])){ // if the user is deleting a user
                    $id = $_POST['user_id'];
                    // get the user's details
                    $sql = "SELECT * FROM users WHERE user_id = '" . $id . "'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1){ // if there is only one user with that id
                        $row = $result->fetch_assoc();
                        if ($row['access_type'] == 0){ // if the user is an admin
                            $sql = "SELECT * FROM users WHERE access_type = 0";
                            $result_admins = $conn->query($sql);
                            if ($result_admins->num_rows == 1){ // if there is only one admin
                                $_SESSION['alert'] = "Cannot delete only admin user"; // warn the user
                                header('Location:dashboard.php?alert=warning'); // redirect to the dashboard
                                exit;
                            }
                        }
                    }
                    // delete the user
                    $sql = "DELETE FROM users WHERE user_id = :id";
                    $stmt = $pdo->prepare($sql); // prepare the query
                    $stmt->bindParam(':id', $id); // bind the parameters
                    $stmt->execute(); // execute the query
                    if ($row['username'] == $_SESSION['username']){ // if the user is deleting their own account
                        session_destroy(); // destroy the session
                        session_start(); // start a new session
                        $_SESSION['alert'] = "Account deleted"; // set the alert message
                        header('Location:index.php?alert=success'); // redirect to the login page
                        exit;
                    }
                    $_SESSION['alert'] = "Account deleted"; // set the alert message
                    header('Location:dashboard.php?alert=success'); // redirect to the dashboard
                } else {
                    $_SESSION['alert'] = "Error deleting account, account could not be found. Please try again."; // set the alert message
                    header('Location:dashboard.php?alert=danger'); // redirect to the dashboard
                }
            } catch (PDOException $e){ // if there is an error
                $_SESSION['alert'] = "Error deleting account. Please try again."; // set the alert message to warn the user
                header('Location:dashboard.php?alert=danger'); // redirect to the dashboard
            }
            exit;
        } else if (isset($_POST['edit'])){ // if the user is editing a club
            $id = $_POST['id']; // get the club id
            header('Location:set_club.php?id=' . $id); // redirect to the set club page
            exit;
        } else if (isset($_POST['delete'])){ // if the user is deleting a club
            try{
                if (isset($_POST['id'])){ // if the user is deleting a club
                    $id = $_POST['id']; // get the club id
                    $sql = "DELETE FROM students WHERE club_id = :id"; // delete all student entries that are in the club
                    $stmt = $pdo->prepare($sql); // prepare the query
                    $stmt->bindParam(':id', $id); // bind the id
                    $stmt->execute(); // execute the query
                    // delete club
                    $sql = "DELETE FROM clubs WHERE club_id = :id"; // set the SQL to remove the club
                    $stmt = $pdo->prepare($sql); // prepare the query
                    $stmt->bindParam(':id', $id); // bind the id
                    $stmt->execute(); // execute the query
                    $_SESSION['alert'] = "Club deleted."; // set the alert message
                    header('Location:dashboard.php?alert=success'); // redirect to the dashboard
                } else { // if the club id is not set
                    $_SESSION['alert'] = "Error deleting club, club could not be found. Please try again."; // set the alert message
                    header('Location:dashboard.php?alert=danger'); // redirect to the dashboard
                }
            } catch (PDOException $e){ // if there is an error
                $_SESSION['alert'] = "Error deleting club. Please try again."; // set the alert message 
                header('Location:dashboard.php?alert=danger'); // redirect to the dashboard
            }
            exit;
        } else if (isset($_POST['add-club']) || isset($_POST['edit-club'])){ // if the user is adding or editing a club
            $name=""; // set the name to blank as default
            if (isset($_POST['club-name'])){ // if the club name is set, make name match
                $name = $_POST['club-name'];
            }
            // get the categories and format them correctly
            $categories="[";
            if (isset($_POST['club-category'])){
                for ($i = 0; $i < sizeof($_POST['club-category']); $i++){
                    if (isset($_POST['club-category'][$i])){
                        if (trim($_POST['club-category'][$i]) != ""){
                            $categories .= "'" . $_POST['club-category'][$i] . "',";
                        }
                    }
                }
                $categories = rtrim($categories, ",");
            }
            $categories .= "]";
            // get the club description, if it's not set, set it to blank
            $description="";
            if (isset($_POST['club-description'])){
                $description = $_POST['club-description'];
            }
            // get the club leader, if it's not set, set it to blank
            $leader_id="";
            if (isset($_POST['club-leader'])){
                $leader_id = $_POST['club-leader'];
                if ($leader_id == "0"){ // if the leader is new, add them
                    // get the leader information
                    $leader_first_name = isset($_POST['leader-first-name'])?$_POST['leader-first-name']:"";
                    $leader_last_name = isset($_POST['leader-last-name'])?$_POST['leader-last-name']:"";
                    $leader_email = isset($_POST['leader-contact'])?$_POST['leader-contact']:"";
                    $leader_type = isset($_POST['leader-type'])?$_POST['leader-type']:"";
                    // insert into the database
                    $query = "INSERT INTO `leaders` (`first_name`, `last_name`, `email`,`leader_type`) VALUES (:first_name, :last_name, :email, :leader_type)";
                    $stmt = $pdo->prepare($query); // prepare the query
                    // bind the parameters
                    $stmt->bindParam(':first_name', $leader_first_name);
                    $stmt->bindParam(':last_name', $leader_last_name);
                    $stmt->bindParam(':email', $leader_email);
                    $stmt->bindParam(':leader_type', $leader_type);
                    $stmt->execute(); // execute the query
                    $leader_id = $pdo->lastInsertId(); // get the id of the new leader
                }
            }
            // get the meetings and format them correctly
            $meetings = "[";
            if (sizeof($_POST['meeting-time']) == 0 || sizeof($_POST['meeting-day']) == 0 || sizeof($_POST['meeting-place']) == 0){ // if one of the required fields are empty, don't include it
                $meetings .= "''";
            } else { // if the meeting is valid, include it
                for ($i = 0; $i < sizeof($_POST['meeting-time']); $i++){ // for every meeting
                    if (isset($_POST['meeting-time'][$i]) && isset($_POST['meeting-day'][$i]) && isset($_POST['meeting-place'][$i])){ // if all parts of the meeting are set
                        if(isset($_POST['meeting-details'])){ // if the meeting includes details, include it in the format
                            $meetings .= '{"date": "' . $_POST['meeting-day'][$i] . '", "time": "' . $_POST['meeting-time'][$i] . '", "location": "' . $_POST['meeting-place'][$i] . '", "details": "' . $_POST['meeting-details'][$i] . '"},';
                        } else {
                            $meetings .= '{"date": "' . $_POST['meeting-day'][$i] . '", "time": "' . $_POST['meeting-time'][$i] . '", "location": "' . $_POST['meeting-place'][$i] . '", "details": ""},';
                        }
                    }
                }
                $meetings = rtrim($meetings, ","); // remove the last comma to keep the format
            }
            $meetings .= "]";
            try{
                if (isset($_POST['add-club'])){ // add the club if the user is adding a club
                    $query = "INSERT INTO `clubs` (`name`, `categories`, `description`, `leader_id`, `meetings`) VALUES (:name, :categories, :description, :leader_id, :meetings)";
                    $stmt = $pdo->prepare($query);
                } else if (isset($_POST['edit-club'])){ // edit the club is the user is editing a club
                    $query = "UPDATE clubs SET name = :name, description = :description, categories  = :categories, meetings = :meetings, leader_id = :leader_id where club_id = :id";
                    $id = $_POST['club-id'];
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':id', $id);
                }
                //prepare query for execution and bind the parameters
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':categories', $categories);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':leader_id', $leader_id);
                $stmt->bindParam(':meetings', $meetings);
                // Execute the query
                $stmt->execute();
                $_SESSION['alert'] = "Club " . (isset($_POST['add-club'])?"added":"edited") . " successfully"; // set the alert
                header('Location:dashboard.php?alert=success'); // redirect to dashboard
                exit;
            } catch(PDOException $exception){ //to handle error
                $_SESSION['alert'] = "Club could not be " . (isset($_POST['add-club'])?"added":"edited") . ". Please try again."; // set the alert
                header('Location:dashboard.php?alert=danger'); // redirect to dashboard
                exit;
            }
        } else if (isset($_POST['add-student'])){ // if the user is adding a student
            try{
                $club_id = isset($_POST['club'])?$_POST['club']:array(); // get the club id, if it isn't set, make an empty array so this can be caught
                $student_id = isset($_POST['student'])?$_POST['student']:0; // get the student id, if it isn't set, make it 0 so this can be caught
                if (is_array($club_id)){ // if there are several clubs
                    if (count($club_id) == 0){ // if there are no clubs, set the alert and redirect
                        $_SESSION['alert'] = "Error adding student. Please try again."; // set the alert
                        header('Location:dashboard.php?alert=danger'); // redirect to dashboard
                        exit;
                    }
                    for ($i = 0; $i < count($club_id); $i++){ // for every club the student is added to
                        if ($student_id == 0){ // if the student does not already exist
                            // set their info to be the inputted values
                            $first_name = isset($_POST['first_name'])?$_POST['first_name']:"";
                            $last_name = isset($_POST['last_name'])?$_POST['last_name']:"";
                            $username = isset($_POST['username'])?$_POST['username']:"";
                        } else { // if the student exists
                            // get the student's info from a previous entry
                            $query = "SELECT * FROM students WHERE student_id = ".$student_id." LIMIT 1";
                            $result = $conn->query($query);
                            $row = $result->fetch_assoc();
                            $first_name = $row['first_name'];
                            $last_name = $row['last_name'];
                            $username = $row['username'];
                        }
                        $club = $club_id[$i]; // get the club id
                        // insert the student into the database
                        $query = "INSERT INTO `students` (`first_name`, `last_name`, `username`, `club_id`) VALUES (:first_name, :last_name, :username, :club_id)";
                        $stmt = $pdo->prepare($query); // prepare the query
                        // bind the parameters
                        $stmt->bindParam(':first_name', $first_name);
                        $stmt->bindParam(':last_name', $last_name);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':club_id', $club);
                        $stmt->execute(); // execute the query
                    }
                } else { // if there is only one club for this student
                    if ($student_id == 0){ // if the student does not already exist
                        $first_name = isset($_POST['first_name'])?$_POST['first_name']:"";
                        $last_name = isset($_POST['last_name'])?$_POST['last_name']:"";
                        $username = isset($_POST['username'])?$_POST['username']:"";
                    } else { // if the student exists get their info from a previous entry
                        $query = "SELECT * FROM students WHERE student_id = ".$student_id . " LIMIT 1";
                        $result = $conn->query($query);
                        $row = $result->fetch_assoc();
                        // set the student's info from the previous entry
                        $first_name = $row['first_name'];
                        $last_name = $row['last_name'];
                        $username = $row['username'];
                    }
                    // insert the student into the database
                    $query = "INSERT INTO `students` (`first_name`, `last_name`, `username`, `club_id`) VALUES (:first_name, :last_name, :username, :club_id)";
                    $stmt = $pdo->prepare($query); // prepare the query
                    // bind the parameters
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':club_id', $club_id);
                    $stmt->execute(); // execute the query
                }
                $_SESSION['alert'] = "Student added successfully"; // set the alert
                header('Location:dashboard.php?alert=success'); // redirect to dashboard
                exit;
            } catch(PDOException $exception){ //to handle error
                $_SESSION['alert'] = "Student could not be added. Please try again."; // set the alert
                header('Location:dashboard.php?alert=danger'); // redirect to dashboard
                exit;
            }
        } else if (isset($_POST['edit-student'])){ // if the user is editing a student
            try {
                if (isset($_POST['student'])){ // if the student id is set
                    $id = $_POST['student']; // get the student id
                    $username = isset($_POST['username'])?$_POST['username']:""; // get the username
                    $first_name = isset($_POST['first_name'])?$_POST['first_name']:""; // get the first name
                    $last_name = isset($_POST['last_name'])?$_POST['last_name']:""; // get the last name
                    $clubs = isset($_POST['club'])?$_POST['club']:array(); // get the clubs
                    $sql = "SELECT * FROM students WHERE username IN (SELECT username FROM `students` WHERE student_id = " . $id . ")"; // get all the students with the same username
                    $result = $conn->query($sql); // execute the query
                    $entries = $result->num_rows; // get the number of entries
                    $index = 0; // set the index to 0
                    while ($row = $result->fetch_assoc()){ // for every entry
                        if ($index < count($clubs)){ // if there are still clubs to add
                            $sql = "UPDATE students SET first_name = :first_name, last_name = :last_name, username = :username, club_id = :club_id WHERE student_id = :id"; // set this entry to be one of the selected clubs
                            $stmt = $pdo->prepare($sql); // prepare the query
                            // bind the parameters
                            $stmt->bindParam(':id', $row['student_id']);
                            $stmt->bindParam(':first_name', $first_name);
                            $stmt->bindParam(':last_name', $last_name);
                            $stmt->bindParam(':username', $username);
                            $stmt->bindParam(':club_id', $clubs[$index]);
                            $stmt->execute(); // execute the query
                        } else {
                            // remove extra entries that are not needed
                            $sql = "DELETE FROM students WHERE student_id = :id";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':id', $row['student_id']);
                            $stmt->execute();
                        }
                        $index++;
                    }
                    if ($entries <= count($clubs)){ // if there are more clubs than entries
                        // add the extra entries
                        for ($i = $index; $i<count($clubs); $i++){
                            $sql = "INSERT INTO students (first_name, last_name, username, club_id) VALUES (:first_name, :last_name, :username, :club_id)"; // add this extra entry
                            $stmt = $pdo->prepare($sql); // prepare the query
                            // bind the parameters
                            $stmt->bindParam(':first_name', $first_name);
                            $stmt->bindParam(':last_name', $last_name);
                            $stmt->bindParam(':username', $username);
                            $stmt->bindParam(':club_id', $clubs[$i]);
                            $stmt->execute(); // execute the query
                        }
                    }
                }
                $_SESSION['alert'] = "Student edited successfully"; // set the alert
                header('Location:dashboard.php?alert=success'); // redirect to dashboard
            } catch(PDOException $exception){ // handle error during the database queries
                $_SESSION['alert'] = "Student could not be edited. Please try again."; // set the alert
                header('Location:dashboard.php?alert=danger'); // redirect to dashboard
            }
            exit;
        } else if (isset($_POST['delete-student'])){ // if the user is deleting a student
            try {
                if (isset($_POST['id'])){ // if the student id is set
                    $id = $_POST['id']; // get the student id
                    $sql = "DELETE FROM students WHERE username IN (SELECT username FROM students WHERE student_id = :id)"; // delete all the students with the same username
                    $stmt = $pdo->prepare($sql); // prepare the query
                    $stmt->bindParam(':id', $id); // bind the id
                    $stmt->execute(); // execute the query
                    $_SESSION['alert'] = "Student deleted successfully"; // set the alert
                    header('Location:dashboard.php?alert=success'); // redirect to dashboard
                } else {
                    $_SESSION['alert'] = "Student could not be deleted. Please try again."; // set the alert
                    header('Location:dashboard.php?alert=danger'); // redirect to dashboard
                }
            } catch (PDOException $exception){ // handle error during the database queries
                $_SESSION['alert'] = "Student could not be deleted. Please try again."; // set the alert
                header('Location:dashboard.php?alert=danger'); // redirect to dashboard
            }
            exit;
        } else if (isset($_POST['edit-leader'])){ // if the user is editing a leader
            try {
                if (isset($_POST['leader_id'])){ // if the leader id is set
                    // get the leader details
                    $id = $_POST['leader_id'];
                    $first_name = isset($_POST['first_name'])?$_POST['first_name']:"";
                    $last_name = isset($_POST['last_name'])?$_POST['last_name']:"";
                    $contact = isset($_POST['contact'])?$_POST['contact']:"";
                    $leader_type = isset($_POST['leader_type'])?$_POST['leader_type']:"";
                    $sql = "UPDATE leaders SET first_name = :first_name, last_name = :last_name, email = :contact, leader_type = :leader_type WHERE leader_id = :id"; // update the leader details
                    $stmt = $pdo->prepare($sql); // prepare the query
                    // bind the parameters
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':first_name', $first_name);
                    $stmt->bindParam(':last_name', $last_name);
                    $stmt->bindParam(':contact', $contact);
                    $stmt->bindParam(':leader_type', $leader_type);
                    $stmt->execute(); // execute the query
                    $_SESSION['alert'] = "Leader edited successfully"; // set the alert
                    header('Location:dashboard.php?alert=success'); // redirect to dashboard
                } else { // if the leader id is not set
                    $_SESSION['alert'] = "Leader could not be edited. Please try again."; // set the alert
                    header('Location:dashboard.php?alert=danger'); // redirect to dashboard
                }
            } catch (PDOException $exception){ // handle error during the database queries
                $_SESSION['alert'] = "Leader could not be edited. Please try again."; // set the alert
                header('Location:dashboard.php?alert=danger'); // redirect to dashboard
            }
            exit;
        } else if (isset($_POST['delete-leader'])){ // if the user is deleting a leader
            try{
                $id = isset($_POST['id'])?$_POST['id']:0; // get the leader id, if not set, set to 0 so that it won't delete anything
                $sql = "DELETE FROM leaders WHERE leader_id = :id"; // delete the leader
                $stmt = $pdo->prepare($sql); // prepare the query
                $stmt->bindParam(':id', $id); // bind the id
                $stmt->execute(); // execute the query
                $_SESSION['alert'] = "Leader deleted successfully"; // set the alert
                header('Location:dashboard.php?alert=success'); // redirect to dashboard
            } catch (PDOException $exception){ // handle error during the database queries
                $_SESSION['alert'] = "Leader could not be deleted. Please try again."; // set the alert
                header('Location:dashboard.php?alert=danger'); // redirect to dashboard
            }
            exit;
        } else { // if an action is not set
            $_SESSION['alert'] = "Load page reached without a valid action."; // set the alert
            header('Location:index.php?alert=warning'); // redirect to index
            exit;
        }
    ?>
</body>
</html>
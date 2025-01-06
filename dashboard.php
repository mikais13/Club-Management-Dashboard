<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title> <!-- This is the title of the page -->
    <!-- The meta tags for the site -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</head>
<body>
    <?php 
        // Include the session and database configuration files
        include 'includes/session.php';
        include 'includes/db_config.php';
    ?>
    <div class="container-md mt-4">
        <?php
            // Include the navbar
            $page_name = "Dashboard";
            $search_bar = false;
            include 'includes/navbar.php';
            // Show the dashboard in an accordion 
            echo '<div class="accordion accordion-flush mt-3 shadow-sm" id="dashboardAccordion">';
            if (isset($_SESSION['username']) && isset($_SESSION['access_type'])){ // If the user is logged in
                // Get all of the clubs
                $sql = "SELECT * FROM clubs JOIN leaders ON clubs.leader_id = leaders.leader_id";
                $result = $conn->query($sql);
                $leader_ids = array();
                if ($result->num_rows > 0) { // if there are clubs in the database
                    // Show the clubs accordion item, which contains a button to take the user to add a new club, and a table of all of the clubs
                    // the table of clubs contains rows for each club that contains the club name, categories, leader, contact, meetings, description, and actions, such as edit and delete based on the user's permissions
                    echo '<div class="col accordion-item"> 
                            <div class="row accordion-header" id="clubsHeader">
                                <div class="col mt-2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#clubsBody" aria-expanded="false" aria-controls="clubsBody">
                                        <h3>Clubs</h3>
                                    </button>
                                </div>
                            </div>
                            <div class="row accordion-collapse collapse" id="clubsBody" aria-labelledby="clubsHeader" data-bs-parent="#dashboardAccordion">
                                <div class="accordion-body">
                                    <div class="col text-center">
                                        <a href="set_club.php" class="btn btn-lg btn-success bi bi-plus-lg shadow-sm"> Add New Club</a>
                                    </div>
                                    <div class="col table-responsive-lg">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Club Name</th>
                                                    <th scope="col">Categories</th>
                                                    <th scope="col">Leader</th>
                                                    <th scope="col">Contact</th>
                                                    <th scope="col">Meetings</th>
                                                    <th scope="col">Description</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                    while($row = $result->fetch_assoc()) { // for each club
                        // get leader_id and add it to the array of leader_ids if it is not already in the array
                        $leader_id = $row['leader_id'];
                        if (!in_array($leader_id, $leader_ids)){
                            array_push($leader_ids, $leader_id);
                        }
                        $id = $row['club_id']; // get the club id
                        // get the categories and meetings and format them
                        $categories = str_replace("'","",trim($row['categories'], "[]"));
                        $categories = str_replace(',',', ',$categories);
                        $meetings = trim($row['meetings'], "[]");
                        $meetings = explode("},{",$meetings);
                        for ($i = 0; $i < count($meetings); $i++){
                            if (substr($meetings[$i],0,1) !=  "{"){
                                $meetings[$i] = '{'.$meetings[$i];
                            }
                            if (substr($meetings[$i],-1) !=  "}"){
                                $meetings[$i] = $meetings[$i].'}';
                            }
                            $meetings[$i] = json_decode($meetings[$i],true);
                        }
                        // show the club in a table row
                        echo                        '<tr>
                                                        <td><a href="club.php?club='.$id.'&dashboard=true">'.$row['name'].'</a></td>
                                                        <td>'.$categories.'</td>
                                                        <td>'.$row['first_name'].' '.$row['last_name'].'</td>
                                                        <td>'.$row['email'].'</td>
                                                        <td>';
                        for ($i = 0; $i < count($meetings); $i++){ // for each meeting
                            // show the date, time, location, and details if there are any in the correct format
                            // format: date time - location
                            echo $meetings[$i]['date'].' '.$meetings[$i]['time'].' - '.$meetings[$i]['location'].'<br>';
                            if ($meetings[$i]['details'] != ""){
                                echo $meetings[$i]['details'].'<br>';
                            }
                        }
                        echo                            '</td>
                                                        <td>'.$row['description'].'</td>
                                                        <td>
                                                            <form action="load.php" method="POST">
                                                                <input type="hidden" name="id" value="'.$id.'">
                                                                <a href="set_student.php?club='.$id.'" class="btn btn-success">Add</a>
                                                                <input type="submit" class="btn btn-primary" name="edit" value="Edit"></input>';
                        if ($_SESSION['access_type'] == 0){ // if the user is an admin let them delete clubs
                            echo                                '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#club'.$id.'Modal">Delete</button>';
                        }
                        // show the modal for deleting the club so that the user is warnws before deleting the club
                        echo                                    '<div class="modal fade" id="club'.$id.'Modal" tabindex="-1" aria-labelledby="club'.$id.'ModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h1 class="modal-title fs-5" id="club'.$id.'ModalLabel">Warning!</h1>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                Are you sure you want to delete <b>' . $row['name'] . '</b> from the database?
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <input type="submit" class="btn btn-danger" name="delete" value="Delete"></input>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    </tr>';
                    }
                    echo '                      </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                } else { // if there are no clubs
                    echo "0 clubs found";
                }
                // get all the leaders
                $sql = "SELECT * FROM leaders";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) { // if there are leaders display them
                    // Show the leaders accordion item, which contains a table of all of the leaders
                    // the table of leaders contains a row for each leader which contains the leader's type, name, email and the actions which are based on the user's permissions
                    echo '<div class="col accordion-item"> 
                            <div class="row accordion-header" id="leadersHeader">
                                <div class="col mt-2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#leadersBody" aria-expanded="false" aria-controls="leadersBody">
                                        <h3>Leaders</h3>
                                    </button>
                                </div>
                            </div>
                            <div class="row accordion-collapse collapse" id="leadersBody" aria-labelledby="leadersHeader" data-bs-parent="#dashboardAccordion">
                                <div class="accordion-body">
                                    <div class="col table-responsive-lg">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">First Name</th>
                                                    <th scope="col">Last Name</th>
                                                    <th scope="col">Email</th>';
                                                    if ($_SESSION['access_type'] == 0){ // if the user is an admin include the actions column
                                                        echo '<th scope="col">Actions</th>';
                                                    }
                                            echo '</tr>
                                            </thead>
                                            <tbody>';
                    while($row = $result->fetch_assoc()) { // for each leader
                        $id = $row['leader_id']; // get the leader's id
                        switch ($row['leader_type']){ // get the leader's type and format into a string
                            case 0: $type = "Teacher"; break;
                            case 1: $type = "Student"; break;
                            case 2: $type = "External Provider"; break;
                            default: $type = "Unknown"; break;
                        }
                        // Display the leader in a table row
                        echo                        '<tr>
                                                        <td>'.$type.'</td>
                                                        <td>'.$row['first_name'].'</td>
                                                        <td>'.$row['last_name'].'</td>
                                                        <td>'.$row['email'].'</td>';
                        if ($_SESSION['access_type'] == 0){ // if the user is an admin include the actions column
                                                    echo '<td>
                                                            <form action="load.php" method="POST">
                                                                <a href="edit_leader.php?id='.$id.'" class="btn btn-primary">Edit</a>
                                                                <input type="hidden" name="id" value="'.$row['leader_id'].'">
                                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#leader'.$id.'Modal">Delete</button>
                                                                <div class="modal fade" id="leader'.$id.'Modal" tabindex="-1" aria-labelledby="leader'.$id.'ModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h1 class="modal-title fs-5" id="leader'.$id.'ModalLabel">Warning!</h1>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                Are you sure you want to delete <b>'. $row['first_name'] . ' ' . $row['last_name'] . '</b> from the database?';
                                                                                if (in_array($id,$leader_ids)){
                                                                                    echo '<br><br>This leader is currently assigned to a club. Please reassign the club before deleting this leader.';
                                                                                }
                                                                            echo '</div>
                                                                            <div class="modal-footer">
                                                                                <input type="submit" class="btn btn-danger" name="delete-leader" value="Delete"'.(in_array($id,$leader_ids)?'disabled':'').'></input>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </td>';
                        }
                                                echo '</tr>';
                    }
                    echo '                      </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                } else { // if there are no leaders
                    echo "0 leaders found";
                }
                // get all the students
                $sql = "SELECT * FROM students JOIN clubs ON students.club_id = clubs.club_id ORDER BY last_name ASC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) { // if there are students display them
                    // Show the students accordion item, which contains a table of all of the students
                    // the table of students contains a row for each student which contains the student's name, username, clubs they are a part of and the actions which are based on the user's permissions
                    echo '<div class="col accordion-item"> 
                            <div class="row accordion-header" id="clubsHeader">
                                <div class="col mt-2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#studentsBody" aria-expanded="false" aria-controls="studentsBody">
                                        <h3>Students</h3>
                                    </button>
                                </div>
                            </div>
                            <div class="row accordion-collapse collapse" id="studentsBody" aria-labelledby="studentsHeader" data-bs-parent="#dashboardAccordion">
                                <div class="accordion-body">
                                    <div class="col text-center">
                                        <a href="set_student.php" class="btn btn-lg btn-success bi bi-plus-lg shadow-sm"> Add New Student</a>
                                    </div>
                                    <div class="col table-responsive-lg">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Username</th>
                                                    <th scope="col">Clubs</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                    $all_students = array(); // array to store all students
                    while($row = $result->fetch_assoc()) { // for each student get their info
                        $id = $row['student_id'];
                        $name = $row['first_name'].' '.$row['last_name'];
                        $username = $row['username'];
                        $clubs = $row['name'];
                        $student = array($id,$name,$username,$clubs); // create an array of the student's info to represent the student
                        array_push($all_students,$student); // add student to the array of all students
                    }
                    $students = array(); // array to store students, merging where entries have the same username
                    for ($i=0;$i<count($all_students);$i++){
                        // if username is already in students change that student to add the other club
                        $found = false;
                        for ($j=0;$j<count($students);$j++){
                            if ($all_students[$i][2] == $students[$j][2]){ // if the current student's username is already in the array change the entry to add the new club
                                $students[$j][3] = $all_students[$i][3].'<br>'.$students[$j][3];
                                $found = true;
                            }
                        }
                        if (!$found){ // if the student is not already in the students array add them
                            array_push($students,$all_students[$i]);
                        }
                    }
                    for ($i=0;$i<count($students);$i++){  // for each student display their remaining info
                        echo                        '<tr>
                                                        <td>'.$students[$i][1].'</td>
                                                        <td>'.$students[$i][2].'</td>
                                                        <td>'.$students[$i][3].'</td>
                                                        <td>
                                                            <form action="load.php" method="POST">
                                                                <input type="hidden" name="id" value="'.$students[$i][0].'">
                                                                <a href="set_student.php?student='.$students[$i][0].'" class="btn btn-primary shadow">Edit</a>';
                        if ($_SESSION['access_type'] == 0){ // if the user is an admin allow them to delete students
                            echo                                '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#student'.$students[$i][0].'Modal">Delete</button>';
                        }
                        echo                                '</div>
                                                            <div class="modal fade" id="student'.$students[$i][0].'Modal" tabindex="-1" aria-labelledby="student'.$students[$i][0].'ModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h1 class="modal-title fs-5" id="student'.$students[$i][0].'ModalLabel">Warning!</h1>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            Are you sure you want to delete <b>'. $students[$i][1] . '</b> from the database?
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="submit" class="btn btn-danger" name="delete-student" value="Delete"></input>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    </tr>';
                    }
                    echo '                      </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                } else { // if there are no students display a message
                    echo "0 students found";
                }
                if ($_SESSION['access_type'] == 0 || $_SESSION['access_type'] == 1){ // if the user is an admin or teacher show the users accordion item
                    // Get all of the users
                    $sql = "SELECT * FROM users ORDER BY access_type ASC, username ASC";
                    $result = $conn->query($sql);
                    // Show the accordion item for users
                    echo '<div class="col accordion-item"> 
                            <div class="row accordion-header" id="usersHeader">
                            <div class="col mt-2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#usersBody" aria-expanded="false" aria-controls="usersBody">
                                        <h3>Users</h3>
                                    </button>
                                </div>
                            </div>';
                    if ($result->num_rows > 0){ // if there are users display them
                        echo '<div class="row accordion-collapse collapse" id="usersBody" aria-labelledby="usersHeader" data-bs-parent="#dashboardAccordion">
                                <div class="accordion-body">';
                        if ($_SESSION['access_type'] <= 1){ // if the users is an admin or teacher allow them to add new users
                            echo '<div class="col text-center">
                                    <a href="register.php" class="btn btn-lg btn-success bi bi-plus-lg shadow-sm"> Add New User</a>
                                </div>';
                        }
                        // Show the table of users
                                echo '<div class="col table-responsive-lg">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Username</th>
                                                    <th scope="col">Access Type</th>
                                                    <th scope="col">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                        // Get all of the admins in a separate query
                        $sql = "SELECT * FROM users WHERE access_type = 0";
                        $result_admin = $conn->query($sql);
                        while($row = $result->fetch_assoc()){ // for each user display their info
                            // get the user's info
                            $id = $row['user_id'];
                            $username = $row['username'];
                            $access_type = $row['access_type'];
                            $access_type_num = $access_type;
                            switch ($access_type){ // convert the access type number to a string
                                case 0: $access_type = 'Admin'; break;
                                case 1: $access_type = 'Teacher'; break;
                                case 2: $access_type = 'Student'; break;
                                default: $access_type = 'Unknown'; break;
                            }
                            if ($_SESSION['access_type'] < $access_type_num || $_SESSION['username'] == $username){ // if the user in this row has less permissions than the logged in user or is the logged in user display them
                                echo '              <tr>
                                                        <td>'.$username.'</td>
                                                        <td>'.$access_type.'</td>
                                                        <td>';
                                if (($_SESSION['access_type'] == 0 && ($access_type != 'Admin' || $username == $_SESSION['username'])) || ($_SESSION['access_type'] == 1 && ($access_type == 'Student' || $access_type == 'Unknown' || $_SESSION['username'] == $username))){ // If the user has permission to edit the row's user then show the edit buttons
                                                        echo '<a href="account.php?user='.$id.'" class="btn btn-primary shadow">Edit</a>';
                                    if (($_SESSION['username'] == $username && $_SESSION['access_type'] == 1) || ($_SESSION['access_type'] == 0 && ($access_type != 'Admin' || $_SESSION['username'] == $username)) || ($_SESSION['access_type'] == 1 && $access_type == 'Student')){ // If the user has permission to delete the row's user then show the delete button
                                        echo '                      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#user'.$id.'Modal">Delete</button>
                                                                    <div class="modal fade" id="user'.$id.'Modal" tabindex="-1" aria-labelledby="user'.$id.'ModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h1 class="modal-title fs-5" id="user'.$id.'ModalLabel">Warning!</h1>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    Are you sure you want to delete ' . ($access_type!='Unknown'?strtolower($access_type).' ':'') . '<b>'. $username . '</b> from the database?' . ($result->num_rows==1?'<br><br>The last user cannot be deleted as the site will no longer function.':($result_admin->num_rows==1?"<br><br>This user cannot be removed as it is the last remaining admin, to keep the site's functionality, add another admin before removing this account.":'')) . '
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <form action="load.php" method="POST">
                                                                                        <input type="hidden" name="user_id" value="'.$id.'">
                                                                                        <input type="submit" class="btn btn-danger" name="delete-user" value="Delete"'.(($result->num_rows==1||$result_admin->num_rows==1)?' disabled':'').'></input>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>';
                                    }
                                }
                                                    
                                                    echo '</td>
                                                    </tr>';
                            }
                        }
                    echo '                      </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                    } else { // if there are no users display a message
                        echo "0 users found";
                    }
                    echo '</div>';
                } else if ($_SESSION['access_type'] == 2){ // if the user is a student display a button to edit their own account as this is the only one they have permissions for
                    echo    '<div class="col accordion-item">
                                <div class="row accordion-header" id="accountHeader">
                                    <div class="col mt-2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accountBody" aria-expanded="false" aria-controls="accountBody">
                                            <h3>Account</h3>
                                        </button>
                                    </div>
                                </div>
                                <div class="row accordion-collapse collapse" id="accountBody" aria-labelledby="accountHeader" data-bs-parent="#dashboardAccordion">
                                    <div class="accordion-body">
                                        <div class="col text-center mt-3">
                                            <a class="btn btn-primary btn-lg shadow" href="account.php">Edit Account Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
            } else { // if the user is not logged in display a message
                $_SESSION['alert'] = "You do not have permission to access this page. Please log in.";
                header("Location:index.php?alert=warning");
                exit;
            }
            include 'includes/footer.php'; // include the footer
            echo '</div>';
        ?>
    </div>
    <!-- Show the alert is there is one set -->
    <div class="fixed-bottom">
        <?php include 'includes/alert.php'; ?>
    </div>
</body>
</html>
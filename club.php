<!DOCTYPE html>
<html lang='en'>
<head>
    <?php
        // Include the session variables and database connection
        include 'includes/session.php';
        include 'includes/db_config.php';
        // Get the club's information from the database
        $sql = "SELECT * FROM clubs WHERE club_id = ".$_GET['club'];
        $result = $conn->query($sql);
        if ($result->num_rows == 1) { // If the club exists
            $row = $result->fetch_assoc();
            // Set the page name and get the club's information
            $page_name = $row['name'];
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
                $meetings[$i] = json_decode($meetings[$i], true);
            }
        }
    ?>
    <!-- Set the page title to the club's name -->
    <title>
        <?php echo $page_name; ?>
    </title>
    <!-- Meta tags for this page -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS and JavaScript -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Custom CSS and JavaScript -->
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="container-md mt-4"> <!-- The main container for content -->
        <?php
            // Include the navbar
            $search_bar = true;
            if (isset($_GET['dashboard']) && $_GET['dashboard'] == 'true' && isset($_SESSION['access_type']) && isset($_SESSION['username'])){
                $return = 'dashboard.php';
            }
            include 'includes/navbar.php';
            $sql = "SELECT * FROM clubs JOIN leaders ON clubs.leader_id = leaders.leader_id WHERE clubs.club_id = ".$_GET['club']; // Get the club's information from the database
            $result = $conn->query($sql);
            if ($result->num_rows > 0){ // If the club exists
                while($row = $result->fetch_assoc()){
                    // Get the club's information
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
                    // Display the club's information
                    echo '<div class="row">
                                <div class="col-lg-6">
                                    <div class="rounded-4 bg-success text-white p-3 my-3 shadow">
                                        <h3>Club Leader: '.$row['first_name'].' '.$row['last_name'].'</h3>';
                                        if ($row['email'] != ""){echo '<h6>Contact: '.$row['email'].'</h6>';}
                    echo            '</div>
                                    <p>'.$row['description'].'</p>
                                    <p>Meeting Times:<br>';
                                for ($i = 0; $i < count($meetings); $i++){ // Loop through the club's meeting times and display them
                                    echo $meetings[$i]['date'].' '.$meetings[$i]['time'].' - '.$meetings[$i]['location'].'<br>';
                                    if ($meetings[$i]['details'] != ""){ // If there are details for the meeting, display them as they are not required
                                        echo $meetings[$i]['details'].'<br>';
                                    }
                                    echo '<br>';
                                }
                                echo '</p>
                                </div>
                            </div>';
                }
                if (isset($_SESSION['access_type']) && (($_SESSION['access_type'] == '0' || $_SESSION['access_type'] == '1'))){ // if a teacher or admin view students in the club
                    // Show a button to add more students to the club
                    echo '<div class="row mb-2">
                            <div class="col-auto order-2">
                                <a class="btn btn-success bi bi-plus-lg shadow" href="set_student.php?club='.$_GET['club'].'"> Add Student</a>
                            </div>';
                    // Get all of the students in the club
                    $sql = "SELECT * FROM students WHERE club_id = ".$_GET['club'];
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0){ // If there are students in the club, display them
                        echo '<div class="col-auto order-1">
                                <a class="btn btn-primary shadow" data-bs-toggle="collapse" href="#students" role="button" aria-expanded="false" aria-controls="students">Students <span class="bi bi-chevron-down"></span></a>
                            </div>
                        </div>
                        <div class="collapse" id="students">';
                        while ($row = $result->fetch_assoc()){ // Loop through the students and display them
                            echo '<div class="row">
                                <div class="col-lg-6">
                                    <p>'.$row['first_name'].' '.$row['last_name'].'</p>
                                </div>
                            </div>';
                        }
                    }
                    echo '</div>';
                } else if (!isset($_SESSION['access_type'])) { // If the user is not logged in, show a button to log in to view students
                    echo '<div class="row mb-2">
                            <div class="col-auto">
                                <a href="login.php?club='.$_GET['club'].'" class="btn btn-primary shadow">Log In to View Students</a>
                            </div>
                        </div>';
                }
            } else { // If the club does not exist, inform the user
                echo "Club could not be found";
            }
            // Include the footer
            include 'includes/footer.php';
        ?>
    </div>
    <!-- Include the alert modal -->
    <?php include 'includes/alert.php'; ?>
</body>
</html>
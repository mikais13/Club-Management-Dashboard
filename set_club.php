<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        // include the session variables and check if the user is logged in and set up the database
        include 'includes/session.php';
        include 'includes/db_config.php';
        if (isset($_GET['id'])){ // if editing a club
            $edit = true; // set edit to true
            $club_info = array(); // create an array to store the club info
            $sql = "SELECT * FROM clubs WHERE club_id = ".$_GET['id']; // get the club info from the database
            $result = $conn->query($sql);
            if ($result->num_rows == 1) { // if the club exists
                $row = $result->fetch_assoc(); // get the club info
                // store the club info in variables
                $name = $row['name'];
                $description = $row['description'];
                $categories = str_replace("'","",trim($row['categories'], "[]"));
                $categories = str_replace(',',', ',$categories);
                $categories = explode(', ',$categories);
                $meetings = explode("},{",trim($row['meetings'], "[]"));
                for ($i = 0; $i < count($meetings); $i++){
                    if (substr($meetings[$i],0,1) !=  "{"){
                        $meetings[$i] = '{'.$meetings[$i];
                    }
                    if (substr($meetings[$i],-1) !=  "}"){
                        $meetings[$i] = $meetings[$i].'}';
                    }
                }
                for ($i = 0; $i < count($meetings); $i++){
                    $meetings[$i] = json_decode($meetings[$i],true);
                }
                $leader_id = $row['leader_id'];
                $sql = "SELECT * FROM leaders WHERE leader_id = $leader_id"; // get the leader info from the database
                $result = $conn->query($sql);
                if ($result->num_rows == 1){
                    $row = $result->fetch_assoc(); // get the leader info
                    // store the leader info in variables
                    $leader = $row['first_name'].' '.$row['last_name'];
                    $leader_contact = $row['email'];
                }
            } else { // if the club doesn't exist, redirect to the dashboard and set an alert to inform the user
                $_SESSION['alert'] = "The club you are trying to edit doesn't exist.";
                header('Location: dashboard.php?alert=danger');
                exit;
            }
        } else { // if adding a club
            $edit = false;
            $club_info = null;
        }
    ?>
    <title><?php echo $edit ? 'Edit' : 'Add'; ?> Club</title> <!-- set the page title according to whether the club is being added or edited -->
    <!-- Them meta tags for the page -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The Bootstrap files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap icons file -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- The custom CSS file -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    <script src="js/removeInput.js"></script>
</head>
<body>
    <div class="container-md mt-4"> <!-- the main container for the page -->
        <?php
            // include the navbar
            $page_name = $edit ? 'Edit' : 'Add' . ' Club';
            $search_bar = false;
            $return = 'dashboard.php';
            include 'includes/navbar.php';
        ?>
        <div class="row">
            <div class="col">
                <form action="load.php" method="POST">
                    <div class="row my-3">
                        <div class="col-lg-6"> <!-- The club name input section -->
                            <label for="club-name" class="form-label">Club Name</label>
                            <?php
                                if ($edit) {
                                    echo '<input type="text" class="form-control" id="club-name" name="club-name" value="'.$name.'" required>';
                                } else {
                                    echo '<input type="text" class="form-control" id="club-name" name="club-name" required>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="row my-3"> <!-- The club category input section -->
                        <div class="col">
                            <label for="club-category" class="form-label d-block position-relative">Club Categories:</label> <!-- The label for the inputs -->
                            <button type="button" class="d-inline-block btn btn-primary mb-3 shadow-sm bi bi-plus-lg" id="add-category"> Add Category</button> <!-- Allow the user to add more inputs -->
                            <div class="horizontal-scroll"> <!-- The div that allows the inputs to be scrolled horizontally -->
                                <div class="d-inline-block" id="category-input"> <!-- The div that contains the current categories inputs or a single blank input -->
                                    <?php
                                        echo '<div class="border border-2 border-light-subtle rounded-3 p-1 shadow-sm" id="category-input-1">';
                                        if ($edit) { // if editing, then show the first category here, the rest will be shown in the extra-inputs div
                                            echo '<input type="text" class="form-control shadow-sm m-1" id="club-category" name="club-category[]" value="'.$categories[0].'" required>';
                                        } else { // if adding a new club, include a single input
                                            echo '<input type="text" class="form-control shadow-sm m-1" id="club-category" name="club-category[]" required>';
                                        }
                                        echo '</div>';
                                    ?>
                                </div>
                                <div class="d-inline-block align-items-center" id="extra-category-inputs"> <!-- space for extra inputs to be included if added -->
                                    <?php
                                        if ($edit) { // if editing, then loop through all of the categories and add an input for each one
                                            for ($i = 1; $i < count($categories); $i++){
                                                echo '<div class="d-inline-block mx-2" id="category-input-'.($i+1).'">
                                                    <div class="input-group border border-2 border-light-subtle rounded-3 p-1 shadow-sm mx-1">
                                                        <input type="text" class="form-control shadow-sm rounded-3 m-1" id="club-category" name="club-category[]" value="'.$categories[$i].'" required>
                                                        <span clas="input-group-btn">
                                                            <button type="button" class="btn btn-danger rounded-3 bi bi-dash text-light m-1" id="remove-category-'.($i+1).'" onclick="removeCategory('.($i+1).')"></button>
                                                        </span>
                                                    </div>
                                                </div>';
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3"> <!-- The club leader input section -->
                        <div class="col-lg-6">
                            <label for="club-leader" class="form-label">Club Leader</label> <!-- The label for the input -->
                            <select class="form-select shadow-sm" id="club-leader" name="club-leader"> <!-- Allow the user to select from current leaders -->
                                <?php
                                    $sql = "SELECT * FROM leaders"; // get all of the leaders in the database
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) { // if there are leaders in the database
                                        while($row = $result->fetch_assoc()) { // loop through all of the leaders and add them to the select
                                            if ($edit && $row['leader_id'] == $leader_id){ // if the leader is currently the leader in the club being edited, select them
                                                echo '<option value="'.$row['leader_id'].'" selected>'.$row['first_name'].' '.$row['last_name'].'</option>';
                                            } else {
                                                echo '<option value="'.$row['leader_id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>';
                                            }
                                        }
                                    }
                                ?>
                                <option value="0">Other:</option> <!-- Allow the user to add a new leader, when this is selected, the #add-leader div appears and the user and add a new leader's info -->
                            </select>
                            <div class="mt-3" id="add-leader"> <!-- The div that contains the inputs for adding a new leader -->
                                <input type="text" class="form-control mb-1 shadow-sm" id="leader-first-name" name="leader-first-name" placeholder="First Name">
                                <input type="text" class="form-control mb-1 shadow-sm" id="leader-last-name" name="leader-last-name" placeholder="Last Name">
                                <input type="text" class="form-control mb-1 shadow-sm" id="leader-contact" name="leader-contact" placeholder="Contact">
                                <select class="form-select mb-3 shadow-sm" id="leader-type" name="leader-type">
                                    <option value="0">Staff</option>
                                    <option value="1">Student</option>
                                    <option value="2">External Provider</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3"> <!-- The club meetings input section -->
                        <div class="col-lg-6">
                            <label for="club-meeting-times" class="form-label">Club Meeting Times</label> <!-- The label for the inputs -->
                            <?php
                                if ($edit){ // if editing a club, loop through all of the meetings and add an input for each one
                                    for ($i = 0; $i < count($meetings); $i++){ // loop through every meeting
                                        if ($i == 1){ //if the meeting is the second, then add the extra-meeting-times div
                                            echo "<div class='d-inline-block' id='extra-meeting-times'>";
                                        }
                                        // get the meeting info
                                        $day = isset($meetings[$i]['date']) ? trim($meetings[$i]['date']) : '';
                                        $time = isset($meetings[$i]['time']) ? trim($meetings[$i]['time']) : '';
                                        $location = isset($meetings[$i]['location']) ? trim($meetings[$i]['location']) : '';
                                        $details = isset($meetings[$i]['details']) ? trim($meetings[$i]['details']) : '';
                                        // add the meeting inputs
                                        echo '<div class="mb-3 border border-2 border-light-subtle rounded-3 p-1 shadow-sm" id="add-meeting-time-'.($i+1).'">';
                                        if ($i > 0){ // if the meeting is not the first, then add a remove button
                                            echo '<div class="position-relative">
                                                    <button type="button" class="position-absolute top-0 start-100 translate-middle border border-light bg-danger rounded-circle bi bi-dash text-light" id="remove-meeting-time-'.($i+1).'" onclick="removeMeeting('.($i+1).')"></button>
                                                </div>';
                                        }
                                            echo '<div class="row p-1">
                                                    <div class="col">
                                                        <select class="form-select shadow-sm" id="meeting-day" name="meeting-day[]">
                                                            <option value="Monday"'.($day == 'Monday' ? ' selected' : '').'>Monday</option>
                                                            <option value="Tuesday"'.($day == 'Tuesday' ? ' selected' : '').'>Tuesday</option>
                                                            <option value="Wednesday"'.($day == 'Wednesday' ? ' selected' : '').'>Wednesday</option>
                                                            <option value="Thursday"'.($day == 'Thursday' ? ' selected' : '').'>Thursday</option>
                                                            <option value="Friday"'.($day == 'Friday' ? ' selected' : '').'>Friday</option>
                                                            <option value="Saturday"'.($day == 'Saturday' ? ' selected' : '').'>Saturday</option>
                                                            <option value="Sunday"'.($day == 'Sunday' ? ' selected' : '').'>Sunday</option>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control shadow-sm" id="meeting-time" name="meeting-time[]" placeholder="Time" value="'.$time.'">
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control shadow-sm" id="meeting-place" name="meeting-place[]" placeholder="Location" value="'.$location.'">
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <input type="text" class="form-control shadow-sm" id="meeting-details" name="meeting-details[]" placeholder="Details" value="'.$details.'">
                                                    </div>
                                                </div>
                                            </div>';
                                        if ($i == count($meetings) - 1 && count($meetings) > 1){ // if the meeting is the last and there is more than one meeting, close the extra-meeting-times div
                                            echo "</div>";
                                        }
                                    }
                                    if (count($meetings) < 2){ // if there is only one meeting, add the extra-meeting-times div
                                        echo "<div class='d-inline-block' id='extra-meeting-times'></div>";
                                    }
                                } else { // if adding a club, add a single input for the first meeting
                                    echo '<div class="mb-3 border border-2 border-light-subtle rounded-3 p-1 shadow-sm" id="add-meeting-time-1">
                                            <div class="row">
                                                <div class="col">
                                                    <select class="form-select shadow-sm" id="meeting-day" name="meeting-day[]" required>
                                                        <option value="Monday">Monday</option>
                                                        <option value="Tuesday">Tuesday</option>
                                                        <option value="Wednesday">Wednesday</option>
                                                        <option value="Thursday">Thursday</option>
                                                        <option value="Friday">Friday</option>
                                                        <option value="Saturday">Saturday</option>
                                                        <option value="Sunday">Sunday</option>
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <input type="text" class="form-control shadow-sm" id="meeting-time" name="meeting-time[]" placeholder="Time" required>
                                                </div>
                                                <div class="col">
                                                    <input type="text" class="form-control shadow-sm" id="meeting-place" name="meeting-place[]" placeholder="Location" required>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <input type="text" class="form-control shadow-sm" id="meeting-details" name="meeting-details[]" placeholder="Details">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-inline-block" id="extra-meeting-times"></div>';
                                }
                            ?>
                            <button type="button" class="btn btn-primary shadow bi bi-plus-lg" id="add-meeting-time"> Add Meeting Time</button> <!-- The button to add more meeting inputs -->
                        </div>
                    </div>
                    <div class="row my-3"> <!-- The club description input section -->
                        <div class="col-lg-6">
                            <label for="club-description" class="form-label">Club Description</label> <!-- The label for the input -->
                            <textarea class="form-control shadow-sm" id="club-description" name="club-description" rows="3" required><?php echo $edit?$description:'';?></textarea> <!-- The input for the club description -->
                        </div>
                    </div>
                    <div class="row my-3"> <!-- The submit button section -->
                        <div class="col">
                            <?php
                                if ($edit) { // if editing, add a hidden input for the club id and change the button to save changes
                                    echo '<input type="hidden" name="club-id" value="'.$_GET['id'].'">';
                                    echo '<input type="submit" class="btn btn-success shadow" name="edit-club" value="Save Changes"></input>';
                                } else { // if adding a club, change the button to add club
                                    echo '<input type="submit" class="btn btn-success shadow" name="add-club" value="Add Club"></input>';
                                }
                            ?>
                            <a href="dashboard.php" class="btn btn-secondary shadow"> Return</a> <!-- Allow the user to return to the dashboard if they wish -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'includes/footer.php';?> <!-- Include the footer -->
    </div>
</body>
</html>
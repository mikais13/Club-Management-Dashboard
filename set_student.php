<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $page_name = 'Add Student';
        if (isset($_GET['club'])){ // add to a club
            $id = $_GET['club'];
            $return = 'club.php?club='.$id;
            echo '<title>Add Student</title>';
        } else if (isset($_GET['student'])){ // edit student from dashboard
            $id = $_GET['student'];
            $return = 'dashboard.php';
            $page_name = 'Edit Student';
            echo '<title>Edit Student</title>';
        } else { // add in general from dashboard
            $id = 0;
            $return = 'dashboard.php';
            echo '<title>Add Student</title>';
        }
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="container-md mt-4">
        <?php
            include 'includes/session.php';
            include 'includes/db_config.php';
            $search_bar = false;
            include 'includes/navbar.php';
            $sql = "SELECT student_id,first_name,last_name,username FROM `students` WHERE username NOT IN (SELECT username FROM `students` WHERE club_id = " . $id . ") GROUP BY username ORDER BY last_name ASC";
            $result = $conn->query($sql);
        ?>
        <div class="row">
            <div class="col-lg-6">
                <form action="load.php" method="POST">
                    <label for="student" class="form-label">Student Info:</label>
                    <?php 
                        if (isset($_GET['club'])){
                            echo '<select class="form-select mb-3 shadow-sm" id="studentSelect" name="student">
                                <option value="0" selected>New Student</option>';
                            if ($result->num_rows > 0){
                                while($row = $result->fetch_assoc()){
                                    echo '<option value="'.$row['student_id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>';
                                }
                            }
                            echo '</select>';
                        } else if (isset($_GET['student'])){
                            $sql = "SELECT * FROM `students` WHERE student_id = " . $id;
                            $result = $conn->query($sql);
                            if ($result->num_rows == 1){
                                $row = $result->fetch_assoc();
                                $first_name = $row['first_name'];
                                $last_name = $row['last_name'];
                                $username = $row['username'];
                            }
                        }
                    ?>
                    <div class="row" id="newStudent">
                        <div class="col">
                            <label for="first_name" class="form-label">First Name:</label>
                            <input type="text" class="form-control mb-3 shadow-sm" id="first_name" name="first_name" placeholder="First Name" <?php echo (isset($first_name)?'value="'.$first_name.'"':'');?> required>
                        </div>
                        <div class="col">
                            <label for="last_name" class="form-label">Last Name:</label>
                            <input type="text" class="form-control mb-3 shadow-sm" id="last_name" name="last_name" placeholder="Last Name" <?php echo (isset($last_name)?'value="'.$last_name.'"':'');?> required>
                        </div>
                        <div class="col">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control mb-3 shadow-sm" id="username" name="username" placeholder="Username" <?php echo (isset($username)?'value="'.$username.'"':'');?> required>
                        </div>
                    </div>
                    <div class="row">
                        <!-- select current club but allow to change to any other club -->
                        <label for="club" class="form-label">Clubs:</label>
                        <?php
                            $sql = "SELECT * FROM clubs ORDER BY name ASC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0){
                                if (isset($_GET['student'])){
                                    $sql = "SELECT * FROM `students` WHERE username IN (SELECT username FROM `students` WHERE students.student_id = " . $id . ')';
                                    $result_clubs = $conn->query($sql);
                                    $clubs = array();
                                    while ($row = $result_clubs->fetch_assoc()){
                                        array_push($clubs,$row['club_id']);
                                    }
                                    if ($result->num_rows > 0){
                                        while ($row = $result->fetch_assoc()){
                                            echo '<div class="col-6 mb-2">
                                                <input type="checkbox" class="form-check-input shadow-sm" name="club[]" id="club'.$row['club_id'].'" value="'.$row['club_id'].'" '.(in_array($row['club_id'],$clubs)?"checked":"").'>
                                                <label class="form-check-label" for="club'.$row['club_id'].'">'.$row['name'].'</label>
                                            </div>';
                                        }
                                    }
                                } else if (isset($_GET['club'])){
                                    echo '<select class="form-select mb-3 shadow-sm" id="club" name="club">';
                                    $result_clubs = $conn->query($sql);
                                    while($row_clubs = $result_clubs->fetch_assoc()){
                                        echo '<option value="'.$row_clubs['club_id'].'"'.(($id==$row_clubs['club_id'])?' selected>':'>').$row_clubs['name'].'</option>';
                                    }
                                    echo '</select>';
                                } else {
                                    if ($result->num_rows > 0){
                                        echo '<select class="form-select mb-3 shadow-sm" name="club">';
                                        while ($row=$result->fetch_assoc()){
                                            echo '<option value="'.$row['club_id'].'">'.$row['name'].'</option>';
                                        }
                                        echo '</select>';
                                    } else {
                                        echo '<h5>There are no clubs to add students to.</h5><br><a href="set_club.php" class="btn btn-primary">Add Club</a>';
                                    }
                                }
                            } else {
                                echo '<h5>There are no clubs to add students to.</h5><br><a href="set_club.php" class="btn btn-primary">Add Club</a>';
                            }
                        ?>
                    </div>
                    <div class="row mt-3">
                        <?php
                            if (isset($_GET['student'])){
                                echo '<input type="hidden" name="student" value="'.$id.'">
                                <input type="submit" class="btn btn-success bi bi-plus-lg shadow w-auto" name="edit-student" value="Save Changes"></input>';
                            } else {
                                echo '<input type="submit" class="btn btn-success bi  bi-plus-lg shadow w-auto" name="add-student" value="Add Student"></input>';
                            }
                        ?>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'includes/footer.php';?>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Leader</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="container-md mt-4">
        <?php
            include 'includes/session.php';
            include 'includes/db_config.php';
            $page_name = "Edit Leader";
            $search_bar = false;
            $return = 'dashboard.php';
            include 'includes/navbar.php';
            if (isset($_GET['id'])){
                $sql = "SELECT * FROM leaders WHERE leader_id = ".$_GET['id'];
                $result = $conn->query($sql);
                if ($result->num_rows == 1){
                    $row = $result->fetch_assoc();
                    $first_name = $row['first_name'];
                    $last_name = $row['last_name'];
                    $contact = $row['email'];
                    $leader_type = $row['leader_type'];
                } else {
                    header("Location: dashboard.php");
                    exit;
                }
            } else {
                header("Location: dashboard.php");
                exit;
            }
        ?>
        <div class="row">
            <div class="col-md-6">
                <form action="load.php" method="POST">
                    <h4>Info:</h4>
                    <div class="row my-3">
                        <div class="col">
                            <label for="first_name" class="form-label">First Name:</label>
                            <input type="text" class="form-control shadow-sm" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>
                        </div>
                        <div class="col">
                            <label for="last_name" class="form-label">Last Name:</label>
                            <input type="text" class="form-control shadow-sm" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col">
                            <label for="contact" class="form-label">Contact:</label>
                            <input type="text" class="form-control shadow-sm" id="contact" name="contact" value="<?php echo $contact; ?>">
                        </div>
                    </div>
                    <?php
                        if ($_SESSION['access_type'] == 0){
                            echo '<div class="row my-3">
                                    <div class="col">
                                        <select class="form-select mb-3 shadow-sm" id="leader_type" name="leader_type">
                                            <option value="0"'.($leader_type==0?"selected":"").'>Staff</option>
                                            <option value="1"'.($leader_type==1?"selected":"").'>Student</option>
                                            <option value="2"'.($leader_type==2?"selected":"").'>External Provider</option>
                                        </select>
                                    </div>
                                </div>';
                        }
                    ?>
                    <div class="row my-3">
                        <div class="col">
                            <input type="hidden" name="leader_id" value="<?php echo $_GET['id']; ?>">
                            <input type="submit" class="btn btn-primary shadow" name="edit-leader" value="Save Changes"></input>
                            <a href="dashboard.php" class="btn btn-secondary shadow">Return</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'includes/footer.php';?>
    </div>
</body>
</html>
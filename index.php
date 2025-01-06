<!DOCTYPE html>
<html lang="en">
<head>
    <!-- The title shown in the tab bar -->
    <title>Home</title>
    <!-- The meta tags for the site -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Alert JS -->
    <script src="js/script.js"></script>
</head>
<body>
    <?php
        // Include the session and database configuration files
        include 'includes/db_config.php';
        include 'includes/session.php';
    ?>
    <div class="container-md mt-4"> <!-- The main container -->
        <?php
            // Include the navbar
            $page_name = "Home";
            $search_bar = false;
            include 'includes/navbar.php';
        ?>
        <div class="row justify-content-center m-5">
            <div class="col-lg-8">
                <h1 class="text-start">Search:</h1> <!-- The title for the search bar -->
                <form action="search.php" method="POST" class="shadow"> <!-- The form for the search bar -->
                    <div class="input-group mb-3"> <!-- The div for the search bar -->
                        <input type="hidden" name="search_type" value="basic"> <!-- The hidden input for the search type -->
                        <input type="hidden" name="breadcrumb" value=<?php echo $_SERVER['PHP_SELF']; ?>> <!-- The hidden input for the breadcrumb -->
                        <select class="form-select form-select-lg" name="search_field" id="search_field"> <!-- The select for the search field -->
                            <!-- The options for the search field -->
                            <option value="name">Club</option>
                            <option value="leader">Leader</option>
                            <option value="meetings">Meeting</option>
                            <option value="categories">Category</option>
                        </select>
                        <input type="text" class="form-control form-control-lg w-lg-75" name="search_value" placeholder="Search" aria-label="Search" aria-describedby="button-addon1"> <!-- The searchbar -->
                        <button class="btn btn-outline-success w-20 fw-bold" type="submit" id="button-addon1">Search</button> <!-- The search button -->
                    </div>
                </form>
                <div class="w-100 text-end"> <!-- The div for the advanced search button, aligned so the button is on the right -->
                    <a href="advanced_search.php" class="btn btn-lg btn-secondary shadow">Advanced Search</a> <!-- The advanced search button -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">Browse All:</h1> <!-- The title for the browse all section -->
            </div>
                <?php
                    $sql = "SELECT * FROM clubs JOIN leaders ON clubs.leader_id = leaders.leader_id ORDER BY RAND()"; // The SQL query to get all the clubs in a random order
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) { // Continue if there are clubs
                        while ($row = $result->fetch_assoc()){
                            include 'includes/club_card.php'; // Show the club
                        }
                    }
                ?>
        </div>
        <?php include 'includes/footer.php'; ?> <!-- Include the footer -->
    </div>
    <div class="fixed-bottom"> <!-- The div for the alert, fixed to the bottom of the screen -->
        <?php include 'includes/alert.php'; ?> <!-- Include the alert if there is one -->
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title of the page -->
    <title>Search Results</title>
    <!-- Meta tags for the page -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Links to bootstrap stylesheets and scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Links to bootstrap icons stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- Links to custom CSS and JS -->
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js"></script>
    <script src="js/selectAll.js"></script>
</head>
<body>
    <?php
        // include the database connection and session variables
        include 'includes/db_config.php';
        include 'includes/session.php';
    ?>
    <div class="container-md mt-4"> <!-- The main container -->
        <?php
            // include the navbar
            $page_name = "Search Results:";
            $search_bar = true;
            include 'includes/navbar.php';
        ?>
        <div class="row"> <!-- The main row containing all of the primary content -->
            <?php
                if(isset($_POST['search_value']) && isset($_POST['search_type'])){ // if the search form has been submitted
                    $search = isset($_POST['search_value'])?$_POST['search_value']:''; // get the search value
                    // get the search results for the basic search
                    $sql = "SELECT * FROM clubs JOIN leaders ON clubs.leader_id = leaders.leader_id WHERE " . (isset($_POST['search_field'])?($_POST['search_field']=='leader'?"CONCAT(first_name,' ',last_name)":$_POST['search_field']):"name") . " LIKE '%$search%' ORDER BY name ASC";
                    $result_basic = $conn->query($sql);
                    if($_POST['search_type'] == "basic"){
                        // show the user how many results were found
                        echo '<div class="row">
                            <p class="text-secondary">Showing '.$result_basic->num_rows.' results for club '. (isset($_POST['search_field'])?$_POST['search_field']:"name") . ': "<span class="text-body fw-bold">'.$search.'</span>"</p>
                        </div>';
                    } else if ($_POST['search_type'] == "advanced"){
                        // get the filters for the advanced search
                        $categories = str_replace("'",'',isset($_POST['categories'])?$_POST['categories']:array());
                        $leaders = str_replace("'",'',isset($_POST['leaders'])?$_POST['leaders']:array());
                        $days = str_replace("'",'',isset($_POST['days'])?$_POST['days']:array());
                        // set the base SQL before adding the filters
                        $sql = "SELECT * FROM clubs JOIN leaders ON clubs.leader_id = leaders.leader_id WHERE (" . (isset($_POST['search_field'])?$_POST['search_field']:"name") . " LIKE '%$search%') AND (categories LIKE '%";
                        if (count($categories) > 0){ // if filtered by categories
                            for ($i = 0; $i < count($categories); $i++){
                                // add the categories to the SQL
                                if ($i == count($categories) - 1){
                                    $sql .= $categories[$i];
                                } else {
                                    $sql .= $categories[$i]."%' OR categories LIKE '%";
                                }
                            }
                        }
                        $sql .= "%') AND (CONCAT(first_name,' ',last_name) LIKE '%"; // close the categories and start the leaders
                        if (count($leaders) > 0){ // if filtered by leaders
                            // add the leaders to the SQL
                            for ($i = 0; $i < count($leaders); $i++){
                                if ($i == count($leaders) - 1){
                                    $sql .= $leaders[$i];
                                } else {
                                    $sql .= $leaders[$i]."' OR CONCAT(first_name,' ',last_name) = '";
                                }
                            }
                        }
                        $sql .= "%') AND (meetings LIKE '%"; // close the leaders and start the days
                        if (count($days) > 0){
                            // add the days to the SQL
                            for ($i = 0; $i < count($days); $i++){
                                if ($i == count($days) - 1){
                                    $sql .= $days[$i];
                                } else {
                                    $sql .= $days[$i]."%' OR meetings LIKE '%";
                                }
                            }
                        }
                        $sql .= "%') ORDER BY name ASC"; // close the days and finish the SQL
                        $result = $conn->query($sql); // get the results
                        // show the user how many results were found
                        echo '<div class="row">
                            <p class="text-secondary">Showing '.$result->num_rows.' results for club ' . (isset($_POST['search_field'])?$_POST['search_field']:"name") . ': "<span class="fw-bold">'.$search.'</span>"</p>
                        </div>';
                    } else {
                        // get all clubs
                        $sql = "SELECT * FROM clubs JOIN leaders ON clubs.leader_id = leaders.leader_id ORDER BY name ASC";
                        $result = $conn->query($sql);
                        $result_basic = $conn->query($sql);
                        // show the user how many results were found
                        echo '<div class="row">
                            <p class="text-secondary">Showing '.$result->num_rows.' results for: "<span class="fw-bold">All Clubs</span>"</p>
                        </div>';
                    }
                    if ($result_basic->num_rows > 0) { // if there are clubs in the database that match the search
                        // show the filter button
                        echo '<div class="row">
                                <a class="btn btn-secondary shadow" data-bs-toggle="offcanvas" href="#filter" role="button" aria-controls="filter">View Filters</a>
                            </div>';
                        // set the filter variables
                        $all_categories = array();
                        $all_leaders = array();
                        $all_days = array();
                        while ($row = $result_basic->fetch_assoc()){ // for every club that could be in the search
                            // get the categories for the club
                            $club_categories = $row['categories'];
                            // format the categories into an array of each one
                            $club_categories = trim($club_categories, "[]");
                            $club_categories = str_replace("'","",$club_categories);
                            $club_categories = explode(",", $club_categories);
                            for ($i = 0; $i < count($club_categories); $i++){
                                if (!in_array($club_categories[$i], $all_categories)){ // add the category to the array if it isn't already there
                                    array_push($all_categories, $club_categories[$i]);
                                }
                            }
                            // get the leader for the club
                            $leader_name = $row['first_name'].' '.$row['last_name'];
                            if (!in_array($leader_name,$all_leaders)){ // add the leader to the array if they aren't already there
                                array_push($all_leaders, $leader_name);
                            }
                            // get the days for the club and format them into each day
                            $all_meetings = trim($row['meetings'], "[]");
                            $all_meetings = explode("},{",$all_meetings);
                            for ($i = 0; $i < count($all_meetings); $i++){
                                if (substr($all_meetings[$i],0,1) !=  "{"){
                                    $all_meetings[$i] = '{'.$all_meetings[$i];
                                }
                                if (substr($all_meetings[$i],-1) !=  "}"){
                                    $all_meetings[$i] = $all_meetings[$i].'}';
                                }
                                $all_meetings[$i] = json_decode($all_meetings[$i],true);
                                if (!in_array($all_meetings[$i]['date'], $all_days)){ // add the day to the array if it isn't already there
                                    array_push($all_days, $all_meetings[$i]['date']);
                                }
                            }
                            if ($_POST['search_type'] == 'basic'){
                                // show the club
                                include 'includes/club_card.php';
                                // set the filter variables to include all possible options
                                $categories = $all_categories;
                                $leaders = $all_leaders;
                                $days = $all_days;
                            } else if ($_POST['search_type'] == 'advanced'){
                                if ($result->num_rows > 0){
                                    while ($row = $result->fetch_assoc()){ // loop through all of the matching clubs
                                        include 'includes/club_card.php'; // show the club
                                    }
                                } else { // if there are no results
                                    echo '<div class="row mt-3">
                                        <h5>No results found.</h5>
                                    </div>';
                                    include 'includes/footer.php'; // include the footer
                                    // show the filters so the user can update the filters
                                    echo '<div class="offcanvas offcanvas-start" tabindex="-1" id="filter" aria-labelledby="filter-title">
                                        <div class="offcanvas-header">
                                            <h3 class="offcanvas-title" id="filter-title">Filters:</h3>
                                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body">
                                            <form action="search.php" method="POST">';
                                                include 'includes/filters.php';
                                                echo '<input type="hidden" name="search_value" value="'.$_POST['search_value'].'">
                                                <input type="hidden" name="search_field" value="'.isset($_POST['search_field'])?$_POST['search_field']:'name'.'">
                                                <input type="hidden" name="search_type" value="advanced"> <!-- The search type is advanced as it includes filters -->
                                                <input type="submit" class="btn btn-success shadow" name="search" value="Apply Filters"> <!-- The submit button -->
                                            </form>
                                        </div>
                                    </div>
                                    <div class="fixed-bottom">';
                                        include 'includes/alert.php'; // include the alert
                                    echo '</div>';
                                    return;
                                }
                            } else { // if neither search type is set
                                $_SESSION['alert'] = "There was an error in the search. Try again.";
                                $_GET['alert'] = "error";
                                echo '<div class="row">
                                    <h5>No results found.</h5>
                                </div>';
                            }   
                        }
                    } else { // if there are no results that match the search
                        echo '<div class="row">
                            <h5>No results found.</h5>
                        </div>';
                    }
                } else { // if the search is not submitted
                    $_SESSION['alert'] = "There was an error in the search. Try again.";
                    $_GET['alert'] = "error";
                    echo '<div class="row">
                        <h5>No results found.</h5>
                    </div>';
                }
            ?>
        </div>
        <?php include 'includes/footer.php'; ?> <!-- include the footer with the privacy statement -->
    </div>
    <!-- The offscreen filters -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="filter" aria-labelledby="filter-title">
        <div class="offcanvas-header">
            <h3 class="offcanvas-title" id="filter-title">Filters:</h3>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="search.php" method="POST">
                <?php include 'includes/filters.php'; ?> <!-- The filter inputs -->
                <input type="hidden" name="search_value" value="<?php echo $_POST['search_value']; ?>"> <!-- The search value is the search bar value for the current search -->
                <input type="hidden" name="search_field" value="<?php echo isset($_POST['search_field'])?$_POST['search_field']:'name';?>"> <!-- The search field is the name by default but can be changed -->
                <input type="hidden" name="search_type" value="advanced"> <!-- The search type is advanced as it includes filters -->
                <input type="submit" class="btn btn-success shadow" name="search" value="Apply Filters"> <!-- The submit button -->
            </form>
        </div>
    </div>
    <!-- The alert if there is one set -->
    <div class="fixed-bottom">
        <?php include 'includes/alert.php'; ?>
    </div>
</body>
</html>
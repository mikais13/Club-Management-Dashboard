<!DOCTYPE html>
<html lang="en">
<head>
    <title>Advanced Search</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/script.js"></script>
    <script src="js/selectAll.js"></script>
</head>
<body>
    <?php
        include 'includes/db_config.php';
        include 'includes/session.php';
    ?>
    <div class="container-md mt-4">
        <?php 
            $page_name = "Advanced Search";
            $search_bar = false;
            include 'includes/navbar.php';
            $sql = 'SELECT * FROM clubs JOIN leaders ON clubs.leader_id = leaders.leader_id';
            $result = $conn->query($sql);
            $categories = array();
            $leaders = array();
            $days = array();
            if ($result->num_rows > 0){
                while ($row = $result->fetch_assoc()){
                    $club_categories = $row['categories'];
                    $club_categories = trim($club_categories, "[]");
                    $club_categories = str_replace("'","",$club_categories);
                    $club_categories = explode(",", $club_categories);
                    foreach ($club_categories as $category){
                        if (!in_array($category,$categories)){
                            array_push($categories,$category);
                        }
                    }
                    $leader_name = $row['first_name'] . " " . $row['last_name'];
                    if (!in_array($leader_name,$leaders)){
                        array_push($leaders,$leader_name);
                    }
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
                        if (!in_array($meetings[$i]['date'], $days)){
                            array_push($days, $meetings[$i]['date']);
                        }
                    }
                }
            }
            $all_categories = $categories;
            $all_leaders = $leaders;
            $all_days = $days;
        ?>
        <div class="row">
            <form action="search.php" method="POST">
                <div class="mb-3 col-lg-6">
                    <div class="row align-items-center my-3">
                        <div class="col-auto">
                            <h5 class="d-inline-block">Search:</h5>
                        </div>
                        <div class="col-auto">
                            <select class="form-select d-inline-block w-20" name="search_field" required>
                                <option value="name" selected>Club</option>
                                <option value="leader">Leader</option>
                                <option value="meetings">Meeting</option>
                                <option value="categories">Category</option>
                            </select>
                        </div>
                    </div>
                    <input type="text" class="form-control" name="search_value" placeholder="Search" aria-label="Search" aria-describedby="button-addon1">
                </div>
                <?php include 'includes/filters.php'; ?>
                <input type="hidden" name="search_type" value="advanced">
                <input type="submit" class="btn btn-success" name="search" value="Search">
            </form>
        </div>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>
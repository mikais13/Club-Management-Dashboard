<?php if(!isset($page_name)){$page_name = "";} ?>
<div class="row align-items-center">
    <div class="col col-12 col-sm-auto text-center text-sm-start">
        <?php
            if (basename($_SERVER['PHP_SELF']) != "index.php"){
                echo '<a href="index.php" class="btn btn-success shadow d-inline-block me-1"><i class="bi bi-house-door"></i></a>';
            }
            if (isset($return)){
                if ($return){
                    echo '<a href="'.$return.'" class="btn btn-success shadow d-inline-block ms-1"><i class="bi bi-arrow-return-left"></i></a>';
                }
            }
        ?>
    </div>
    <div class="col col-12 col-sm-auto text-center text-sm-start">
        <?php echo '<h1 class="d-inline-block">'.$page_name."</h1>";?>
    </div>
    <div class="col text-center text-sm-end">
        <?php
            if (basename($_SERVER['PHP_SELF']) != "dashboard.php" && basename($_SERVER['PHP_SELF']) != "set_club.php"){
                if (isset($_SESSION['username']) && isset($_SESSION['access_type'])) {
                    echo '<div class="btn-group shadow">
                            <a href="dashboard.php" class="btn btn-success shadow">Admin</a>
                            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="account.php">Edit Account Details</a></li>
                            </ul>
                        </div>
                        <form action="load.php" method="POST" class="d-inline-block">
                            <input type="submit" class="btn btn-danger shadow" name="log-out" value="Log Out">
                        </form>';
                } else {
                    echo '<a href="login.php" class="btn btn-secondary shadow">Admin</a>';
                }
            } else {
                echo '<form action="load.php" method="POST" class="d-inline-block">
                        <input type="submit" class="btn btn-danger shadow" name="log-out" value="Log Out">
                    </form>';
            }
        ?>
    </div>
</div>
<?php 
    if ($search_bar){
        echo '<div class="row justify-content-end">
                <div class="col col-sm-auto">
                    <form action="search.php" method="POST" class="shadow">
                        <div class="input-group mb-3">
                            <select class="form-select form-select-sm w-20" name="search_field" id="search_field">
                                <option value="name" selected>Club</option>
                                <option value="leader">Leader</option>
                                <option value="meetings">Meeting</option>
                                <option value="categories">Category</option>
                            </select>
                            <input type="hidden" name="search_type" value="basic">
                            <input type="hidden" name="breadcrumb" value='.$_SERVER["PHP_SELF"].'>
                            <input type="text" class="form-control" name="search_value" placeholder="Search" aria-label="Search" aria-describedby="button-addon1">
                            <button class="btn btn-success w-20 fw-bold" type="submit" id="button-addon1">Search</button>
                            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="advanced_Search.php">Advanced Search</a></li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>';
    }
?>
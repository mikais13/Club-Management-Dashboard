<?php
    echo '<div class="col-12 col-lg-6 mt-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="m-0"><a class="text-decoration-none text-reset" href="club.php?club='.$row['club_id'].'">'.$row['name'].'</a></h5>
                </div>
                <div class="card-body">
                    <p><b>Leader:</b> '.$row['first_name'].' '.$row['last_name'].'</p>';
    if (trim($row['email'])!=""){
        echo '<p><b>Contact:</b> '.$row['email'].'</p>';
    }
    echo            '<p>'.$row['description'].'</p>
                </div>
            </div>
        </div>';
?>
<?php
    if (isset($_GET['alert']) && isset($_SESSION['alert'])){
        $alert = $_GET['alert'];
        switch($alert){
            case 'success':
                $alert = 'Success:';
                break;
            case 'danger':
                $alert = 'Error:';
                break;
            case 'warning':
                $alert = 'Warning:';
                break;
            default:
                $alert = 'Alert:';
                break;
        }
        echo '<div class="alert alert-'.$_GET['alert'].' alert-dismissible fade show container-md w-auto p-3 m-5 position-fixed bottom-0 end-0 shadow" id="alert" role="alert">
                <strong class="col">'.$alert.'</strong> <div class="col">'.$_SESSION['alert'].'</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
?>
<div class="mb-3">
    <h5>Categories:</h5>
    <input type="button" class="btn btn-sm btn-secondary shadow" value="Select All" onclick="selectAll('categories')">
    <?php
        for ($i = 0; $i<count($all_categories); $i++){
            echo '<div class="form-check my-2">
                    <input type="checkbox" class="form-check-input shadow-sm" name="categories[]" value="'.$all_categories[$i].'"';
                    if ($categories != '' && count($all_categories) != count($categories)){
                        if (in_array($all_categories[$i],$categories)){
                            echo ' checked';
                        }
                    }
                    echo '>
                    <label class="form-check-label" for="categories[]">'.$all_categories[$i].'</label>
                </div>';
        }
    ?>
</div>
<div class="mb-3">
    <h5>Leaders:</h5>
    <input type="button" class="btn btn-sm btn-secondary shadow" value="Select All" onclick="selectAll('leaders')">
    <?php
        for($i = 0; $i < count($all_leaders); $i++){
            echo '<div class="form-check my-2">
                    <input type="checkbox" class="form-check-input shadow-sm" name="leaders[]" value="'.$all_leaders[$i].'"';
                    if ($leaders != '' && count($all_leaders) != count($leaders)){
                        if (in_array($all_leaders[$i],$leaders)){
                            echo ' checked';
                        }
                    }
                    echo '>
                    <label class="form-check-label" for="leaders[]">'.$all_leaders[$i].'</label>
                </div>';
        }
    ?>
</div>
<div class="mb-3">
    <h5>Days:</h5>
    <input type="button" class="btn btn-sm btn-secondary shadow" value="Select All" onclick="selectAll('days')">
    <?php
        for ($i = 0; $i < count($all_days); $i++){
            echo '<div class="form-check my-2">
                    <input type="checkbox" class="form-check-input shadow-sm" name="days[]" value="'.$all_days[$i].'"';
                    if ($days != '' && count($all_days) != count($days)){
                        if (in_array($all_days[$i],$days)){
                            echo ' checked';
                        }
                    }
                    echo '>
                    <label class="form-check-label" for="days[]">'.$all_days[$i].'</label>
                </div>';
        }
    ?>
</div>
// Select all checkboxes in a group with the name of the parameter
function selectAll(name) {
    var checkboxes = document.getElementsByName(name+'[]'); // Get all checkboxes with the name of the parameter
    var checked = false; // Assume that not all checkboxes are checked to begin with
    for (var i = 0; i < checkboxes.length; i++) { // Loop through all checkboxes
        if (checkboxes[i].checked) { // If all checkboxes are checked then we want ot uncheck them all
            checked = true;
        } else { // If a checkbox is not checked then not all checkboxes are checked and we want to check them all
            checked = false;
            break;
        }
    }
    for (var i = 0; i < checkboxes.length; i++) { // Loop through all checkboxes
        checkboxes[i].checked = !checked; // Set the checkbox to the opposite of the checked variable
    }
}
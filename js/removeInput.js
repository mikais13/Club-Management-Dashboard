// Remove a meeting time from the form, the inputted number is the number that corresponds to the meeting time to remove
function removeMeeting(number) {
    meeting = document.getElementById("add-meeting-time-" + number); // get the meeting time to remove
    extraInputs = document.getElementById('extra-meeting-times'); // get the div that contains all the meeting times that can be removed
    extraInputs.removeChild(meeting); // remove the meeting time from the div
}

// Remove a category from the form, the inputted number is the number that corresponds to the category to remove
function removeCategory(number){
    category = document.getElementById("category-input-" + number); // get the category to remove
    extraInputs = document.getElementById('extra-category-inputs'); // get the div that contains all the categories that can be removed
    extraInputs.removeChild(category); // remove the category from the div
}
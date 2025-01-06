window.onload = function(){ // When the window has loaded, run the script
    // Script for hiding the alert
    var alert = document.getElementById('alert'); // Get the alert element
    if (alert != null) { // If the alert element exists
        setTimeout(function(){ // Wait 10 seconds
            alert.classList.add('translate-animate'); // Add the translate-animate class to the alert element os it will animate off the screen
        }, 10000);
    }

    // Script for switching the change password div on and off depending if the user wants to change their password
    passwordDiv = document.getElementById('password'); // Get the change password div
    if (passwordDiv != null) { // If the change password div exists
        passwordDiv.style.display = 'none'; // Hide the change password div by default
        passwordSwitch = document.getElementById('change_password'); // Get the change password switch
        if (passwordSwitch != null) { // If the change password switch exists
            passwordSwitch.onchange = function() { // When the change password switch is changed
                if (passwordSwitch.checked) { // If the change password switch is checked, show the div
                    passwordDiv.style.display = 'block';
                } else { // if the change password switch is unchecked, hide the div
                    passwordDiv.style.display = 'none';
                }
            }
        }
    }

    // Script for adding categories to the club form
    addCategory = document.getElementById('add-category'); // Get the add category button
    if (addCategory != null) { // If the add category button exists
        addCategory.onclick = function() { // When the add category button is clicked, add a new input to the form
            extraInputs = document.getElementById('extra-category-inputs'); // Get the extra inputs div
            if (extraInputs != null) { // if there are no extra inputs// if there are extra inputs
                extraInputsDivs = extraInputs.getElementsByTagName('div'); // get all of the divs inside the extra inputs div
                extraInputsDivs = Array.from(extraInputsDivs); // convert the divs inside the extra inputs div into an array
                newNumberOfMeetings = 0; // set the default number of meetings to 0
                for (i = 0; i < extraInputsDivs.length; i++) { // loop through the divs inside the extra inputs div
                    if (extraInputsDivs[i].id.includes('category-input-')) { // if the div is a category input div
                        newNumberOfMeetings = parseInt(extraInputsDivs[i].id.replace('category-input-', '')) + 1; // get the number of the div and add 1 to it
                    }
                }
                if (newNumberOfMeetings == 0) { // if there are no extra inputs
                    newNumberOfMeetings = 2; // set the number of meetings to 1
                }
                newCategoryDiv = document.createElement('div'); // Create a new div element
                // Set the attributes of the new div element
                newCategoryDiv.setAttribute('class', 'd-inline-block mx-2');
                newCategoryDiv.setAttribute('id', 'category-input-' + newNumberOfMeetings);
                newCategoryInnerDiv = document.createElement('div'); // Create a new div element
                newCategoryInnerDiv.setAttribute('class', 'input-group border border-2 border-light-subtle rounded-3 p-1 shadow-sm mx-1'); // Set the id of the new div element
                newInput = document.createElement('input'); // Create a new input element
                // Set the attributes of the new input element
                newInput.setAttribute('name', 'club-category[]');
                newInput.setAttribute('type', 'text');
                newInput.setAttribute('class', 'form-control shadow-sm rounded-3 m-1');
                newInput.setAttribute('id', 'club-category');
                newRemoveSpan = document.createElement('span'); // Create a new span element
                // Set the attributes of the new span element
                newRemoveSpan.setAttribute('class', 'input-group-btn');
                newRemoveButton = document.createElement('button'); // Create a new button element
                // Set the attributes of the new button element
                newRemoveButton.setAttribute('type', 'button');
                newRemoveButton.setAttribute('class', 'btn btn-danger rounded-3 bi bi-dash text-light m-1');
                newRemoveButton.setAttribute('id', 'remove-category-' + newNumberOfMeetings);
                newRemoveButton.setAttribute('onclick', 'removeCategory(' + newNumberOfMeetings + ')');
                newRemoveSpan.appendChild(newRemoveButton); // Add the new button element to the new span element
                newCategoryInnerDiv.appendChild(newInput); // Add the new input element to the new inner div element
                newCategoryInnerDiv.appendChild(newRemoveSpan); // Add the new span element to the new inner div element
                newCategoryDiv.appendChild(newCategoryInnerDiv); // Add the new inner div element to the new div element
                extraInputs.appendChild(newCategoryDiv); // Add the new input div to the extra-inputs div
            }
        }
    }

    // Script for showing the form to add a new leader if the user selects the option to add a new leader in the club form
    leaderSelect = document.getElementById('club-leader'); // Get the club leader select element
    addLeader = document.getElementById('add-leader'); // Get the add leader div
    if (addLeader != null && leaderSelect != null) { // If the add leader div exists
        addLeader.style.display = 'none';
        leaderSelect.onchange = function() { // When the club leader select is changed
            if (leaderSelect.value == 0) { // If the user selects the option to add a new leader, show the add leader div
                addLeader.style.display = 'block';
                // loop through all input children of add-leader and set required attribute unless they are a contact input
                var inputs = addLeader.getElementsByTagName('input');
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].name != 'leader-contact'){
                        inputs[i].setAttribute('required', 'required');
                    }
                }
            } else {
                document.getElementById('add-leader').style.display = 'none';
                // loop through all input children of add-leader and remove required attribute
                var inputs = addLeader.getElementsByTagName('input');
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].removeAttribute('required');
                }
            }
        }
    }

    // Script for showing the form to add a new student if the user selects the option to add a new student rather than add a current student to a club
    studentSelect = document.getElementById('studentSelect');
    if (studentSelect != null) {
        newStudentDiv = document.getElementById('newStudent');
        if (newStudentDiv != null) {
            studentSelect.onchange = function() {
                if (studentSelect.value == 0) { // if a new student is selected
                    newStudentDiv.style.display = 'block'; // show the new student div
                    // loop through all input children of add-leader and set required attribute
                    var inputs = newStudentDiv.getElementsByTagName('input');
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].setAttribute('required', 'required');
                    }
                } else { // if a current student is selected
                    newStudentDiv.style.display = 'none'; // hide the new student div
                    // loop through all input children of add-leader and remove required attribute
                    var inputs = newStudentDiv.getElementsByTagName('input');
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].removeAttribute('required');
                    }
                }
            }
        }
    }

    // Script for adding meetings to the club form
    addMeeting = document.getElementById('add-meeting-time'); // Get the add meeting button
    if (addMeeting != null) { // If the add meeting button exists
        addMeeting.onclick = function() {  // When the add meeting button is clicked, add a new day, time, place and place input to the form
            var extraInputs = document.getElementById('extra-meeting-times');
            if (extraInputs != null) { // if there are no extra inputs
                var newDay = document.createElement('select'); // create a new select element for the day
                // Set the attributes of the new day select element
                newDay.setAttribute('name', 'meeting-day[]');
                newDay.setAttribute('class', 'form-select shadow-sm');
                newDay.setAttribute('id', 'meeting-day');
                days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                for (var i = 0; i < days.length; i++) { // create a new option for each day of the week
                    var option = document.createElement('option');
                    option.setAttribute('value', days[i]);
                    option.innerHTML = days[i];
                    newDay.appendChild(option); // add the day to the day select element
                }
                var newTime = document.createElement('input'); // create a new input element for the time
                // Set the attributes of the new time input element
                newTime.setAttribute('name', 'meeting-time[]');
                newTime.setAttribute('type', 'text');
                newTime.setAttribute('class', 'form-control shadow-sm');
                newTime.setAttribute('id', 'meeting-time');
                newTime.setAttribute('placeholder', 'Time');
                var newLocation = document.createElement('input'); // create a new input element for the location
                // Set the attributes of the new location input element
                newLocation.setAttribute('name', 'meeting-place[]');
                newLocation.setAttribute('type', 'text');
                newLocation.setAttribute('class', 'form-control shadow-sm');
                newLocation.setAttribute('id', 'meeting-place');
                newLocation.setAttribute('placeholder', 'Location');
                var newDetails = document.createElement('input'); // create a new input element for the details
                // Set the attributes of the new details input element
                newDetails.setAttribute('name', 'meeting-details[]');
                newDetails.setAttribute('type', 'text');
                newDetails.setAttribute('class', 'form-control shadow-sm');
                newDetails.setAttribute('id', 'meeting-details');
                newDetails.setAttribute('placeholder', 'Details');
                // create a new row to hold the day, time, location and details
                var row = document.createElement('div');
                row.setAttribute('class', 'row p-1');
                // create three columns to hold the day, time and location
                var col1 = document.createElement('div');
                col1.setAttribute('class', 'col');
                col1.appendChild(newDay);
                row.appendChild(col1);
                var col2 = document.createElement('div');
                col2.setAttribute('class', 'col');
                col2.appendChild(newTime);
                row.appendChild(col2);
                var col3 = document.createElement('div');
                col3.setAttribute('class', 'col');
                col3.appendChild(newLocation);
                row.appendChild(col3);
                // create a new row to hold the details inside the previous row below the day, time and location with a column in it
                var col4 = document.createElement('div');
                col4.setAttribute('class', 'col-12 mt-2');
                col4.appendChild(newDetails);
                row.appendChild(col4);
                var outerDiv = document.createElement('div'); // create a new div to hold the remove button and row
                // set the attributes of the outer div
                outerDiv.setAttribute('class', 'mb-3 border border-2 border-light-subtle rounded-3 p-1 shadow-sm');
                if (extraInputs.hasChildNodes) { // if there are no extra inputs
                    newNumberOfMeetings = 2;
                } else { // if there are extra inputs
                    newNumberOfMeetings = Number(extraInputs.lastChild.id.split("-").splice(-1)) + 1;
                }
                outerDiv.setAttribute('id', 'add-meeting-time-' + newNumberOfMeetings); // set the id of the div to the number of meetings
                var removeDiv = document.createElement('div'); // create a new div to hold the remove button
                removeDiv.setAttribute('class', 'position-relative');
                var removeButton = document.createElement('button'); // create a new button to remove the meeting
                // set the attributes of the remove button
                removeButton.setAttribute('class', 'position-absolute top-0 start-100 translate-middle border border-light bg-danger rounded-circle bi bi-dash text-light');
                removeButton.setAttribute('type', 'button');
                removeButton.setAttribute('id', 'remove-meeting-time-' + newNumberOfMeetings); // set the id of the button to the number of meetings
                removeButton.setAttribute('onclick', 'removeMeeting(' + newNumberOfMeetings + ')'); // set the onclick attribute of the button to the removeMeetingTime function with the number of meetings as the parameter
                removeDiv.appendChild(removeButton); // add the remove button to the remove div
                outerDiv.appendChild(removeDiv); // add the remove div to the outer div
                outerDiv.appendChild(row); // add the bigger row to the outer div
                extraInputs.appendChild(outerDiv); // add the bigger row to the extra inputs div
            }
        }
    }
}
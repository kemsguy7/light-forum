<?php
session_start();

function sanitizeInput($input) {
    $input .= trim($input);
    $input .= stripslashes($input);
    $input .= htmlspecialchars($input);
    return $input;
}

//check if user is logged in 
function loggedIn(){
    if (isset($_SESSION['user_id']) & isset($_SESSION['username'])){
        return true;
    }
}
loggedIn();
echo "<pre>";
print_r(loggedIn());
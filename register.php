<?php
session_start();

// init variables to store error and success messages
$error = "";
$success = "";

//check for form submission through POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get form data and remove whitespace with trim()
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // VALIDATION SECTION
    // edge case for empty fields
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } 
    // verify username length, needs to be at least three chars
    elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long";
    } 
    // verify password length, needs to be at least six chars
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } 
    // verify the password fields for accuracy
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } 
    else {
        // USERNAME DUPLICATE CHECK
        // define users file where data is stored
        $users_file = 'users.txt';
        $username_exists = false;
        
        // edge case to check for file existence
        if (file_exists($users_file)) {
            // read all file lines, FILE_IGNORE_NEW_LINES removes \n from each line
            $users = file($users_file, FILE_IGNORE_NEW_LINES);
            
            // loop through users in file
            foreach ($users as $user) {
                // split each line by ':' to separate username from password
                // list() assigns values to variables from an array
                list($stored_username, ) = explode(':', $user);
                
                // case sensitive check for if username exists
                if ($username === $stored_username) {
                    $username_exists = true;
                    break; // break search once found
                }
            }
        }
        
        if ($username_exists) {
            $error = "Username already exists";
        } else {
            // SAVE NEW USER
            // hash the password for security
            // PASSWORD_DEFAULT uses bcrypt algorithm
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // format: username:hashed_password followed by new line
            $user_data = $username . ':' . $hashed_password . PHP_EOL;
            
            // write to file with append mode and exclusive lock
            // FILE_APPEND adds to end of file instead of overwriting
            // LOCK_EX prevents other processes from writing simultaneously
            if (file_put_contents($users_file, $user_data, FILE_APPEND | LOCK_EX)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Failed to register. Please try again.";
            }
        }
    }
}
?>

<!--Register HTML-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>This Is Jeopardy! - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-page">
    <!-- main container for registration form -->
    <div class="login-container">
        <!-- page title with glowing animation effect -->
        <h1 class="game-title login-title">Register for JEOPARDY!</h1>

        <!-- registration form that submits to itself -->
        <form class="login-form" method="POST" action="register.php">
            <!-- edge case - validation failure error message display -->
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- display success message after successful registration -->
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- username input field -->
            <div class="form-group">
                <label for="username">Username:</label>
                <!-- keep username val after failed submission -->
                <!-- htmlspecialchars() prevents XSS attacks by escaping special characters -->
                <input type="text" id="username" name="username" required autofocus 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <!-- password input field -->
            <div class="form-group">
                <label for="password">Password:</label>
                <!-- password fields are never pre-filled for security -->
                <input type="password" id="password" name="password" required>
            </div>

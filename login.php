<?php
session_start();

// if true login, redirect to game, else throw error
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$error = "";

// login form submission handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    //validation edge case
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
	} else
		// if edge case passes, read users from file
		$users_file = 'users.txt';
        $valid_login = false;

        if (file_exists($users_file)) {
            $users = file($users_file, FILE_IGNORE_NEW_LINES);
            foreach ($users as $user) {
                list($stored_username, $stored_password) = explode(':', $user);
                if ($username === $stored_username && password_verify($password, $stored_password)) {
                    $valid_login = true;
                    break;
                }
            }
        }
        if ($valid_login) {
            $_SESSION['username'] = $username;
            
            // set remember me cookie if checked
            if (isset($_POST['remember'])) {
                setcookie('jeopardy_user', $username, time() + (86400 * 30), "/");
            }
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}

// perform check for remember me cookie
if (isset($_COOKIE['jeopardy_user']) && !isset($_SESSION['username'])) {
    $cookie_username = $_COOKIE['jeopardy_user'];
    // proceed to auto-fill username
}
?>



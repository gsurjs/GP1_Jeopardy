// index php for Group Project 1 'Jeopardy'

<?php
session_start();

// edge case check for user login
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

// init game data if !exist
if (!isset($_SESSION['scores'])) {
	$_SESSION['scores'] = array();
}

// add curr user to scores if !exist
if (!isset($_SESSION['scores'][$_SESSION['username']])) {
	$_SESSION['scores'][$_SESSION['username']] = 0;
}

// init answered questions
if (!isset($_SESSION['answered'])) {
	$_SESSION['answered'] = array();
}



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

// categories and questions
$categories = array (
	"WORLD CAPITALS" => array (
		200 => array("q" => "This French capital city is home to the Eiffel Tower and the Louvre Museum." "a" => "What is Paris?"),
		400 => array("q" => "Known for its canals and gondolas, this Italian city serves as the capital of the Veneto region." "a" => "What is Venice?"),
		600 => array("q" => "This capital of Thailand was formerly known as Siam and is famous for its ornate temples." "a" => "What is Bangkok?"),
		800 => array("q" => "Sitting at 11,942 feet above sea level, this Bolivian capital is one of the world's highest." "a" => "What is La Paz?"),
		1000 => array("q" => "This capital of Bhutan, whose name means \"rice valley,\" is the only capital without traffic lights." "a" => "What is Thimphu?"),
	),
	"SCIENCE & NATURE" => array (
		200 => array("q" => "This organ in the human body pumps blood throughout the circulatory system." "a" => "What is the heart?"),
		400 => array("q" => "H₂O is the chemical formula for this common substance." "a" => "What is water?"),
		600 => array("q" => "This process by which plants convert sunlight into energy produces oxygen as a byproduct." "a" => "What is photosynthesis?"),
		800 => array("q" => "These subatomic particles with no electric charge were discovered by James Chadwick in 1932." "a" => "What are neutrons?"),
		1000 => array("q" => "This quantum mechanical principle states that you cannot simultaneously know both the exact position and momentum of a particle." "a" => "What is the Heisenberg Uncertainty Principle?"),
	),
	


	
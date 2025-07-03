// index php for Group Project 1 'Jeopardy'

<?php
session_start();

// edge case check for user login
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

// init game data if !exist, stores scores in array if this is the first time playing
if (!isset($_SESSION['scores'])) {
	$_SESSION['scores'] = array();
}

// add curr user to scores if !exist, adds users curr score if first time
if (!isset($_SESSION['scores'][$_SESSION['username']])) {
	$_SESSION['scores'][$_SESSION['username']] = 0;
}

// init answered questions, prevent repeated answers
if (!isset($_SESSION['answered'])) {
	$_SESSION['answered'] = array();
}

// init curr player turn -for multiplayer
if (!isset($_SESSION['current_player'])) {
    $_SESSION['current_player'] = 0;
}

if (!isset($_SESSION['players'])) {
    $_SESSION['players'] = array($_SESSION['username']);
}

// categories and questions, structure: category >> val >> question & answer
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
	"LITERATURE" => array (
		200 => array("q" => "This Dr. Seuss character \"stole Christmas\" in a beloved holiday tale." "a" => "Who is the Grinch?"),
		400 => array("q" => "\"It was the best of times, it was the worst of times\" opens this Charles Dickens novel." "a" => "What is A Tale of Two Cities?"),
		600 => array("q" => "This American author wrote \"The Great Gatsby\" and \"Tender Is the Night.\"" "a" => "Who is F. Scott Fitzgerald?"),
		800 => array("q" => "In this Shakespearean tragedy, the title character delivers the \"To be or not to be\" soliloquy." "a" => "What is Hamlet?"),
		1000 => array("q" => "This 1922 modernist novel by James Joyce takes place in Dublin over the course of a single day." "a" => "What is Ulysses?"),
	),
	"POP CULTURE" => array (
		200 => array("q" => "This animated movie franchise features a cowboy named Woody and a space ranger named Buzz." "a" => "What is Toy Story?"),
		400 => array("q" => "This British band sang \"Bohemian Rhapsody\" and \"We Will Rock You.\"" "a" => "Who is Queen?"),
		600 => array("q" => "This streaming series set in Hawkins, Indiana features a parallel dimension called the Upside Down." "a" => "What is Stranger Things?"),
		800 => array("q" => "This South Korean film became the first non-English language film to win Best Picture at the Oscars in 2020." "a" => "What is Parasite?"),
		1000 => array("q" => "This video game, released in 1980, was the first to feature cutscenes and is considered the first mascot character in gaming." "a" => "What is Pac-Man?"),
	),
	"HISTORY" => array (
		200 => array("q" => "This American president appears on the penny and the five-dollar bill." "a" => "Who is Abraham Lincoln?"),
		400 => array("q" => "This year marks the beginning of World War II with Germany's invasion of Poland." "a" => "What is 1939?"),
		600 => array("q" => "This ancient wonder of the world, built around 2560 BCE, is the only one still largely intact." "a" => "What is the Great Pyramid of Giza?"),
		800 => array("q" => "This treaty, signed in 1919, officially ended World War I and imposed harsh penalties on Germany." "a" => "What is the Treaty of Versailles?"),
		1000 => array("q" => "This Byzantine emperor, ruling from 527-565 CE, attempted to reconquer the Western Roman Empire and codified Roman law." "a" => "Who is Justinian I?")
	)
);

// handle answer submission from question page, processes POST requests when user submits an aswer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
    $category = $_POST['category'];
    $value = $_POST['value'];
    $userAnswer = trim($_POST['answer']);
    $correctAnswer = $categories[$category][$value]['a'];

    // unique id for questions, format ex: "HISTORY_200" used for tracking answered questions
    $questionKey = $category . "_" . $value;
    $_SESSION['answered'][] = $questionKey;
    
    // verify if answer is correct, strcasecmp returns 0 if string is equal
    if (strcasecmp($userAnswer, $correctAnswer) == 0) {
        $_SESSION['scores'][$_SESSION['username']] += $value;
        $_SESSION['last_result'] = "Correct! You earned $" . $value;
        $_SESSION['result_class'] = "correct";
    } else {
    	// wrong answer subtracts points based on val
        $_SESSION['scores'][$_SESSION['username']] -= $value;
        $_SESSION['last_result'] = "Sorry, the correct answer was: " . $correctAnswer;
        $_SESSION['result_class'] = "incorrect";
    }
    
    // redirect to prevent form resubmission when page is refreshed
    header("Location: index.php");
    exit();
}

// reset game
if (isset($_GET['reset'])) {
    $_SESSION['answered'] = array();
    $_SESSION['scores'][$_SESSION['username']] = 0;
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>This Is Jeopardy!</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1 class="game-title">Jeopardy!</h1>
        <!-- user info display bar, shows curr player and associated score -->
        <div class="user-info">
        	<!-- using htmlspecialchars to prevent XSS attacks -->
            <p>Playing as: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
            <p>Score: <strong>$<?php echo $_SESSION['scores'][$_SESSION['username']]; ?></strong></p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- result message display area -->
        <!-- shows feedback after answering a question -->
        <?php if (isset($_SESSION['last_result'])): ?>
            <div class="result-message <?php echo $_SESSION['result_class']; ?>">
                <?php 
                echo htmlspecialchars($_SESSION['last_result']);
                unset($_SESSION['last_result']);
                unset($_SESSION['result_class']);
                ?>
            </div>
        <?php endif; ?>

        <!-- main Jeopardy game board -->
        <div class="game-board">
            <!-- category row - displays the 5 categories -->
            <div class="categories">
                <?php foreach ($categories as $category => $questions): ?>
                    <div class="category"><?php echo $category; ?></div>
                <?php endforeach; ?>
            </div>

            <!-- question vals -->
            <?php 
            $values = array(200, 400, 600, 800, 1000);
            foreach ($values as $value): 
            ?>
                <div class="question-row">
                    <?php foreach ($categories as $category => $questions): ?>
                        <?php 
                        $questionKey = $category . "_" . $value;
                        $isAnswered = in_array($questionKey, $_SESSION['answered']);
                        ?>
                        <?php if (!$isAnswered): ?>
                            <div class="question-tile">
                                <form method="GET" action="question.php">
                                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                                    <input type="hidden" name="value" value="<?php echo $value; ?>">
                                    <button type="submit" class="question-button">$<?php echo $value; ?></button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="question-tile answered">
                                <span class="answered-text">---</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
</body>
<?php
session_start();

//start sesh, gather user data and game state

//edge case security check, make sure user is logged in, if not, redirect to login
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

//validation check for required params, needs category and val to display question
if (!isset($_GET['category']) || !isset($_GET['value'])) {
    header("Location: index.php");
    exit();
}

//get question params from url
$category = $_GET['category'];
$value = intval($_GET['value']); //convert to int for security

//game data section, define all categories and questions
//acts as database and matches data in index.php
$categories = array(
    "WORLD CAPITALS" => array(
        200 => array("q" => "This French capital city is home to the Eiffel Tower and the Louvre Museum.", "a" => "What is Paris?"),
        400 => array("q" => "Known for its canals and gondolas, this Italian city serves as the capital of the Veneto region.", "a" => "What is Venice?"),
        600 => array("q" => "This capital of Thailand was formerly known as Siam and is famous for its ornate temples.", "a" => "What is Bangkok?"),
        800 => array("q" => "Sitting at 11,942 feet above sea level, this Bolivian capital is one of the world's highest.", "a" => "What is La Paz?"),
        1000 => array("q" => "This capital of Bhutan, whose name means 'rice valley,' is the only capital without traffic lights.", "a" => "What is Thimphu?")
    ),
    "SCIENCE & NATURE" => array(
        200 => array("q" => "This organ in the human body pumps blood throughout the circulatory system.", "a" => "What is the heart?"),
        400 => array("q" => "H₂O is the chemical formula for this common substance.", "a" => "What is water?"),
        600 => array("q" => "This process by which plants convert sunlight into energy produces oxygen as a byproduct.", "a" => "What is photosynthesis?"),
        800 => array("q" => "These subatomic particles with no electric charge were discovered by James Chadwick in 1932.", "a" => "What are neutrons?"),
        1000 => array("q" => "This quantum mechanical principle states that you cannot simultaneously know both the exact position and momentum of a particle.", "a" => "What is the Heisenberg Uncertainty Principle?")
    ),
    "LITERATURE" => array(
        200 => array("q" => "This Dr. Seuss character 'stole Christmas' in a beloved holiday tale.", "a" => "Who is the Grinch?"),
        400 => array("q" => "'It was the best of times, it was the worst of times' opens this Charles Dickens novel.", "a" => "What is A Tale of Two Cities?"),
        600 => array("q" => "This American author wrote 'The Great Gatsby' and 'Tender Is the Night.'", "a" => "Who is F. Scott Fitzgerald?"),
        800 => array("q" => "In this Shakespearean tragedy, the title character delivers the 'To be or not to be' soliloquy.", "a" => "What is Hamlet?"),
        1000 => array("q" => "This 1922 modernist novel by James Joyce takes place in Dublin over the course of a single day.", "a" => "What is Ulysses?")
    ),
    "POP CULTURE" => array(
        200 => array("q" => "This animated movie franchise features a cowboy named Woody and a space ranger named Buzz.", "a" => "What is Toy Story?"),
        400 => array("q" => "This British band sang 'Bohemian Rhapsody' and 'We Will Rock You.'", "a" => "Who is Queen?"),
        600 => array("q" => "This streaming series set in Hawkins, Indiana features a parallel dimension called the Upside Down.", "a" => "What is Stranger Things?"),
        800 => array("q" => "This South Korean film became the first non-English language film to win Best Picture at the Oscars in 2020.", "a" => "What is Parasite?"),
        1000 => array("q" => "This video game, released in 1980, was the first to feature cutscenes and is considered the first mascot character in gaming.", "a" => "What is Pac-Man?")
    ),
    "HISTORY" => array(
        200 => array("q" => "This American president appears on the penny and the five-dollar bill.", "a" => "Who is Abraham Lincoln?"),
        400 => array("q" => "This year marks the beginning of World War II with Germany's invasion of Poland.", "a" => "What is 1939?"),
        600 => array("q" => "This ancient wonder of the world, built around 2560 BCE, is the only one still largely intact.", "a" => "What is the Great Pyramid of Giza?"),
        800 => array("q" => "This treaty, signed in 1919, officially ended World War I and imposed harsh penalties on Germany.", "a" => "What is the Treaty of Versailles?"),
        1000 => array("q" => "This Byzantine emperor, ruling from 527-565 CE, attempted to reconquer the Western Roman Empire and codified Roman law.", "a" => "Who is Justinian I?")
    )
);

//validation check for requested question existence
if (!isset($categories[$category][$value])) {
    header("Location: index.php");
    exit();
}

//check for existing answer, create unique ID for question
$questionKey = $category . "_" . $value;
// ?? [] provides empty arr as default if answered array doesn't exist
if (in_array($questionKey, $_SESSION['answered'] ?? [])) {
    header("Location: index.php");
    exit();
}

// gather question text for display
$question = $categories[$category][$value]['q'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy - Question</title>
    <link rel="stylesheet" href="styles.css">
</head>
<!-- body class for question page styling -->
<body class="question-page">
    <!-- Full-screen overlay creates modal effect -->
    <div class="question-modal-overlay">
        <!-- modal container for the question -->
        <div class="question-modal">
            <!-- header shows category and dollar value -->
            <div class="question-header">
                <!-- htmlspecialchars prevents XSS attacks by escaping HTML -->
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <h3>$<?php echo $value; ?></h3>
            </div>

            <!-- main question content -->
            <div class="question-content">
                <!-- display the question text -->
                <p class="question-text"><?php echo htmlspecialchars($question); ?></p>

                <!-- answer submission form -->
                <!-- submits to index.php for answer processing -->
                <form method="POST" action="index.php" class="answer-form">
                    <!-- hidden fields pass category and value to index.php -->
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                    <input type="hidden" name="value" value="<?php echo $value; ?>">

                    <!-- answer input field -->
                    <div class="form-group">
                        <label for="answer">Your Answer:</label>
                        <!-- placeholder reminds player of Jeopardy format, autofocus adjusts cursor to field automatically -->
                        <input type="text" id="answer" name="answer" placeholder="Remember to phrase as a question..." required autofocus>
                    </div>

                    <!-- action buttons -->
                    <div class="button-group">
                    	<!-- submit button to post form -->
                    	<button type="submit" class="submit-btn">Submit Answer</button>
                    	<!-- cancel link returns to game board without submitting answer -->
                    	<a href="index.php" class="cancel-btn">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>



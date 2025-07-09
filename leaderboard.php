<?php
session_start();

// Edge case security check: ensure a user is logged in.
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

// Retrieve the scores array from the session.
$scores = isset($_SESSION['scores']) ? $_SESSION['scores'] : [];

// Sort the scores in descending order (highest first).
// arsort() maintains the key-value association, which is perfect for this.
if (!empty($scores)) {
    arsort($scores);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy! - Leaderboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="leaderboard-page">
    <div class="container">
        <h1 class="game-title">Leaderboard</h1>

        <div class="leaderboard-container">
            <?php if (!empty($scores)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($scores as $username => $score): 
                        ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo htmlspecialchars($username); ?></td>
                                <td>$<?php echo $score; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-scores">No scores recorded yet. Play a game to see the leaderboard!</p>
            <?php endif; ?>
        </div>

        <footer>
            <div class="game-options">
                <a href="index.php" class="option-btn">Back to Game</a>
            </div>
        </footer>
    </div>
</body>
</html>

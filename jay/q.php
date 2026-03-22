<?php
// 1. Simulan ang session para ma-save ang score kahit mag-refresh ang page
session_start(); 

// 2. Kumonekta sa database (Host, Username, Password, Database Name)
$conn = new mysqli("localhost", "root", "", "quiz_db");

// I-check kung nagana ang koneksyon, kung hindi, itigil ang program
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. RESTART LOGIC: Kapag pinindot ang restart button (?restart=1 sa URL)
if (isset($_GET['restart'])) {
    session_destroy();         // Burahin ang lahat ng na-save na score sa memory
    header("Location: q.php"); // I-refresh ang page para bumalik sa simula
    exit();                    // Itigil na ang pagbasa ng code sa baba
}

// 4. SCORE INITIALIZATION: Kung bagong laro pa lang, gawing 0 ang score
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}

// 5. GET RANDOM QUESTION: Kumuha ng isang (1) random na tanong sa database
$sql = "SELECT * FROM questions ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);
$question = $result->fetch_assoc(); // Ilagay ang data ng tanong sa variable na $question

// 6. CHECK ANSWER LOGIC: Kapag may pinindot na option ang user
$status = "Pumili ng tamang sagot!";
if (isset($_GET['choice'])) {
    // I-compare kung ang 'choice' (pinindot) ay pareho sa 'correct' (tamang sagot)
    if ($_GET['choice'] === $_GET['correct']) {
        $_SESSION['score'] += 10; // Dagdagan ng 10 ang score sa session memory
        $status = "<b style='color:green;'>TAMA! +10 Points</b>";
    } else {
        $status = "<b style='color:red;'>MALI! Subukan ulit.</b>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Easy Quiz</title>
    <style>
        /* Simpleng design para maging malinis ang itsura */
        body { font-family: sans-serif; text-align: center; background: #f4f4f4; padding-top: 50px; }
        .card { width: 350px; background: white; border: 3px solid #000; margin: 0 auto; padding: 20px; border-radius: 10px; box-shadow: 8px 8px 0px #000; }
        
        /* Box para sa image gaya ng drawing mo */
        .img-box { width: 100%; height: 180px; border: 2px solid #000; margin: 15px 0; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .img-box img { max-width: 100%; max-height: 100%; }
        
        /* 2x2 Grid para sa apat na options */
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        
        /* Style para sa mga buttons/links */
        .btn { padding: 15px; border: 2px solid #000; text-decoration: none; color: black; font-weight: bold; background: #fff; font-size: 14px; }
        .btn:hover { background: #000; color: #fff; }

        /* Style para sa restart button */
        .restart-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #ff4d4d; color: white; text-decoration: none; font-weight: bold; border: 2px solid #000; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Score: <?php echo $_SESSION['score']; ?></h2>

        <p><b><?php echo $question['question_text']; ?></b></p>

        <div class="img-box">
            <img src="<?php echo $question['image_url']; ?>" alt="Quiz Image">
        </div>

        <div class="grid">
            <?php 
            $correct = $question['correct_answer']; // Itago ang tamang sagot para madaling i-pasa
            
            // Loop o isa-isang paggawa ng buttons para sa bawat option
            echo "<a href='?choice={$question['option1']}&correct=$correct' class='btn'>{$question['option1']}</a>";
            echo "<a href='?choice={$question['option2']}&correct=$correct' class='btn'>{$question['option2']}</a>";
            echo "<a href='?choice={$question['option3']}&correct=$correct' class='btn'>{$question['option3']}</a>";
            echo "<a href='?choice={$question['option4']}&correct=$correct' class='btn'>{$question['option4']}</a>";
            ?>
        </div>

        <p class="status-msg"><?php echo $status; ?></p>
    </div>

    <a href="?restart=1" class="restart-btn">RESTART GAME</a>

</body>
</html>
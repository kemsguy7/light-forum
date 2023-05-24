<?php
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $topicId = $_POST['topic_id'];
    $parentId = $_POST['parent_id'];
    $replyText = sanitizeInput($conn, $_POST['reply_text']);
    $userId = $_SESSION['user_id'];

    // Insert the reply into the database
    $sql = "INSERT INTO replies (user_id, topic_id, parent_id, reply_text, created_at)
            VALUES ('$userId', '$topicId', '$parentId', '$replyText', NOW())";

    if (mysqli_query($conn, $sql)) {
        // Reply added successfully
        header("Location: index.php");
        exit();
    } else {
        echo "Error adding reply: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

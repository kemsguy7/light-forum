<?php
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $topicId = $_POST['topic_id'];
    $commentText = sanitizeInput($conn, $_POST['comment_text']);
    $userId = $_SESSION['user_id'];

    // Insert the comment into the database
    $sql = "INSERT INTO comments (user_id, topic_id, comment_text, created_at)
            VALUES ('$userId', '$topicId', '$commentText', NOW())";

    if (mysqli_query($conn, $sql)) {
        // Comment added successfully
        header("Location: index.php");
        exit();
    } else {
        echo "Error adding comment: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

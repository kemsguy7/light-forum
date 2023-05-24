<?php

include('includes/db.php');


// Retrieve the topics from the database
$sql = "SELECT * FROM topics ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Display the topics
    while ($row = mysqli_fetch_assoc($result)) {
        echo '
        <div class="card">
            <h2>'.$row['title'].'</h2>
            <p>'.$row['description'].'</p>
            <p>Created at: '.$row['created_at'].'</p>';

        if (isset($_SESSION['user_id'])) {
            // Display the comment form
            echo '
            <div class="comment-form">
                <h3>Add Comment</h3>
                <form action="add_comment.php" method="POST">
                    <input type="hidden" name="topic_id" value="'.$row['id'].'">
                    <textarea name="comment_text" required></textarea>
                    <input type="submit" value="Post Comment">
                </form>
            </div>';
        }

        // Retrieve the comments for the topic
        $topicId = $row['id'];
        $commentsSql = "SELECT * FROM comments WHERE topic_id = '$topicId' ORDER BY created_at DESC";
        $commentsResult = mysqli_query($conn, $commentsSql);

        if (mysqli_num_rows($commentsResult) > 0) {
            // Display the comments
            echo '
            <div class="comments-section">
                <h3>Comments</h3>';
            
            while ($commentRow = mysqli_fetch_assoc($commentsResult)) {
                echo '
                <div class="comment">
                    <p>'.$commentRow['comment_text'].'</p>
                    <p>Posted at: '.$commentRow['created_at'].'</p>';

                if (isset($_SESSION['user_id'])) {
                    // Display the reply form
                    echo '
                    <div class="reply-section">
                        <h4>Add Reply</h4>
                        <form action="add_reply.php" method="POST">
                            <input type="hidden" name="topic_id" value="'.$row['id'].'">
                            <input type="hidden" name="parent_id" value="'.$commentRow['id'].'">
                            <textarea name="reply_text" required></textarea>
                            <input type="submit" value="Post Reply">
                        </form>
                    </div>';
                }

                // Retrieve the replies for the comment
                $commentId = $commentRow['id'];
                $repliesSql = "SELECT * FROM replies WHERE parent_id = '$commentId' ORDER BY created_at DESC";
                $repliesResult = mysqli_query($conn, $repliesSql);

                if (mysqli_num_rows($repliesResult) > 0) {
                    // Display the replies
                    echo '
                    <div class="replies-section">
                        <h4>Replies</h4>';
                    
                    while ($replyRow = mysqli_fetch_assoc($repliesResult)) {
                        echo '
                        <div class="reply">
                            <p>'.$replyRow['reply_text'].'</p>
                            <p>Posted at: '.$replyRow['created_at'].'</p>
                        </div>';
                    }

                    echo '</div>';
                }

                echo '</div>';
            }

            echo '</div>';
        }

        echo '</div>';
    }
} else {
    echo "No topics found";
}

mysqli_close($conn);
?>

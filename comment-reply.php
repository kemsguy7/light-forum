<!DOCTYPE html>
<html>
<head>
    <title>Basic Forum</title>
    <style>
        /* CSS styles for the comment and reply system */
        .container {
            background-color: #f7f7f7;
            padding: 20px;
        }

        .comment {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 10px;
        }

        .comment-text {
            margin: 0;
        }

        .comment-author {
            color: #888888;
            font-size: 14px;
            margin: 5px 0;
        }

        .comment-date {
            color: #888888;
            font-size: 12px;
            margin: 5px 0;
        }

        .reply-form {
            margin-top: 10px;
        }

        .reply-textarea {
            width: 100%;
            padding: 5px;
            margin-bottom: 5px;
        }

        .reply-button {
            background-color: #4CAF50;
            color: #ffffff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .success-message {
            color: #008000;
        }

        .error-message {
            color: #ff0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        include('includes/db.php');
        include('includes/functions.php');
    

        // Check if user is logged in
        $loggedIn = false;
        if (isset($_SESSION['username'])) {
            $loggedIn = true;
            $username = $_SESSION['username'];
        }

        // Create a new topic
        if (isset($_POST['create_topic'])) {
            $topicTitle = $_POST['topic_title'];
            $topicDescription = $_POST['topic_description'];

            $sql = "INSERT INTO topics (title, description) VALUES ('$topicTitle', '$topicDescription')";
            if (mysqli_query($conn, $sql)) {
                echo '<p class="success-message">Topic created successfully.</p>';
            } else {
                echo '<p class="error-message">Error creating topic: ' . mysqli_error($conn) . '</p>';
            }
        }

        // Handle adding a comment
        if (isset($_POST['comment_text'])) {
            $topicId = $_POST['topic_id'];
            $commentText = $_POST['comment_text'];

            $sql = "INSERT INTO comments (topic_id, parent_id, comment_text, author, created_at)
                    VALUES ($topicId, 0, '$commentText', '$username', NOW())";

            if (mysqli_query($conn, $sql)) {
                echo '<p class="success-message">Comment added successfully.</p>';
            } else {
                echo '<p class="error-message">Error adding comment: ' . mysqli_error($conn) . '</p>';
            }
        }

        // Handle adding a reply
        if (isset($_POST['reply_text'])) {
            $topicId = $_POST['topic_id'];
            $parentId = $_POST['parent_id'];
            $replyText = $_POST['reply_text'];

            $sql = "INSERT INTO comments (topic_id, parent_id, comment_text, author, created_at)
                    VALUES ($topicId, $parentId, '$replyText', '$username', NOW())";

            if (mysqli_query($conn, $sql)) {
                echo '<p class="success-message">Reply added successfully.</p>';
            } else {
                echo '<p class="error-message">Error adding reply: ' . mysqli_error($conn) . '</p>';
            }
        }

        // Display the list of topics
        $topicsQuery = "SELECT * FROM topics";
        $topicsResult = mysqli_query($conn, $topicsQuery);

        echo '<h2>Topics</h2>';
        echo '<ul>';
        while ($row = mysqli_fetch_assoc($topicsResult)) {
            echo '<li><a href="index.php?topic_id=' . $row['id'] . '">' . $row['title'] . '</a></li>';
        }
        echo '</ul>';

        // Display the selected topic, comments, and reply forms
        if (isset($_GET['topic_id'])) {
            $topicId = $_GET['topic_id'];

            $topicQuery = "SELECT * FROM topics WHERE id = $topicId";
            $topicResult = mysqli_query($conn, $topicQuery);

            if (mysqli_num_rows($topicResult) > 0) {
                $topicRow = mysqli_fetch_assoc($topicResult);
                $topicTitle = $topicRow['title'];
                $topicDescription = $topicRow['description'];

                echo '<h2>' . $topicTitle . '</h2>';
                echo '<p>' . $topicDescription . '</p>';

                // Display the comments for the selected topic
                function displayComments($conn, $topicId, $parentId = 0) {
                    $sql = "SELECT * FROM comments WHERE topic_id = $topicId AND parent_id = $parentId ORDER BY created_at ASC";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        echo '<ul class="comments-list">';

                        while ($row = mysqli_fetch_assoc($result)) {
                            $commentId = $row['id'];

                            echo '<li class="comment">';
                            echo '<p class="comment-text">'.$row['comment_text'].'</p>';
                            echo '<p class="comment-author">'.$row['author'].'</p>';
                            echo '<p class="comment-date">'.$row['created_at'].'</p>';

                            // Display the reply form for the current comment
                            if ($loggedIn) {
                                echo '<form class="reply-form" method="post" action="index.php?topic_id='.$topicId.'">';
                                echo '<input type="hidden" name="topic_id" value="'.$topicId.'">';
                                echo '<input type="hidden" name="parent_id" value="'.$commentId.'">';
                                echo '<textarea class="reply-textarea" name="reply_text" placeholder="Reply to this comment"></textarea>';
                                echo '<button type="submit" class="reply-button">Reply</button>';
                                echo '</form>';
                            }

                            // Recursive call to display nested replies
                            displayComments($conn, $topicId, $commentId);

                            echo '</li>';
                        }

                        echo '</ul>';
                    }
                }

                displayComments($conn, $topicId);

                // Display the comment form
                if ($loggedIn) {
                    echo '<form class="comment-form" method="post" action="index.php?topic_id='.$topicId.'">';
                    echo '<input type="hidden" name="topic_id" value="'.$topicId.'">';
                    echo '<textarea class="comment-textarea" name="comment_text" placeholder="Add a comment"></textarea>';
                    echo '<button type="submit" class="comment-button">Post Comment</button>';
                    echo '</form>';
                } else {
                    echo '<p>Please <a href="login.php">login</a> to post comments.</p>';
                }
            } else {
                echo '<p>Invalid topic ID.</p>';
            }
        }

        // Display the topic creation form
        if ($loggedIn) {
            echo '<h2>Create a Topic</h2>';
            echo '<form class="topic-form" method="post" action="index.php">';
            echo '<input type="text" name="topic_title" placeholder="Topic Title" required><br>';
            echo '<textarea name="topic_description" placeholder="Topic Description" required></textarea><br>';
            echo '<button type="submit" name="create_topic">Create Topic</button>';
            echo '</form>';
        }

        mysqli_close($conn);
        ?>

    </div>
</body>
</html>

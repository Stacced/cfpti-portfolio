<?php
require_once('functions.php');
define('MAX_FILESIZE_INDIVIDUAL', 3145728);
$postOk = true;
$postAlert = '';

// Filter inputs
$comment = filter_input(INPUT_POST, 'postComment', FILTER_SANITIZE_STRING);
$submitted = filter_input(INPUT_POST, 'postSubmit', FILTER_SANITIZE_STRING);

// Get sent files count and remove empty entries
$picturesCount = count(array_filter($_FILES['postPictures']['name']));
$pictures = $_FILES['postPictures'];

// Get PDO
$pdo = getPDO();

// Check if user actually submitted POST data and not accidentally entered the URL
if ($submitted) {
    // Check if PDO is correctly setup'd
    if ($pdo) {
        // Insert post in DB
        $prepQuery = $pdo->prepare('INSERT INTO posts (comment) VALUES (:comment)');
        $prepQuery->bindParam(':comment', $comment);

        // Check if query execution is OK and files have been passed
        if ($prepQuery->execute()) {
            // Check if user submitted pictures
            if ($picturesCount > 0) {
                // Get post ID
                $postId = $pdo->lastInsertId();

                $target_dir = 'uploads/';
                for ($i = 0; $i < $picturesCount; $i++) {
                    // Check individual file size
                    if ($pictures['size'][$i] <= MAX_FILESIZE_INDIVIDUAL) {
                        $filename = basename(time() . '_' . $pictures['name'][$i]);
                        $target_file = $target_dir . $filename;

                        // In order to check if the MIME type is image/*, we need to first store it on the webserver
                        if (move_uploaded_file($pictures['tmp_name'][$i], $target_file)) {
                            $filetype = mime_content_type($target_file);
                            if (strpos($filetype, 'image/') !== false) {
                                // Insert into DB
                                $sql = 'INSERT INTO medias (mediaType, mediaName, idPost) VALUES (:mediaType, :mediaName, :idPost)';
                                $prepQuery = $pdo->prepare($sql);
                                $prepQuery->bindParam(':mediaType', $filetype);
                                $prepQuery->bindParam(':mediaName', $filename);
                                $prepQuery->bindParam(':idPost', $postId);

                                // Check if media was inserted
                                if ($prepQuery->execute()) {
                                    $postAlert = 'Votre post a été ajouté';
                                } else {
                                    // Remove post from DB
                                    $sql = 'DELETE FROM posts WHERE idPost = :idPost';
                                    $prepQuery = $pdo->prepare($sql);
                                    $prepQuery->bindParam(':idPost', $postId);

                                    $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
                                    $postOk = false;
                                }
                            } else {
                                // Throw incorrect file format error
                                $postAlert = "Un de vos fichiers n'est pas une image";
                                $postOk = false;
                            }
                        } else {
                            // File type was not an image
                            $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
                            $postOk = false;
                        }
                    } else {
                        // File size was too big
                        $postAlert = 'Un de vos fichiers est trop gros';
                        $postOk = false;
                    }
                }
            }

            $postAlert = 'Votre post a été ajouté';
        } else {
            $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
            $postOk = false;
        }
    } else {
        $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
        $postOk = false;
    }

    // Send back to homepage with alert
    header('Location: index.php?alert="' . urlencode($postAlert) . '"');
} else {
    // Redirect to home page
    header('Location: index.php');
}
?>
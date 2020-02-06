<?php
/*
 * Project  : CFPTi Portfolio
 * Author   : Stacked - I.FA-P3B
 * Desc.    : Adds user-submitted post to database
 */

// Include functions
require_once('../functions/functions.php');

// Start session
session_start();

// Define vars

$postOk = true;
$postAlert = '';

// Filter inputs
$comment = filter_input(INPUT_POST, 'postComment', FILTER_SANITIZE_STRING);
$submitted = filter_input(INPUT_POST, 'postSubmit', FILTER_SANITIZE_STRING);

// Get sent files count and remove empty entries
$picturesCount = count(array_filter($_FILES['postPictures']['name']));
$pictures = $_FILES['postPictures'];
// Check if user actually submitted POST data and not accidentally entered the URL
if ($submitted) {
    // Check if PDO is correctly setup'd
    if (getPDO()) {
        // Begin transaction
        getPDO()->beginTransaction();

        // Insert post in DB
        $post = addPost($comment);

        // Check if query execution is OK and files have been passed
        if ($post['success']) {
            $postAlert = 'Votre post a été ajouté';
            // Check if user submitted pictures
            if ($picturesCount > 0) {
                // Get post ID
                $postId = (int)$post['postId'];

                $target_dir = 'uploads/img/';

                // Check if any file is bigger than 3M and the type of the file
                $sizeOk = checkFilesSize($pictures, $picturesCount);
                $typeOk = checkFilesType($pictures, $picturesCount);
                if ($sizeOk) {
                    // Check file type
                    if ($typeOk) {
                        // Loop through all submitted files
                        for ($i = 0; $i < $picturesCount; $i++) {
                            $filename = basename(uniqid('', true) . '_' . $pictures['name'][$i]);
                            $target_file = $target_dir . $filename;
                            // Move temp file to local storage
                            move_uploaded_file($pictures['tmp_name'][$i], $target_file);

                            // Insert into DB
                            $file = [
                                'mediaType' => $pictures['type'][$i],
                                'mediaName' => $filename,
                                'idPost' => $postId
                            ];

                            $mediaAdded = addMedia($file);

                            // Check if media was inserted
                            if ($mediaAdded) {
                                $postAlert = 'Votre post a été ajouté';
                                // Commit full transaction (post + media add)
                                getPDO()->commit();
                            } else {
                                // Rollback media add, delete file
                                getPDO()->rollBack();
                                unlink($target_file);

                                $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
                                $postOk = false;
                            }
                        }
                    } else {
                        // Incorrect file type
                        // Rollback transaction
                        getPDO()->rollBack();

                        $postAlert = "Un de vos fichiers n'est pas une image";
                        $postOk = false;
                    }
                } else {
                    // One file or more was too big
                    // Rollback transaction
                    getPDO()->rollBack();

                    $postAlert = 'Un de vos fichiers est trop gros (max 3MB par fichier / max 70MB en tout)';
                    $postOk = false;
                }
            } else {
                // Commit post add transaction
                getPDO()->commit();
            }
        } else {
            // Rollback transaction
            getPDO()->rollBack();
            $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
            $postOk = false;
        }
    } else {
        $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
        $postOk = false;
    }
}
// Set session variables and redirect user to home page
$_SESSION['postOk'] = $postOk;
$_SESSION['postAlertMsg'] = $postAlert;
header('Location: ../index.php');
exit;

?>
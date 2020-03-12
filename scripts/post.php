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
$mediasCount = count(array_filter($_FILES['postMedias']['name']));
$medias = $_FILES['postMedias'];
// Check if user actually submitted POST data and not accidentally entered the URL
if ($submitted) {
    if ($comment !== '' || $mediasCount > 0) {
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
                if ($mediasCount > 0) {
                    // Get post ID
                    $postId = (int)$post['postId'];

                    $sizeOk = checkFilesSize($medias, $mediasCount);
                    $typeOk = checkFilesType($medias, $mediasCount);
                    // Check file type
                    if ($typeOk) {
                        // Check if any file is bigger than 3M and the type of the file
                        if ($sizeOk) {
                            $flagMediaError = false;
                            // Loop through all submitted files
                            for ($i = 0; $i < $mediasCount; $i++) {
                                // Detect file type and move file to corresponding folder
                                $uploaddir = '../' . returnUploadDir($medias['type'][$i]);

                                $filename = basename(uniqid('', true) . '_' . $medias['name'][$i]);
                                $target_file = $uploaddir . $filename;
                                // Move temp file to local storage
                                $result = move_uploaded_file($medias['tmp_name'][$i], $target_file);

                                // Insert into DB
                                $file = [
                                    'mediaType' => $medias['type'][$i],
                                    'mediaName' => $filename,
                                    'idPost' => $postId
                                ];

                                $mediaAdded = addMedia($file);

                                // Check if media was inserted
                                if ($mediaAdded) {
                                    $postAlert = 'Votre post a été ajouté';
                                } else {
                                    // Set flag and delete file
                                    $flagMediaError = true;
                                    unlink($target_file);

                                    $postAlert = "Une erreur s'est produite lors de l'ajout de votre post";
                                    $postOk = false;
                                }
                            }

                            // Check if all files were inserted
                            if (!$flagMediaError) {
                                // Commit full transaction (post + media add)
                                getPDO()->commit();
                            } else {
                                // Rollback media add
                                getPDO()->rollBack();
                            }
                        } else {
                            // Incorrect file type
                            // Rollback transaction
                            getPDO()->rollBack();

                            $postAlert = 'Un de vos fichiers est trop gros (max 3MB par image / max 10MB par vidéo / max 70MB en tout)';
                            $postOk = false;
                        }
                    } else {
                        // One file or more was too big
                        // Rollback transaction
                        getPDO()->rollBack();

                        $postAlert = "Un de vos fichiers n'est pas un média supporté (image, vidéo ou audio)";
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
    } else {
        $postAlert = 'Votre post ne contient aucun commentaire ni média !';
        $postOk = false;
    }
}
// Set session variables and redirect user to home page
$_SESSION['postOk'] = $postOk;
$_SESSION['postAlertMsg'] = $postAlert;
header('Location: ../index.php');
exit;

?>
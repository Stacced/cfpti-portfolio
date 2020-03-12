<?php
require('../functions/functions.php');
session_start();
$idMedia = filter_input(INPUT_GET, 'idMedia', FILTER_SANITIZE_NUMBER_INT);
$idPost = filter_input(INPUT_GET, 'idPost', FILTER_SANITIZE_NUMBER_INT);

try {
    // Begin transaction
    getPDO()->beginTransaction();
    if (deletePostMedia($idMedia)) {
        // Get post and post medias
        $post = getPostById($idPost);
        $postMedias = getMediasByPostId($idPost);

        // Check if post has a comment and medias associated with it, otherwise post is empty and gets deleted
        if ($post['comment'] === '' && count($postMedias) === 0) {
            // Completely delete post from database
            deletePost($idPost);

            $_SESSION['postOk'] = true;
            $_SESSION['postAlertMsg'] = 'Post supprimé (aucun commentaire / aucun média associé';

            // Commit transaction and redirect user
            getPDO()->commit();
            header('Location: ../index.php');
            exit();
        }

        // Commit DB changes
        getPDO()->commit();
        $_SESSION['postOk'] = true;
        $_SESSION['postAlertMsg'] = 'Média supprimé avec succès';
    } else {
        $_SESSION['postOk'] = false;
        $_SESSION['postAlertMsg'] = 'Erreur lors de la suppression du média';
        getPDO()->rollBack();
    }
} catch (Exception $e) {
    // Rollback changes
    getPDO()->rollBack();
} finally {
    header('Location: ../updatePost.php?id=' . $idPost);
    exit();
}
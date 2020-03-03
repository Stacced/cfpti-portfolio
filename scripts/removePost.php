<?php
require('../functions/functions.php');
session_start();
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

try {
    // Begin transaction
    getPDO()->beginTransaction();
    if (deletePostMediasFiles($id)) {
        if (deletePost($id)) {
            $_SESSION['postOk'] = true;
            $_SESSION['postAlertMsg'] = 'Post supprimé avec succès';
            getPDO()->commit();
        }
    } else {
        $_SESSION['postOk'] = false;
        $_SESSION['postAlertMsg'] = 'Erreur lors de la suppression du post';
        getPDO()->rollBack();
    }
} catch (Exception $e) {
    // Rollback changes
    getPDO()->rollBack();
} finally {
    header('Location: ../index.php');
    exit();
}
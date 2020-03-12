<?php
require('../functions/functions.php');
session_start();

// GET variables
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Begin transaction
    getPDO()->beginTransaction();
    if (deletePostMediasFiles($id)) {
        if (deletePost($id)) {
            $_SESSION['postOk'] = true;
            $_SESSION['postAlertMsg'] = 'Post supprimé avec succès';

            // Commit transaction
            getPDO()->commit();
        }
    } else {
        $_SESSION['postOk'] = false;
        $_SESSION['postAlertMsg'] = 'Erreur lors de la suppression du post';

        // Rollback changes
        getPDO()->rollBack();
    }
} catch (Exception $e) {
    // Rollback changes
    getPDO()->rollBack();
} finally {
    header('Location: ../index.php');
    exit();
}
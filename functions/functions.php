<?php
/*
 * Project  : CFPTi Portfolio
 * Author   : Stacked - I.FA-P3B
 * Desc.    : Useful functions for the project
 */

// Define vars
define('MAX_FILESIZE_IMAGE', 3145728);
define('MAX_FILESIZE_VIDEO', 10485760);
define('MAX_FILESIZE_AUDIO', 41943040);

// Change locale for dates
// UTF-8 part is mandatory, otherwise accents will display as question marks
// Had to uncomment "fr_FR.UTF-8" in /etc/locale.gen and then run locale-gen
setlocale(LC_ALL, 'fr_FR.UTF-8');

/**
 * Returns PDO object
 * @return PDO|null
 */
function getPDO() {
    static $pdo = null;

    $dbUsername = 'cfptportfolio';
    $dbPassword = 'cfpti2020';
    $dbHostname = 'localhost';
    $dbName = 'cfpti_portfolio';

    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:dbname=' . $dbName . ';host=' . $dbHostname, $dbUsername, $dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ]);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage() . '<br />';
            echo 'N° : ' . $e->getCode();
            die('Could not connect to MySQL');
        }
    }

    return $pdo;
}

/**
 * Returns an array containing all posts with linked medias
 * @return array Posts
 */
function getPosts() {
    $sql = 'SELECT posts.idPost, comment, posts.creationDate, posts.editDate, GROUP_CONCAT(medias.mediaType) as mediaTypes, GROUP_CONCAT(medias.mediaName) as mediaNames FROM posts
            LEFT JOIN medias ON medias.idPost = posts.idPost
            GROUP BY posts.idPost
            ORDER BY posts.creationDate DESC';
    try {
        $posts = getPDO()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        // Using reference variable to directly edit post array
        foreach ($posts as &$post) {
            $post['mediaTypes'] = explode(',', $post['mediaTypes']);
            $post['mediaNames'] = explode(',', $post['mediaNames']);
        }
        return $posts;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Returns an array containing post data with passed ID
 * @param $idPost
 * @return array|mixed
 */
function getPostById($idPost) {
    $sql = 'SELECT idPost, comment FROM posts WHERE idPost = :idPost';

    try {
        $ps = getPDO()->prepare($sql);
        $ps->bindParam(':idPost', $idPost);
        $ps->execute();
        return $ps->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Returns an array containing all medias associated with passed post ID
 * @param integer $idPost Post ID
 * @return array
 */
function getMediasByPostId($idPost) {
    // Init
    static $ps = null;
    $sql = 'SELECT * FROM medias WHERE idPost = :idPost';

    // Process
    if ($ps === null) {
        $ps = getPDO()->prepare($sql);
    }

    try {
        $ps->bindParam(':idPost', $idPost);
        $ps->execute();
        return $ps->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Returns an HTML string containing every post with its medias
 * @return string Posts HTML string
 */
function displayPostsModal() {
    $posts = getPosts();
    $html = '';
    foreach ($posts as $post) {
        $html .= '<div class="modal-content"><div class="modal-body post-body">';

        for ($i = 0; $i < count($post['mediaNames']); $i++) {
            $uploaddir = returnUploadDir($post['mediaTypes'][$i]);
            if (strpos($post['mediaTypes'][$i], 'image/') !== false) {
                $html .= '<img src="' . $uploaddir . $post['mediaNames'][$i] . '" alt="..." style="max-height: 300px; margin-right: 10px">';
            } else if (strpos($post['mediaTypes'][$i], 'video/') !== false) {
                $html .= '<video width="320" height="240" controls autoplay style="margin-right: 10px"><source src="' . $uploaddir . $post['mediaNames'][$i] . '" type="' . $post['mediaTypes'][$i] .'">Video tag not supported</video><br>';
            } else if (strpos($post['mediaTypes'][$i], 'audio/') !== false) {
                $html .= '<audio controls style="margin-right: 10px"><source src="' . $uploaddir . $post['mediaNames'][$i] . '" type="' . $post['mediaTypes'][$i] . '">Audio tag not supported</audio><br>';
            }
        }

        $html .= $post['comment'] . '</div>';
        $html .= '<div class="modal-footer">Envoyé le ' . strftime('%e %B %Y', strtotime($post['creationDate'])) . ' à ' . strftime('%R', strtotime($post['creationDate'])) . '';
        $html .= '<div class="btn-group btn-actions-group" role="group" aria-label="Actions">';
        $html .= '<a href="updatePost.php?id=' . $post['idPost'] .'" class="btn btn-warning"><i class="fas fa-pencil-alt warning-icon"></i></a>';
        $html .= '<a href="scripts/removePost.php?id=' . $post['idPost'] . '" class="btn btn-danger"><i class="far fa-trash-alt delete-icon"></i></a>';
        $html .= '</div></div></div>';
    }
    return $html;
}

/**
 * Returns an HTML string containing the whole update panel div for the post
 * @param integer $idPost Post ID
 * @return string Update panel HTML string
 */
function displayPostUpdatePanel($idPost) {
    $post = getPostById($idPost);
    $medias = getMediasByPostId($idPost);
    $html = '<div class="panel-body"><table><tr><td>';
    $html .= '<input type="text" name="updatedComment" value="' . $post['comment'] . '">';
    $html .= '</td><td><input type="submit" class="btn btn-primary btn-sm" name="submitUpdate" value="Mettre à jour le post">';
    $html .= '</td></tr>';
    for ($i = 0; $i < count($medias); $i++) {
        $html .= '<tr><td>';
        $uploaddir = returnUploadDir($medias[$i]['mediaType']);
        $path = $uploaddir . $medias[$i]['mediaName'];
        $fileType = $medias[$i]['mediaType'];

        if (strpos($fileType, 'image/') !== false) {
            $html .= '<img src="' . $path . '" alt="..." style="max-height: 300px; margin-right: 10px">';
        } else if (strpos($fileType, 'video/') !== false) {
            $html .= '<video width="320" height="240" controls autoplay style="margin-right: 10px"><source src="' . $path . '" type="' . $fileType .'">Video tag not supported</video>';
        } else if (strpos($fileType, 'audio/') !== false) {
            $html .= '<audio controls style="margin-right: 10px"><source src="' . $path . '" type="' . $fileType . '">Audio tag not supported</audio>';
        }
        $html .= '</td><td><a href="scripts/deleteMedia.php?id=" class="btn btn-primary btn-sm">Supprimer</a>';
        $html .= '</td></tr>';
    }
    $html .= '</table></div>';

    return $html;
}

/**
 * Adds post to database
 * @param string $comment Post comment
 * @return array
 */
function addPost($comment) {
    // Init
    static $ps = null;
    $sql = 'INSERT INTO posts (comment) VALUES (:comment)';
    $ok = false;
    $postId = null;

    // Process
    if ($ps === null) {
        $ps = getPDO()->prepare($sql);
    }

    try {
        // Execute prepared statement with params
        $ps->bindParam(':comment', $comment);
        $ok = $ps->execute();

        // Get inserted post ID
        $postId = getPDO()->lastInsertId();
    } catch (PDOException $e) {
        $ok = false;
    }

    // Output
    return ['success' => $ok, 'postId' => $postId];
}

/**
 * Adds passed $file to database (special format
 * $file = [
 *      'mediaType' => string / MIME type,
 *      'mediaName' => string,
 *      'idPost' => int
 * ]
 * @param array $file
 * @return bool
 */
function addMedia($file) {
    // Init
    static $ps = null;
    $sql = 'INSERT INTO medias (mediaType, mediaName, idPost) VALUES (:mediaType, :mediaName, :idPost)';
    $ok = false;

    // Process
    if ($ps === null) {
        $ps = getPDO()->prepare($sql);
    }

    try {
        // Execute prepared statement with params
        $ps->bindParam(':mediaType', $file['mediaType']);
        $ps->bindParam(':mediaName', $file['mediaName']);
        $ps->bindParam(':idPost', $file['idPost']);
        $ok = $ps->execute();
    } catch (PDOException $e) {
        $ok = false;
    }

    // Output
    return $ok;
}

/**
 * Deletes post from database
 * @param string $idPost
 * @return bool
 */
function deletePost($idPost) {
    // Init
    static $ps = null;
    $sql = 'DELETE FROM posts WHERE idPost = :idPost';
    $ok = false;

    // Process
    if ($ps === null) {
        $ps = getPDO()->prepare($sql);
    }

    try {
        $ps->bindParam(':idPost', $idPost);
        $ok = $ps->execute();
    } catch (PDOException $e) {
        $ok = false;
    }

    // Output
    return $ok;
}

/**
 * Takes a post id as input and deletes associated medias from disk
 * @param string $idPost
 * @return bool
 */
function deletePostMediasFiles($idPost) {
    $medias = getMediasByPostId($idPost);

    try {
        foreach ($medias as $media) {
            $path = returnUploadDir($media['mediaType']) . $media['mediaName'];
            unlink('../' . $path);
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Checks if $files ($_FILES formatted) contains only images with size <= 3M
 * @param array $files $_FILES array
 * @param int $filesCount Files count
 * @return bool
 */
function checkFilesSize($files, $filesCount) {
    // Process
    $sizeOk = true;
    for ($i = 0; $i < $filesCount; $i++) {
        $fileType = $files['type'][$i];

        if (strpos($fileType, 'image/') !== false) {
            if ($files['size'][$i] > MAX_FILESIZE_IMAGE) {
                $sizeOk = false;
                break;
            }
        } else if (strpos($fileType, 'video/') !== false) {
            if ($files['size'][$i] > MAX_FILESIZE_VIDEO) {
                $sizeOk = false;
                break;
            }
        } else if (strpos($fileType, 'audio/') !== false) {
            if ($files['size'][$i] > MAX_FILESIZE_AUDIO) {
                $sizeOk = false;
                break;
            }
        }

    }

    // Output
    return $sizeOk;
}

/**
 * Checks if $files array ($_FILES formatted) contains images, videos only (MIME type check)
 * @param array $files $_FILES array
 * @param int $filesCount Files count
 * @return bool
 */
function checkFilesType($files, $filesCount) {
    // Process
    $typeOk = true;
    for ($i = 0; $i < $filesCount; $i++) {
        $fileType = mime_content_type($files['tmp_name'][$i]);
        if (strpos($fileType, 'image/') === false && !preg_match('/mp4|ogg|webm/i', $fileType) && !preg_match('/mpeg|ogg|wav/i', $fileType)) {
            $typeOk = false;
            break;
        }
    }

    // Output
    return $typeOk;
}

/**
 * Returns corresponding file type upload directory path
 * @param string $fileType MIME file type
 * @return string Upload directory path
 */
function returnUploadDir($fileType) {
    // Process
    $uploaddir = 'uploads/';
    if (strpos($fileType, 'image/') !== false) {
        $uploaddir .= 'img/';
    } else if (strpos($fileType, 'video/') !== false) {
        $uploaddir .= 'videos/';
    } else if (strpos($fileType, 'audio/') !== false) {
        $uploaddir .= 'sounds/';
    }

    // Output
    return $uploaddir;
}
?>
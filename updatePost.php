<?php
session_start();
require('functions/functions.php');

// GET & POST variables
$idPost = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$submit = filter_input(INPUT_POST, 'submitUpdate', FILTER_SANITIZE_STRING);
$updatedComment = filter_input(INPUT_POST, 'updatedComment', FILTER_SANITIZE_STRING);
$updatedMedias = $_FILES['updatedMedias'];

// Check if submit update button was pressed
if ($submit) {
	if ($updatedComment !== '' && count(getMediasByPostId($idPost)) > 0) {
		getPDO()->beginTransaction();

        $mediasCount = count(array_filter($updatedMedias['name']));
        $sizeOk = checkFilesSize($updatedMedias, $mediasCount);
        $typeOk = checkFilesType($updatedMedias, $mediasCount);
        // Check file type
        if ($typeOk) {
            // Check if any file is bigger than 3M and the type of the file
            if ($sizeOk) {
                $flagMediaError = false;
                // Loop through all submitted files
                for ($i = 0; $i < $mediasCount; $i++) {
                    // Detect file type and move file to corresponding folder
                    $uploaddir = returnUploadDir($updatedMedias['type'][$i]);

                    $filename = basename(uniqid('', true) . '_' . $updatedMedias['name'][$i]);
                    $target_file = $uploaddir . $filename;
                    // Move temp file to local storage
                    $result = move_uploaded_file($updatedMedias['tmp_name'][$i], $target_file);

                    // Insert into DB
                    $file = [
                        'mediaType' => $updatedMedias['type'][$i],
                        'mediaName' => $filename,
                        'idPost' => $idPost
                    ];

                    $mediaAdded = addMedia($file);

                    // Check if media was inserted
                    if ($mediaAdded) {
                        $postAlert = 'Média ajouté';
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

        if (updatePostComment($idPost, $updatedComment)) {
            $postOk = true;
            $postAlertMsg = 'Post mis à jour !';
        } else {
            $postOk = false;
            $postAlertMsg = "Une erreur s'est produite lors de la mise à jour du post";
        }
	} else {
		header('Location: scripts/removePost.php?id=' . $idPost);
		exit();
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>Facebook CFPT</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="assets/css/bootstrap.css" rel="stylesheet">
	<link href="assets/css/facebook.css" rel="stylesheet">
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
	<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css"/>
</head>
<body>
<div class="wrapper">
	<div class="box">
		<div class="row row-offcanvas row-offcanvas-left">
			<!-- main right col -->
			<div class="column col-sm-10 col-xs-11" id="main">

				<!-- top nav -->
				<?php include('nav.inc.php'); ?>
				<!-- /top nav -->

				<div class="padding">
					<div class="full col-sm-9">

						<!-- content -->
						<div class="row">

							<!-- main col left -->
							<div class="col-sm-5">
								<div class="panel panel-default">
									<div class="panel-thumbnail"><img src="assets/img/bg_5.jpg" class="img-responsive"></div>
									<div class="panel-body">
										<p class="lead">Facebook CFPT</p>
										<p>45 Followers, 13 Posts</p>

										<p>
											<img src="assets/img/uFp_tsTJboUY7kue5XAsGAs28.png" height="28" width="28">
										</p>
									</div>
								</div>
							</div>

							<!-- main col right -->
							<div class="col-sm-7">

								<!--Mot accueil-->
								<div class="panel panel-default">
									<div class="panel-heading">
										<h1>Mise à jour de post</h1>
									</div>
								</div>

								<div class="panel panel-default">
									<form method="POST" action="updatePost.php?id=<?= $idPost ?>" enctype="multipart/form-data">
										<?= displayPostUpdatePanel($idPost) ?>
									</form>
								</div>
							</div>
						</div>
						<!--/row-->

						<div class="row">
							<div class="col-sm-6">
								<a href="#">Twitter</a> <small class="text-muted">|</small> <a href="#">Facebook</a>
								<small class="text-muted">|</small> <a href="#">Google+</a>
							</div>
						</div>

					</div><!-- /col-9 -->
				</div><!-- /padding -->
			</div>
			<!-- /main -->
		</div>
	</div>
</div>
</body>
<script src="https://kit.fontawesome.com/a51964368d.js" crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        <?php if ($postOk === true) { ?>
        alertify.success("<?= $postAlertMsg ?>");
        <?php } else if ($postOk === false) { ?>
        alertify.error("<?= $postAlertMsg ?>");
        <?php }
        $_SESSION['postOk'] = null;
        $_SESSION['postAlertMsg'] = null;
        ?>
    });
</script>
</html>
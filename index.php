<?php
/*
 * Project  : CFPTi Portfolio
 * Author   : Stacked - I.FA-P3B
 * Desc.    : Main view with user-submitted posts (wip)
 */
// Require functions file
require('functions/functions.php');

// Start session
session_start();

// Get post publication status
$postOk = isset($_SESSION['postOk']) ? $_SESSION['postOk'] : null;
$postAlertMsg = isset($_SESSION['postAlertMsg']) ? $_SESSION['postAlertMsg'] : null;
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
										<div class="panel-thumbnail"><img src="assets/img/bg_5.jpg"
												class="img-responsive"></div>
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
									<div class="panel panel-default">
										<div class="panel-heading">
											<h1>Bienvenue !</h1>
										</div>
									</div>
									<?= displayPostsModal() ?>
								</div>
							</div>
							<!--/row-->

							<div class="row">
								<div class="col-sm-6">
									<a href="#">Twitter</a> <small class="text-muted">|</small> <a href="#">Facebook</a>
									<small class="text-muted">|</small> <a href="#">Google+</a>
								</div>
							</div>

							<div class="row" id="footer"></div>
						</div><!-- /col-9 -->
					</div><!-- /padding -->
				</div>
				<!-- /main -->

			</div>
		</div>
	</div>

	<!--post modal-->
	<div id="postModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
					Mettre à jour mon statut
				</div>
				<form class="form center-block" method="POST" action="scripts/post.php" enctype="multipart/form-data">
					<div class="modal-body">
						<div class="form-group">
							<textarea class="form-control input-lg" autofocus=""
								placeholder="Que voulez-vous partager ?" name="postComment"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<div>
							<input type="submit" class="btn btn-primary btn-sm" aria-hidden="true" value="Poster" name="postSubmit">
							<ul class="pull-left list-inline">
								<li>
									<input type="file" name="postMedias[]" multiple id="postMedias" style="display: none" accept="image/*,video/mp4,video/ogg,video/webm,audio/mpeg,audio/ogg,audio/wav">
									<label for="postMedias"><i class="fas fa-camera"></i></label>
								</li>
							</ul>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- delete modal -->
	<div id="deleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
					Suppression du post
				</div>
				<form class="form center-block" method="POST" action="scripts/removePost.php" enctype="multipart/form-data">
					<div class="modal-body">
						<div class="form-group">
							Êtes-vous sûr de vouloir supprimer ce post ?
						</div>
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-danger btn-sm" aria-hidden="true" value="Confirmer la suppression" name="postSubmit">
					</div>
				</form>
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
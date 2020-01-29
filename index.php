<?php
$alert = filter_input(INPUT_GET, 'alert', FILTER_SANITIZE_STRING);
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
	<script src="https://kit.fontawesome.com/a51964368d.js" crossorigin="anonymous"></script>
</head>

<body>
	<div class="wrapper">
		<div class="box">
			<div class="row row-offcanvas row-offcanvas-left">
				<!-- main right col -->
				<div class="column col-sm-10 col-xs-11" id="main">

					<!-- top nav -->
					<div class="navbar navbar-blue navbar-static-top">
						<div class="navbar-header">
							<button class="navbar-toggle" type="button" data-toggle="collapse"
								data-target=".navbar-collapse">
								<span class="sr-only">Toggle</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a href="#" class="navbar-brand logo">F</a>
						</div>
						<nav class="collapse navbar-collapse" role="navigation">
							<form class="navbar-form navbar-left">
								<div class="input-group input-group-sm" style="min-width:360px;">
									<input class="form-control" placeholder="Search" name="srch-term" id="srch-term"
										type="text">
									<div class="input-group-btn">
										<button class="btn btn-default" type="submit"><i
												class="fas fa-search"></i></button>
									</div>
								</div>
							</form>
							<ul class="nav navbar-nav">
								<li>
									<a href="#"><i class="fas fa-home"></i> Accueil</a>
								</li>
								<li>
									<a href="#postModal" role="button" data-toggle="modal"><i class="fas fa-plus"></i>
										Post</a>
								</li>
								<li>
									<a href="#"><span class="badge">badge</span></a>
								</li>
							</ul>
							<ul class="nav navbar-nav navbar-right">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
											class="fas fa-user"></i></a>
									<ul class="dropdown-menu">
										<li><a href="">More</a></li>
										<li><a href="">More</a></li>
										<li><a href="">More</a></li>
										<li><a href="">More</a></li>
										<li><a href="">More</a></li>
									</ul>
								</li>
							</ul>
						</nav>
					</div>
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
												<img src="assets/img/uFp_tsTJboUY7kue5XAsGAs28.png" height="28px" width="28px">
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
				<form class="form center-block" method="POST" action="post.php" enctype="multipart/form-data">
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
									<input type="file" name="postPictures[]" multiple id="postPictures" style="display: none" accept="image/*">
									<label for="postPictures"><i class="fas fa-camera"></i></label>
								</li>
							</ul>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('[data-toggle=offcanvas]').click(function () {
				$(this).toggleClass('visible-xs text-center');
				$(this).find('i').toggleClass('glyphicon-chevron-right glyphicon-chevron-left');
				$('.row-offcanvas').toggleClass('active');
				$('#lg-menu').toggleClass('hidden-xs').toggleClass('visible-xs');
				$('#xs-menu').toggleClass('visible-xs').toggleClass('hidden-xs');
				$('#btnShow').toggle();
			});
		});
	</script>
</body>

</html>
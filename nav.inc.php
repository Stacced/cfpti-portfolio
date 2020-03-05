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
                <input class="form-control" placeholder="Search" name="srch-term" id="srch-term" type="text">
                <div class="input-group-btn">
                    <button class="btn btn-default" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        <ul class="nav navbar-nav">
            <li>
                <a href="index.php"><i class="fas fa-home"></i> Accueil</a>
            </li>
			<?php if (strpos($_SERVER['PHP_SELF'], 'updatePost') === false): ?>
            <li>
                <a href="#postModal" role="button" data-toggle="modal"><i class="fas fa-plus"></i> Post</a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
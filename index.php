<?php
require( 'config.php' );
// require( '/includes/openpeek-class.php' );
?>
<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>OpenPeeks - Free Stock Photos for the masses</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/app.css">
	<script src="js/vendor/modernizr-2.8.0.min.js"></script>
</head>
<body>
	<!--[if lt IE 8]>
	<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->

	<div class="navbar fixed">
		<div class="container">
			<h4 class="pull-left"><a href="index.php">OpenPeeks</a></h4>
			<ul class="pull-right">
				<li><a href="/">Home</a></li>
				<li><a href="about.php">About</a></li>
				<li><a href="https://github.com/ThemeAvenue/OpenPeeks">Github</a></li>
			</ul>
		</div>
	</div>

	<?php
	/* Load a random image */
	$images = $opdb->get_images( 'all' );
	$count  = count( $images )-1;
	$rand   = rand( 0, $count );
	$url    = $images[$rand]['preview_url'];
	?>
	<img src="<?php echo $url; ?>">

	<div class="superheader">
		<div class="container">
			<input type="search" placeholder="Enter keywords..." id="input_search">
			<h3>Latest 20 pics</h3>
		</div>
	</div>

	<div class="container">

		<!-- <h3>KaboomPics</h3> -->
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Tags</th>
					<th>Download link</th>
				</tr>

				<?php
				for ( $i = 0; $i <= 20; ++$i ) {

					$tags = $opdb->get_image_tags( $i );

					foreach ( $tags as $key => $tag ) {
						$tags[$key] = "<span class='badge'>$tag</span>";
					} ?>

					<tr>
						<td><a target="_blank" class="name" href="<?php echo $images[$i]['source']; ?>" rel="<?php echo $images[$i]['preview_url']; ?>"><?php echo $images[$i]['name']; ?></a></td>
						<td><?php echo implode( ' ', $tags ); ?></td>
						<td><a class="button-dl button primary small" href="<?php echo $images[$i]['link']; ?>" download>Download</a></td>
					</tr>

				<?php }
				?>

			</thead>
			<tbody>
				<tr class="noresults">
					<td colspan="3">No results...</td>
				</tr>
			</tbody>
		</table>

	</div>

	<?php require_once('footer.php'); ?>
</body>
</html>

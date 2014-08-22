<?php
////////////////
// kaboompics //
////////////////
$kp_request = "https://www.kimonolabs.com/api/dumrmhu4?apikey=34f710899fb2424aeb213c881ff10109";
$kp_response = file_get_contents($kp_request);
$kp_results = json_decode($kp_response, TRUE);
$kp_count = $kp_results['count'];
$kp_array = $kp_results['results']['collection1'];
// print_r($kp_array);

//////////////
// picjumbo //
//////////////
$pj_request = "https://www.kimonolabs.com/api/52f46muq?apikey=34f710899fb2424aeb213c881ff10109";
$pj_response = file_get_contents($pj_request);
$pj_results = json_decode($pj_response, TRUE);
$pj_count = $pj_results['count'];
$pj_array = $pj_results['results']['collection1'];
// print_r($pj_array);

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

	<div class="superheader">
		<div class="container">
			<input type="search" placeholder="Enter keywords..." id="input_search">
			<h3>Latest 50 pics</h3>
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
			</thead>
			<tbody>
				<tr class="noresults">
					<td colspan="3">No results...</td>
				</tr>
				<?php
				foreach ($kp_array as $value) {
					$kp_tags = $value['tags'];
					$kp_tags = str_replace("TAGS:","",$kp_tags);
					$kp_tag_single = explode('#', $kp_tags); // $kp_tag_single[1]
					echo '
					<tr>
						<td><a target="_blank" class="name" href="'. $value['name']['href'] .'" rel="'. $value['img'] .'">'. $value['name']['text'] .'</a></td>
						<td>(hidden)';
						foreach (array_slice($kp_tag_single,1) as $tag) {
							// echo "<span class='badge'>$tag</span>";
						}
						echo '
						</td>
						<td><a class="button-dl button primary small" href="'. $value['link'] .'">Download</a></td>
					</tr>';
				}
				?>
			</tbody>
		</table>

		<!-- <h3>PicJumbo</h3> -->
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Tags</th>
					<th>Download link</th>
				</tr>
			</thead>
			<tbody>
				<tr class="noresults">
					<td colspan="3">No results...</td>
				</tr>
				<?php
				foreach ($pj_array as $value) {
					$pj_dl_link = str_replace("-1300x866","",$value['img']);
					$pj_filename = str_replace("http://picjumbo.com/wp-content/uploads/","",$value['img']);
					$pj_filename = str_replace("-1300x866","",$pj_filename);
					// <td><a class="button primary small" href="http://picjumbo.com/download/?d='. $pj_dl_link .'.jpg">Download</a></td>
					echo '
					<tr>
						<td><a target="_blank" class="name" href="'. $value['link'] .'" rel="'. $value['img'] .'">'. $value['name'] .'</a></td>
						<td>N/A</td>
						<td><a class="button-dl button primary small" href="'. $pj_dl_link .'" download="'. $pj_filename .'">Download</a></td>
					</tr>';
				}
				?>
			</tbody>
		</table>

		<?php
		// Retrieve tags from each page (picjumbo)
		// http://simplehtmldom.sourceforge.net/
		require_once('includes/simple_html_dom.php');

		// Create DOM from URL or file
		$html = file_get_html('http://picjumbo.com/ready-to-cut-strawberries/');

		// Find all tags 
		foreach($html->find('div.meta a') as $element) {
			echo "<span class='badge'>". $element->innerText() ."</span>";
		}
		?>

	</div>

	<?php require_once('footer.php'); ?>
</body>
</html>

$(function () {
	// http://james.padolsey.com/demos/imgPreview/full/
	$('a.name').imgPreview({
		containerID: 'imgPreviewWithStyles',
		srcAttr: 'rel',
		imgCSS: {
			height: 200
		},
		onShow: function (link) {
			$('<span>' + $(link).text() + '</span>').appendTo(this);
		},
		onHide: function (link) {
			$('span', this).remove();
		}
	});

	// Live Search
	$('#input_search').quicksearch('.table > tbody tr', {
		// 'delay': 100,
		// 'selector': 'th',
		// 'stripeRows': ['odd', 'even'],
		'loader': 'span.loading',
		'noResults': 'tr.noresults'
	});

	// Give hints to users
	$('.button-dl').on('click', function () {
		$(this).prop('disabled', true).text('Please wait...');
		setTimeout(function () {
			$(this).prop('disabled', false).text('Download');
		}, 2000);
	});

	///////////////
	// Unsplash //
	///////////////
	// http://www.snip2code.com/Snippet/68961/Fetch-image-urls-from-unsplash-com
	// http://www.snip2code.com/Snippet/19236/Download-all-files-from-www-unsplash-com
	// http://unsplash.com/api/read

	console.log('test');
	var unsplashApi = "http://unsplash.com/api/read/json?num=100?callback=?";
	$.getJSON(unsplashApi, function (data) {
		console.log("Unplash data: " + data);
	});
	$.getJSON(unsplashApi).done(function (data) {
		console.log("Unplash data: " + data);
	});

});
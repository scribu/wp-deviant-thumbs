jQuery(document).ready(function() {
	jQuery('.deviant-thumbs-horizontal').jcarousel({
		wrap: 'last'
	});
	
	jQuery('.deviant-thumbs-vertical').jcarousel({
		vertical: true,
		scroll: 2,
		wrap: 'last'
	});
});
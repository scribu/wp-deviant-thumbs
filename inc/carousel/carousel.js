function simpleCarousel(id, nr, speed)
{
	var e = jQuery(id);

	e.nr = parseInt(nr);
	e.li = e.find('li');	// elements
	e.n = e.li.length;

	e.addClass('simple-carousel');

	if ( e.n <= e.nr )
		return;

	// Init hide elements
	e.find('li:gt('+(e.nr-1)+')').hide();

	e.i = 0;	// index
	e.l = 0;	// last action

	function move(d)
	{
		// Rewind index from previous action
		if ( e.l == -d )
			e.i = e.i + d;

		// Set d as last action
		e.l = d;

		if ( (e.i >= 0) && (e.i + e.nr < e.n) ) 
		{
			// Hide and reveal the top and bottom elements respectively
			jQuery(e.li[e.i]).slideToggle(speed);
			jQuery(e.li[e.i+e.nr]).slideToggle(speed);

			// Update index
			e.i = e.i + d;
		}
	}

	e.up = jQuery('<i>').addClass('up').html('&nbsp;')
		.prependTo(e)
		.hide();

	e.down = jQuery('<i>').addClass('down').html('&nbsp;')
		.appendTo(e);

	e.down.click(function() {
		move(1);

		if ( e.i > 0 )
			e.up.slideDown(speed);

		if ( e.i == e.n - e.nr )
			e.down.slideUp(speed);
	});

	e.up.click(function() {
		move(-1);

		if ( e.i < e.n - e.nr )
			e.down.slideDown(speed);

		if ( e.i == -1 )
			e.up.slideUp(speed);
	});
}

function include_css(filename) {
	jQuery('head').append('<link rel="stylesheet" href="' + filename + '" type="text/css" media="screen" />');
}


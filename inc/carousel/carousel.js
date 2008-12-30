jQuery.fn.simpleCarousel = function(nr, speed) {
	var e = this;

	nr = parseInt(nr);
	e.li = e.find('li');	// elements
	e.n = e.li.length;		// number of elements

	// Init
	e.addClass('simple-carousel');
	e.prepend('<i class="up">&nbsp;</i>');
	e.append('<i class="down">&nbsp;</i>');

	// Init hide buttons
	e.find('.up').hide();
	if ( e.n <= nr ) {
		e.find('.down').slideUp(speed);
		return;
	}

	// Init hide elements
	e.find('li:gt('+(nr-1)+')').hide();

	e.i = 0;	// index
	e.l = 0;	// last action

	function move(d) {
		// Rewind index from previous action
		if ( e.l == -d )
			e.i = e.i + d;

		// Set d as last action
		e.l = d;

		if ( (e.i >= 0) && (e.i + nr < e.n) ) {
			// Hide and reveal the top and bottom elements respectively
			jQuery(e.li[e.i]).slideToggle(speed);
			jQuery(e.li[e.i+nr]).slideToggle(speed);

			// Update index
			e.i = e.i + d;
		}
	}

	e.find('.down').click(function() {
		move(1);
		if ( e.i > 0 )
			e.find('.up').slideDown(speed);

		if ( e.i == e.n-nr )
			e.find('.down').slideUp(speed);
	});

	e.find('.up').click(function() {
		move(-1);
		if ( e.i < e.n-nr )
			e.find('.down').slideDown(speed);

		if ( e.i == -1 )
			e.find('.up').slideUp(speed);
	});
}

function include_css(filename) {
	var css = document.createElement('link');

	css.setAttribute('rel', 'stylesheet');
	css.setAttribute('href', filename);
	css.setAttribute('type', 'text/css');

	document.getElementsByTagName('head').item(0).appendChild(css);
}

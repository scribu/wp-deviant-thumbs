function simpleCarousel(id, nr, speed) {
	var e = this;

	e.s = speed;
	e.nr = parseInt(nr);
	e.c = $(id);			// container
	e.li = $(e.c).find('li');	// elements
	e.n = e.li.length;		// number of elements

	$(e.c).addClass('simple-carousel');
	$(e.c).prepend('<i class="up disabled">&nbsp;</i>');
	$(e.c).append('<i class="down">&nbsp;</i>');

	if ( e.n <= e.nr ) {
		$(e.c).find('.down').addClass('disabled');
		return;
	}

	$(e.c).find('li:gt('+(nr-1)+')').hide();	// show only the first {nr} elements

	e.i = 0;	// index
	e.l = 0;	// last action

	var move = function(d) {
		if ( e.l == -d )
			e.i = e.i + d;
		e.l = d;

		if ( (e.i >= 0) && (e.i + e.nr < e.n) ) {
			$(e.li[e.i]).slideToggle(e.s);
			$(e.li[e.i+e.nr]).slideToggle(e.s);
			e.i = e.i + d;
		}
	}

	$(e.c).find('.down').click(function() {
		move(1);
		if ( e.i > 0 )
			$(e.c).find('.up').removeClass('disabled');

		if ( e.i == e.n-e.nr )
			$(e.c).find('.down').addClass('disabled');
	});

	$(e.c).find('.up').click(function() {
		move(-1);
		if ( e.i < e.n-e.nr )
			$(e.c).find('.down').removeClass('disabled');

		if ( e.i == -1 )
			$(e.c).find('.up').addClass('disabled');
	});
}

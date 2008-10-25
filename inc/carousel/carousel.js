function simpleCarousel(id, nr, s) {
	var e = this;	// gotta love lexical closures

	e.n = $(id+" li").length;
	e.id = id;
	e.nr = nr;
	e.s = s;

	if (e.n<1) return;

	$(id).addClass('simple-carousel');

	e.i = 0;		// index
	e.l = 0;		// flag: last action

	$(id+" li:gt("+(nr-1)+")").hide('slow');

	$(id+" .down").click(function() {
		if (e.l==-1)
			e.i++;
		e.l = 1;

		if (e.i+e.nr < e.n) {
			$(e.id+" li:eq("+e.i+")").slideToggle(e.s);
			$(e.id+" li:eq("+(e.i+e.nr)+")").slideToggle(e.s);
			e.i++;
		}
	});

	$(id+" .up").click(function() {
		if (e.l==1)
			e.i--;
		e.l = -1;

		if (e.i>=0) {
			$(e.id+" li:eq("+e.i+")").slideToggle(e.s);
			$(e.id+" li:eq("+(e.i+e.nr)+")").slideToggle(e.s);
			e.i--;
		}
	});
}

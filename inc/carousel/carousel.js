function simpleCarousel(id, nr, s) {
	var e = this;	// gotta love lexical closures

	e.id = id;
	e.nr = nr;
	e.s = s;

	e.n = $(e.id+" > ul > li").length;
	if (e.n<1) return;

	$(e.id).addClass('simple-carousel');

	e.i = 0;		// index
	e.l = 0;		// flag: last action

	$(e.id+" > ul > li:gt("+(nr-1)+")").hide('slow');

	$(e.id+" > .down").click(function() {
		if (e.l==-1)
			e.i++;
		e.l = 1;

		if (e.i+e.nr < e.n) {
			$(e.id+" > ul > li:eq("+e.i+")").slideToggle(e.s);
			$(e.id+" > ul > li:eq("+(e.i+e.nr)+")").slideToggle(e.s);
			e.i++;
		}
	});

	$(e.id+" > .up").click(function() {
		if (e.l==1)
			e.i--;
		e.l = -1;

		if (e.i>=0) {
			$(e.id+" > ul > li:eq("+e.i+")").slideToggle(e.s);
			$(e.id+" > ul > li:eq("+(e.i+e.nr)+")").slideToggle(e.s);
			e.i--;
		}
	});
}

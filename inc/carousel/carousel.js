function slideCarousel(id, i, j, s) {
	$(id+" li:eq("+i+")").slideToggle(s);
	$(id+" li:eq("+j+")").slideToggle(s);
}
function simpleCarousel(id, nr, s) {
	n = $(id+" li").length;

	if (n<2) return;

	$(id).addClass('simple-carousel');

	i = 0;		// index
	l = 0;		// flag: last action

	$(id+" li:gt("+(nr-1)+")").hide('slow');

	$(id+" .down").click(function(){
		if (l==-1) i++;
		l = 1;

		if (i+nr<n) {
			slideCarousel(id, i, i+nr, s);
			i++;
		}
	})

	$(id+" .up").click(function(){
		if (l==1) i--;
		l = -1;

		if (i>=0) {
			slideCarousel(id, i, i+nr, s);
			i--;
		}
	})
}

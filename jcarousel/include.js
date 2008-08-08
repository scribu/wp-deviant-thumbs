function include_js(script_filename) {
	var js = document.createElement('script');

	js.setAttribute('language', 'javascript');
	js.setAttribute('type', 'text/javascript');
	js.setAttribute('src', script_filename);

	document.getElementsByTagName('head').item(0).appendChild(js);
	return false;
}

function include_css(script_filename) {
	var css = document.createElement('link');

	css.setAttribute('rel', 'stylesheet');
	css.setAttribute('href', script_filename);
	css.setAttribute('type', 'text/css');

	document.getElementsByTagName('head').item(0).appendChild(css);
	return false;
}

<?php

class deviantThumbsInline {

	function init() {
		add_filter('the_content', array(__CLASS__, 'inline'));
	}

	static function inline($post) {
		$pattern = '#:thumb(\d+):#i';
		$img = '<img class="deviation" src="http://www.deviantart.com/global/getthumb.php?size=150&id=$1" />';
		$replacement = '<a href="http://www.deviantart.com/deviation/$1/">' . $img . '</a>';

		return preg_replace($pattern, $replacement, $post);
	}
}


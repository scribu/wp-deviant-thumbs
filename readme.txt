=== Deviant Thumbs ===
Contributors: scribu
Donate link: http://scribu.net/projects
Tags: deviantart, thumbs, images
Requires at least: 2.0
Tested up to: 2.6+
Stable tag: trunk

Display deviantART thumbnails on your blog.


== Description ==

Display a selection of linked thumbnails from deviantART on your WordPress blog.

Since version 1.5, the thumbs are enclosed in a **scrollable carousel**, using [jCarousel Lite](http://www.gmarwaha.com/jquery/jcarousellite/).

Can be used with or without widgets.


== Installation ==

1. Upload the `deviant-thumbs` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

= General Usage (With Widget) =

1. Go to **WP-Admin -> Design -> Widgets**.
1. Add the Deviant Thumbs widget to your sidebar.
1. Set the desired settings for the widget.
1. Click 'Save Changes'.

= Function Call (Outside WP loop) =

**Carousel**

`<?php if (function_exists('deviant_thumbs_carousel')): ?>
  <ul class="deviant-thumbs">
    <?php deviant_thumbs_carousel($query, $count = 3, $rand = FALSE, $vertical = FALSE, $cache = 6); ?>
  </ul>
<?php endif; ?>`

*Parameters:*

* *$query* is a search string used on [deviantART](http://browse.deviantart.com/). ( Example: 'by:scribu in:photography' )
* *$count* is the number of thumbs to display.
* *$rand* is a flag to randomise thumbs or not. Can be `TRUE` or `FALSE`.
* *$cache* is the number of hours after which the cache has to be rebuilt. This creates a text file in the directory of the plugin, which must be writable.

**Simple**

`<?php if (function_exists('deviant_thumbs')): ?>
  <ul class="deviant-thumbs">
    <?php deviant_thumbs($query = 'your query', $count = 5, $rand = FALSE, $cache = 6, $before = '<li>', $after = '</li>'); ?>
  </ul>
<?php endif; ?>`

*Parameters:*

* *$query* is a search string used on [deviantART](http://browse.deviantart.com/). ( Example: 'by:scribu in:photography' )
* *$count* is the number of thumbs to display. The maximum is 24.
* *$rand* is a flag to randomise thumbs or not. Can be `TRUE` or `FALSE`.
* *$cache* is the number of hours after which the cache has to be rebuilt. This creates a text file in the directory of the plugin, which must be writable.
* *$before* is a string inserted before each thumb. It can be a HTML tag or just plain text.
* *$after* is a string inserted after each thumb. It can also be a HTML tag or just plain text.


== Frequently Asked Questions ==

= How can I choose which thumbs to display? =

Please see the information about [using the main search](http://help.deviantart.com/577/) on deviantART.

= How can I modifify the carousel skin? =

You can edit 'deviant-thumbs/inc/carousel/carousel.css/'.

= Why isn't the cache working? =

Probably because the plugin can't create the folder 'deviant-thumbs/cache'. You can try to create the folder manually and set it's permissions to 757. See [Changing File Permissions](http://codex.wordpress.org/Changing_File_Permissions).

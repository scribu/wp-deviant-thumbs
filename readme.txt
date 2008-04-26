=== Deviant Thumbs ===
Contributors: scribu
Donate link: http://scribu.net/download/
Tags: deviantart, thumbs, images
Requires at least: 1.5
Tested up to: 2.5.1
Stable tag: trunk

Display DeviantArt thumbs on your blog.

== Description ==

Display a selection of linked thumbnails from DeviantArt on your WordPress blog.

Can be used with or without widgets.


== Installation ==

1. Upload the `deviant-thumbs` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.


== Usage ==

**General Usage (With Widget)**

1. Go to **WP-Admin -> Presentation -> Widgets**.
1. **Drag** the Deviant Thumbs Widget to your sidebar.
1. **Configure** the Deviant Thumbs Widget.
1. Click 'Save Changes'.

**Function Call (Outside WP loop)**

Use:

`<?php if (function_exists('deviant_thumbs')): ?>
  <ul class="deviant-thumbs">
    <?php deviant_thumbs($query = 'your query', $count = 5, $rand = FALSE, $cache = 6, $before = '<li>', $after = '</li>'); ?>
  </ul>
<?php endif; ?>`

**Parameters**

* *$query* is a search string used on [DeviantArt](http://browse.deviantart.com/). ( Example: 'by:scribu in:photography' )
* *$count* is the number of thumbs to display.
* *$rand* is a flag to randomise thumbs or not. Can be `TRUE` or `FALSE`.
* *$cache* is the number of hours after which the cache has to be rebuilt. This creates a text file in the directory of the plugin, which must be writable.
* *$before* is a string inserted before each thumb. It can be a HTML tag or just plain text.
* *$after* is a string inserted after each thumb. It can also be a HTML tag or just plain text.


== Frequently Asked Questions ==

= What options do I have for displaying thumbs? =

Please see the information about [using the main search](http://help.deviantart.com/577/) on DeviantArt.

= How many thumbs can it display? =

Any number from 1 to 24.

= Why isn't the cache working? =

Probably because the plugin folder doesn't have writting permissions. If you don't know how to set file permissions, please read this [Codex tutorial](http://codex.wordpress.org/Changing_File_Permissions).

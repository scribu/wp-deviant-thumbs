=== Plugin Name ===
Contributors: scribu
Tags: deviantart, thumbs, images
Requires at least: 1.5
Tested up to: 2.5
Stable tag: 1.2.4

Display deviation thumbs from DeviantArt on your blog.

== Description ==

Display a selection of linked thumbnails from DeviantArt into your WordPress blog.

Can be used with or without widgets.

== Installation ==

1. Upload the `deviant-thumbs` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Usage ==

**General Usage (With Widget)**

1. Go to **WP-Admin -> Presentation -> Widgets**.
1. **Drag** the Deviant Thumbs Widget to your sidebar.
1. **Configure** the Deviant Thumbs Widget by clicking on the configure icon.
1. Click 'Save Changes'.

**Function Call (Outside WP loop)**

Use:

`<?php if (function_exists('deviant_thumbs')): ?>
   <?php deviant_thumbs($query, $count, $rand, $cache); ?>
<?php endif; ?>`

**Options**

* *$query* is a search string used on http://browse.deviantart.com/
* *$count* is the number of thumbs to display. (Default: 3)
* *$rand* is a flag to randomise thumbs or not. Can be `TRUE` or `FALSE`. (Default: FALSE)
* *$cache* is the number of hours after which the cache has to be rebuilt. This creates a text file in the directory of the plugin. (Default: 3)

**Examples**

`<?php if (function_exists('deviant_thumbs')): ?>
   <?php deviant_thumbs('by:scribu in:photography', 5, TRUE); ?>
<?php endif; ?>`
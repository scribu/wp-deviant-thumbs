=== Deviant Thumbs ===
Contributors: scribu
Donate link: http://scribu.net/projects
Tags: deviantart, thumbs, images
Requires at least: 2.2
Tested up to: 2.7
Stable tag: trunk
Display deviantART thumbnails on your blog.

== Description ==

Display linked thumbnails from deviantART on your WordPress blog.

**Features:**

* **Scrollable carousel**: your thumbs can be displayed in a dA style carousel
* **Inline deviations**: the code *:thumb98765:* inside a post becomes a thumbnail, just like on dA
* **Widget support** and **flexible template tags**

Note: PHP5 is required, from version 1.6 onwards.

== Installation ==

1. Unzip and upload the `deviant-thumbs` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

= Widget Usage =

1. Add the Deviant Thumbs widget to your sidebar.
1. Set the desired settings for the widget.
1. Enjoy

= Template tags =

These are meant to be used outside The Loop.

**Thumb list**

`<?php if (function_exists('deviant_thumbs')): ?>
  <ul class="deviant-thumbs">
    <?php deviant_thumbs($query = 'your query', $args = 'rand=false&count=10'); ?>
  </ul>
<?php endif; ?>`

*Parameters:*

* **$query** is a search string used on [deviantART](http://browse.deviantart.com/). Example: *by:scribu in:photography*
* **$args** is a string of options for displaying the carousel:
* <ul>
<li>*count* is the number of thumbs to display (The maximum is 24). Default: 6</li>
<li>*rand* is a flag to randomise thumbs or not. (Can be *true* or *false*). Default: true</li>
<li>*cache* is the number of hours after which the thumb list has to be updated. Default: 6</li>
<li>*before* is a string inserted before each thumb. Default: `<li>`</li>
<li>*after* is a string inserted after each thumb. Default: `</li>`</li>
</ul>

**Carousel**

`<?php if (function_exists('deviant_thumbs_carousel')): ?>
    <?php deviant_thumbs_carousel($query = 'your query', $args = 'count=10&rand=false&id=my_carousel'); ?>
<?php endif; ?>`

*Parameters:*

* **$query** is a search string used on [deviantART](http://browse.deviantart.com/).
* **$args** is a string of options for displaying the carousel:
* <ul>
<li>*count* is the total number of thumbs. Default: 6</li>
<li>*show* is the number of visible thumbs. Default: 3</li>
<li>*rand* is an option to show thumbs in a random order (Can be *true* or *false*). Default: true</li>
<li>*cache* is the number of hours after which the thumb list has to be updated. Default: 6</li>
<li>*speed* is the speed of the sliding effect (Can be *fast*, *normal* or *slow*). Default: fast</li>
</ul>

== Frequently Asked Questions ==

= How can I choose which thumbs to display? =

You enter a search string (the thumbs are found using the search engine from dA). Read more about [search options](http://help.deviantart.com/577/) on deviantART.

= How can I modifify the carousel skin? =

You can edit `deviant-thumbs/inc/carousel/carousel.css`.

= Why isn't the cache working? =

Probably because your wp-uploads folder isn't writable. You will have to chmod it to 757. If you don't know how, read [Changing File Permissions](http://codex.wordpress.org/Changing_File_Permissions).

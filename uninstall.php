<?php
delete_option('deviant thumbs');
include_once(dirname(__FILE__) . '/deviant-thumbs.php');
deviantThumbs::clear_cache();

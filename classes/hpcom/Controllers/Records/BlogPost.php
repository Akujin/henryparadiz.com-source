<?php
namespace hpcom\Controllers\Records;

class BlogPost extends \Divergence\Controllers\RecordsRequestHandler {
	use Permissions\LoggedIn;
	
	static public $recordClass = 'hpcom\\Models\\BlogPost';
}
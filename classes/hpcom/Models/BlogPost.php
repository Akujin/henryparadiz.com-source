<?php
namespace hpcom\Models;

class BlogPost extends \Divergence\Models\Model {
	//use \Divergence\Models\Versioning;
	use \Divergence\Models\Relations;
	
	// support subclassing
	static public $rootClass = __CLASS__;
	static public $defaultClass = __CLASS__;
	static public $subClasses = [__CLASS__];


	// ActiveRecord configuration
	static public $tableName = 'blog_posts';
	static public $singularNoun = 'blogpost';
	static public $pluralNoun = 'blogposts';
	
	// versioning
	//static public $historyTable = 'test_history';
	//static public $createRevisionOnDestroy = true;
	//static public $createRevisionOnSave = true;
	
	static public $fields = [
        'Title',
        'Permalink',
        'MainContent'
	];
	
	static public $relationships = [
		/*'Positions' => array(
	    	'type' => 'one-many'
	    	,'class' => 'Pages'
	    	,'local'	=>	'ID'
	    	,'foreign' => 'BlogPostID'
	    	//,'conditions' => 'Status != "Deleted"'
	    	,'order' => array('name' => 'ASC')
	    )
	    ,*/
	];
}
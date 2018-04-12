<?php
namespace hpcom\Controllers;

use Divergence\IO\Database\MySQL as DB;

use \hpcom\Models\BlogPost as BlogPost;
use \hpcom\App as App;

class Blog extends \Divergence\Controllers\RequestHandler
{

	public static function getSidebarData() {
		return [
			'Months' => DB::AllRecords('SELECT DISTINCT MONTHNAME(`Created`) as `MonthName`,MONTH(`Created`) as `Month`, YEAR(`Created`) as `Year` FROM `blog_posts`')
		];
	}

	public static function home() {
		$BlogPosts = BlogPost::getAll([
			'order' =>  'Created DESC'
		]);
		
		return static::respond('blog/posts.tpl', [
			'BlogPosts' => $BlogPosts,
			'Sidebar' => static::getSidebarData()
		]);
	}
	
	public static function year($year) {
		$BlogPosts = BlogPost::getAllByWhere([
			sprintf('YEAR(`Created`)=%d',$year)
		],[
			'order' =>  'Created DESC'
		]);
		
		return static::respond('blog/posts.tpl', [
			'BlogPosts' => $BlogPosts,
			'Sidebar' => static::getSidebarData()
		]);
	}
	
	public static function month($year,$month) {
		$BlogPosts = BlogPost::getAllByWhere([
			sprintf('YEAR(`Created`)=%d',$year),
			sprintf('MONTH(`Created`)=%d',$month)
		],[
			'order' =>  'Created DESC'
		]);
		
		return static::respond('blog/posts.tpl', [
			'BlogPosts' => $BlogPosts,
			'Sidebar' => static::getSidebarData()
		]);
	}
	
	public static function post($year,$month,$permalink) {
		$BlogPost = BlogPost::getByWhere([
			sprintf('YEAR(`Created`)=%d',$year),
			sprintf('MONTH(`Created`)=%d',$month),
			"`permalink`='".DB::escape($permalink)."'"
		]);
		
		return static::respond('blog/post.tpl', [
			'BlogPost' => $BlogPost,
			'Sidebar' => static::getSidebarData()
		]);
	}

	public static function handleRequest()
	{
		switch($action = $action?$action:static::shiftPath())
		{	
			case 'admin':
				return Admin::handleRequest();
				
			case 'api':
				return API::handleRequest();
				
			case 'logout':
				return static::logout();
					
			case '':
				return static::home();
				break;

			case ctype_digit($action):
				// year of posts
				if(strlen($action) == 4) {
					$year = $action;
				}
				// month of posts
				if(ctype_digit(static::peekPath()) && strlen(static::peekPath()) == 2) {
					$month = static::shiftPath();
				}
				// single post
				if(static::peekPath()) {
					$permalink = static::shiftPath();
				}
				
				if(!$permalink && !$month) {
					return static::year($year);
				}
				
				if(!$permalink) {
					return static::month($year,$month);
				}
				
				return static::post($year,$month,$permalink);
				
			default:

				
				// code for tag(s)
				// [tag+[tag]]
				break;
		}
	}
	
	public static function logout() {
		if(App::$Session->CreatorID) {
			App::$Session->CreatorID = null;
			App::$Session->save();
		}
		header('Location: /blog/');
		exit;
	}
}

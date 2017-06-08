<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* Factory class
* 
* Class to handle automagicaly load of custom post type and user 
* 
* @author eqima <hasina@eqima.org>
*/
class WP_Factory
{

	/**
	 * Replace the traditionnal get_post's wordpress function
	 * 
	 * Will automaticly create a custom Object that "inherit" from WP_Post. If post type is
	 *  "custom" then you have to declare a class named "WP_Post_Custom" for it. Notice that
	 *  this class must inherit from WP_Post_Base class.
	 * 
	 * @param int the post ID
	 * @return object depend on it's post type, fallback to get_post if no longer avalaible
	 */
	public static function get_post($id)
	{
		$post_type = get_post_type($id);
		$class_name = 'WP_Post_' . ucfirst($post_type);
		
		// default to wp_post
		$the_post =  get_post( $id );

		// overrides
		if (class_exists( $class_name )) {
			if (is_subclass_of($this, 'WP_Post_Base')) {
				$the_post = new $class_name( $id );
			}else{
				throw new Exception(" $class_name must be a subclass of WP_Post_Base ", 1);
			}
		}elseif (class_exists('WP_Post_Base')) {
			$the_post = new WP_Post_Base( $id );
		}

		setup_postdata( $GLOBALS['post'] =& $the_post );

		return $the_post;
	}

	/**
	 * Replace the traditionnal get_posts's wordpress function
	 * 
	 * It work exactly the same way as get_posts but will return an array of 
	 *  custom object depend on the post type
	 * 
	 * @param array like get_posts param
	 * @return array of objects 
	 */
	public static function get_posts($params)
	{
		$posts = get_posts( $params );

		$results = array();

		foreach ($posts as $post) {
			array_push($results, self::get_post( $post->ID ));
		}

		return $results;
	}

	/**
	 * Instance a sub class of WP_User according to user role
	 * 
	 * If user role is "custom" then you have to declare a class named "WP_User_Custom" for it
	 *  Remember to extend the WP_User class.
	 * 
	 * @param int the user id
	 * @return object a custom object
	 */
	public static function get_user($id)
	{
		$user_data = get_userdata($id);
		$role = array_shift($user_data->roles);
		$class_name = 'WP_User_' . ucfirst($role);
		if (class_exists($class_name)) {
			return new $class_name( $id );
		}

		return new WP_User( $id );
	}
}
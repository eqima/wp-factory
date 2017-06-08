<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* A Pathern to simulate inheritance from final class
* 
* @author eqima <hasina@eqima.org>
*/
class WP_Post_Base
{
	private static $post;

	public function __construct($id)
	{
		self::$post = WP_Post::get_instance( $id );
		$this->configure_custom_fields();
	}

	/**
	 * Provide a ACF support
	 * 
	 * @param none
	 * @return none
	 */
	protected function configure_custom_fields()
	{
		$this->id = self::$post->ID;
		if (function_exists('get_fields')) {
			$fields = get_fields( $this->id );
			foreach ($fields as $field_name => $value) {
				if ("" !== $field_name) {
					$this->$field_name = $value;
				}
			}
		}
	}

	public function __get($name)
	{
		if(property_exists(self::$post, $name)){
			return  self::$post->$name;
		}else{
			throw new Exception("Property $name not exists");
		}
	}

	public function __call($method, $parameters)
	{
		if (method_exists(self::$post, $method)) {
			call_user_func_array(array( self::$post, $method ), $parameters);
		}else{
			throw new Exception("Method $method not exists");
		}
	}
}
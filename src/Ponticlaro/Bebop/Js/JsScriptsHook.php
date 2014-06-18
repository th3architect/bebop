<?php

namespace Ponticlaro\Bebop\Js;

use Ponticlaro\Bebop;

class JsScriptsHook extends \Ponticlaro\Bebop\Patterns\ScriptsHook {

	/**
	 * Registers a single script
	 * 
	 * @param string  $id           Script ID
	 * @param string  $file_path    Script file path
	 * @param array   $dependencies Script dependencies
	 * @param string  $version      Script version
	 * @param boolean $in_footer    If script should be loaded in the wp_footer hook
	 */
	public function register($id, $file_path, array $dependencies = array(), $version = null, $in_footer = true)
	{
		$script = new \Ponticlaro\Bebop\Js\JsScript($id, $file_path, $dependencies, $version, $in_footer);

		$this->scripts->set($id, $script);
		$this->register_list->push($id);

		return $this;
	}
}
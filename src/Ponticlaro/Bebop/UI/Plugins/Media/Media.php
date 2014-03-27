<?php

namespace Ponticlaro\Bebop\UI\Plugins;

use Ponticlaro\Bebop;

class Media extends \Ponticlaro\Bebop\UI\PluginAbstract {

	/**
	 * Identifier Key to call this plugin
	 * 
	 * @var string
	 */
	protected static $__key = 'Media';

	protected static $__base_url;

	protected $__instances;

	protected $__current_instance_key;

	public function __construct()
	{
		self::$__base_url = Bebop::getPathUrl(__DIR__);

		$this->__instances = Bebop::Collection();

		$args = func_get_args();

		if ($args) call_user_func_array(array($this, '__createInstance'), $args);
	}

	private function __createInstance($key, $data = array(), array $config = array())
	{	
		$this->__enqueueScripts();

		$label = $key;
		$key   = Bebop::util('slugify', $key);

		$default_config = array(
			'key'                  => $key,
			'field_name'           => $key,
			'data'                 => $data,
			'select_button_class'  => '',
			'select_button_text'   => 'Select '. $label,
			'remove_button_class'  => '',
			'remove_button_text'   => 'Remove '. $label,
			'no_selection_message' => 'No selected item',  
			'modal_title'          => 'Upload or select existing resources',
			'modal_button_text'    => 'Select '. $label,
			'mime_types'           => array()
		);

		$this->config   = Bebop::Collection(array_merge($default_config, $config));
		$this->template = 'single';

		return $this;
	}

	/**
	 * This function will register everything on the right hooks
	 * when the plugin is added to Bebop::UI
	 *  
	 * @return void
	 */
	public function load()
	{
		add_action('admin_enqueue_scripts', array($this, 'registerScripts'));
		add_action('admin_footer', array($this, 'renderTemplates'));
	}

	public function registerScripts()
	{
		wp_register_style('bebop-ui--media', self::$__base_url .'/assets/css/bebop-ui--media.css', array('bebop-ui'));
		
		wp_register_script('bebop-ui--mediaView', self::$__base_url .'/assets/js/views/Media.js', array(), false, true);

		$app_dependencies = array(
			'jquery',
			'jquery-ui-sortable',
			'underscore',
			'backbone',
			'bebop-ui',
			'bebop-ui--mediaView'
		);		
		wp_register_script('bebop-ui--media', self::$__base_url .'/assets/js/bebop-ui--media.js', $app_dependencies, false, true);
	}

	public function renderTemplates()
	{
		?>
		<script bebop-media--template="image-view" type="text/template" style="display:none">
			<div class="bebop-media--previewer-image">
				<div class="bebop-media--previewer-image-inner">
					<img src="{{sizes.thumbnail.url}}">
				</div>
			</div>
		</script>

		<script bebop-media--template="non-image-view" type="text/template" style="display:none">
			<div class="bebop-media--previewer-inner">
				<div class="bebop-media--previewer-icon bebop-ui-icon-file"></div>
				<div class="bebop-media--previewer-file-title">{{title}}</div>
				<div class="bebop-media--previewer-info">
					<a href="{{url}}" target="_blank">Open file in new tab</a> <span class="bebop-ui-icon-share"></span>
				</div>
			</div>
		</script>

		<script bebop-media--template="empty-view" type="text/template" style="display:none">
			<div bebop-media--action="select" title="Click to select media" class="bebop-media--previewer-inner">
				<div class="bebop-media--previewer-icon bebop-ui-icon-file-remove"></div>
				<div class="bebop-media--previewer-file-title">No file selected</div>
			</div>
		</script>

		<script bebop-media--template="error-view" type="text/template" style="display:none">
			<div class="bebop-media--previewer-inner bebop-media--status-warning">
				<div class="bebop-media--previewer-icon bebop-ui-icon-warning"></div>
				<div class="bebop-media--previewer-status-code">{{status}}</div>
				<div class="bebop-media--previewer-file-title">{{message}}</div>
			</div>
		</script>

		<script bebop-media--template="loading-view" type="text/template" style="display:none">
			<div class="bebop-media--previewer-inner">
				<div class="bebop-media--previewer-icon bebop-ui-icon-busy"></div>
				<div class="bebop-media--previewer-file-title">Loading...</div>
			</div>
		</script>
	<?php }

	private function __enqueueScripts()
	{
		global $wp_version;

		if (version_compare($wp_version, '3.5', '>=')) {
			
			wp_enqueue_media();
			
		} else {

			// Handle WordPress lower than 3.5
		}

		wp_enqueue_style('bebop-ui--media');
		wp_enqueue_script('bebop-ui--media');
	}

	private function __renderTemplate($template_name, $data)
	{
		include __DIR__ . '/templates/'. $template_name .'.php';
	}

	public function render()
	{
		$this->__renderTemplate($this->template, $this->config);

		return $this;
	}
}
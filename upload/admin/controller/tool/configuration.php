<?php
class ControllerToolConfiguration extends Controller {
	private $error = array();
	private $_name = 'configuration';

	public function index() {
		$this->language->load('tool/' . $this->_name);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('text_home'),
			'href'		=> $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'		=> $this->language->get('heading_title'),
			'href'		=> $this->url->link('tool/' . $this->_name, 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_on'] = $this->language->get('text_on');
		$this->data['text_off'] = $this->language->get('text_off');
		$this->data['text_system_core'] = $this->language->get('text_system_core');
		$this->data['text_version'] = sprintf($this->language->get('text_version'), VERSION);
		$this->data['text_system_name'] = $this->language->get('text_system_name');
		$this->data['text_revision'] = sprintf($this->language->get('text_revision'), REVISION);
		$this->data['text_theme'] = $this->language->get('text_theme');
		$this->data['text_timezone'] = $this->language->get('text_timezone');
		$this->data['text_phptime'] = $this->language->get('text_phptime');
		$this->data['text_dbtime'] = $this->language->get('text_dbtime');
		$this->data['text_dbname'] = $this->language->get('text_dbname');
		$this->data['text_dbengine'] = $this->language->get('text_dbengine');
		$this->data['text_store_info'] = $this->language->get('text_store_info');
		$this->data['text_setting_info'] = $this->language->get('text_setting_info');
		$this->data['text_server_info'] = $this->language->get('text_server_info');

		$this->data['tab_store'] = $this->language->get('tab_store');
		$this->data['tab_setting'] = $this->language->get('tab_setting');
		$this->data['tab_server'] = $this->language->get('tab_server');

		$this->data['column_php'] = $this->language->get('column_php');
		$this->data['column_extension'] = $this->language->get('column_extension');
		$this->data['column_directories'] = $this->language->get('column_directories');
		$this->data['column_required'] = $this->language->get('column_required');
		$this->data['column_current'] = $this->language->get('column_current');
		$this->data['column_status'] = $this->language->get('column_status');

		$this->data['text_phpversion'] = $this->language->get('text_phpversion');
		$this->data['text_registerglobals'] = $this->language->get('text_registerglobals');
		$this->data['text_magicquotes'] = $this->language->get('text_magicquotes');
		$this->data['text_fileuploads'] = $this->language->get('text_fileuploads');
		$this->data['text_autostart'] = $this->language->get('text_autostart');

		$this->data['text_mysql'] = $this->language->get('text_mysql');
		$this->data['text_gd'] = $this->language->get('text_gd');
		$this->data['text_curl'] = $this->language->get('text_curl');
		$this->data['text_mcrypt'] = $this->language->get('text_mcrypt');
		$this->data['text_zlib'] = $this->language->get('text_zlib');
		$this->data['text_zip'] = $this->language->get('text_zip');
		$this->data['text_mbstring'] = $this->language->get('text_mbstring');
		$this->data['text_mbstring_note'] = $this->language->get('text_mbstring_note');

		$this->data['button_close'] = $this->language->get('button_close');

		$this->data['token'] = $this->session->data['token'];

		$this->data['close'] = $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL');

		// Template
		$this->data['templates'] = array();

		$directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);

		foreach ($directories as $directory) {
			$this->data['templates'][] = basename($directory);
		}

		if (isset($this->request->post['config_template'])) {
			$this->data['config_template'] = $this->request->post['config_template'];
		} else {
			$this->data['config_template'] = $this->config->get('config_template');
		}

		// Time
		$date_timezone = ini_get('date.timezone');
		$default_timezone = date_default_timezone_get();

		if ($date_timezone) {
			$this->data['server_zone'] = $date_timezone;
		} elseif ($default_timezone) {
			$this->data['server_zone'] = $default_timezone;
		} else {
			$this->data['server_zone'] = $this->language->get('text_no_timezone');
		}

		$this->data['server_time'] = date('Y-m-d H:i:s');
		$this->data['database_time'] = $this->db->query("SELECT NOW() AS now")->row['now'];

		// Database
		$database_name = DB_DRIVER;

		if ($database_name == 'mysql') {
			$this->data['database_name'] = 'MySQL';
		} elseif ($database_name == 'mysqli') {
			$this->data['database_name'] = 'MySQLi';
		} elseif ($database_name == 'mpdo') {
			$this->data['database_name'] = 'PDO (MySQL)';
		} elseif ($database_name == 'pgsql') {
			$this->data['database_name'] = 'PgSQL';
		} elseif ($database_name == 'mssql') {
			$this->data['database_name'] = 'MsSQL';
		} else {
			$this->data['database_name'] = 'SQL';
		}

		// Engines
		$this->load->model('tool/database');

		$engines = $this->model_tool_database->getEngines();

		foreach ($engines as $engine) {
			if ($engine == 'InnoDB') {
				$this->data['engine'] = true;
			} else {
				$this->data['engine'] = false;
			}
		}

		$this->data['text_innodb'] = 'InnoDB';
		$this->data['text_myisam'] = 'MyISAM';

		// Check install directory exists
		if (is_dir(dirname(DIR_APPLICATION) . '/install')) {
			$this->data['error_install'] = $this->language->get('error_install');
		} else {
			$this->data['error_install'] = '';
		}

		// Check write permissions
		$this->data['cache'] = DIR_SYSTEM . 'cache';
		$this->data['logs'] = DIR_SYSTEM . 'logs';
		$this->data['upload'] = DIR_SYSTEM . 'upload';
		$this->data['download'] = DIR_DOWNLOAD;
		$this->data['image'] = DIR_IMAGE;
		$this->data['image_cache'] = DIR_IMAGE . 'cache';
		$this->data['image_data'] = DIR_IMAGE . 'data';

		// VQMod folders
		$this->data['vqmod'] = DIR_VQMOD;
		$this->data['vqlogs'] = DIR_VQMOD . 'logs';
		$this->data['vqcache'] = DIR_VQMOD . 'vqcache';
		$this->data['vqmod_xml'] = DIR_VQMOD . 'xml';

		// Server
		$server_responses = array();

		$server_requests = array(
			'PHP_SELF',
			'GATEWAY_INTERFACE',
			'SERVER_ADDR',
			'SERVER_NAME',
			'SERVER_SOFTWARE',
			'SERVER_PROTOCOL',
			'DOCUMENT_ROOT',
			'HTTP_ACCEPT',
			'HTTP_ACCEPT_CHARSET',
			'HTTP_ACCEPT_ENCODING',
			'HTTP_ACCEPT_LANGUAGE',
			'HTTP_CONNECTION',
			'HTTP_HOST',
			'HTTPS',
			'SCRIPT_FILENAME',
			'SERVER_ADMIN',
			'SERVER_PORT'
		);

		foreach ($server_requests as $argument) {
			if (isset($_SERVER[$argument])) {
				$response = $_SERVER[$argument];
			} else {
				$response = '';
			}

			$this->data['server_responses'][] = array(
				'request'		=> $argument,
				'response'	=> $response
			);
		}

		$this->template = 'tool/' . $this->_name . '.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
}
?>
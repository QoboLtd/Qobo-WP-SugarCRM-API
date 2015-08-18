<?php
/*
Plugin Name: Qobo SugarCRM - API
Plugin URI: http://www.qobo.biz
Description: Integration with SugarCRM API
Author: Qobo ltd
Version: 1.0.0
Author URI: http://www.qobo.biz
*/

define('QBSCRMA__TEXT_DOMAIN', 'qbsccrma');
define('QBSCRMA__PLUGIN_FILE', __FILE__);
define('QBSCRMA__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('QBSCRMA__PLUGIN_SRC_DIR', plugin_dir_path( __FILE__ ) . 'src' . DIRECTORY_SEPARATOR);
define('QBSCRMA__PLUGIN_SUGARCRM_DIR', plugin_dir_path( __FILE__ ) . 'src' . DIRECTORY_SEPARATOR . 'SugarCRM' . DIRECTORY_SEPARATOR);
require_once (QBSCRMA__PLUGIN_SRC_DIR.'qbscrma_settings.php');
require_once (QBSCRMA__PLUGIN_SRC_DIR.'qbscrma_api.php');
require_once (QBSCRMA__PLUGIN_SUGARCRM_DIR.'SugarCRM_API.php');


new QBSCRMA_Settings();

<?php

require_once (QBSCRMA__PLUGIN_SRC_DIR.'qbscrma_settings.php');
require_once (QBSCRMA__PLUGIN_SUGARCRM_DIR.'SugarCRM_API.php');

class QBSCRMA_API extends SugarCRM_API {
    
    public function __construct() {
        $settings = static::get_api_settings();
        $session_id = static::get_api_session();
        
        $this->set_url($settings['ws_url']);
        $this->set_username($settings['ws_username']);
        $this->set_password($settings['ws_password']);
        $this->set_session_id($session_id?:static::login($this->get_url(), $this->get_username(), $this->get_password()));
    }
       
    public function set_session_id($session_id) {
        if ($this->session_id != $session_id) {
            if ($this->session_id) {
                update_option(QBSCRMA_Settings::OPTION_NAME_API_SESSION, $session_id);
            }
            else {
                add_option(QBSCRMA_Settings::OPTION_NAME_API_SESSION, $session_id);
            }
        }
        $this->session_id = $session_id;
    }
    
    public static function get_api_settings() {
        return get_option(QBSCRMA_Settings::OPTION_NAME_API);
    }
    
    public static function get_api_session() {
        return get_option(QBSCRMA_Settings::OPTION_NAME_API_SESSION);
    }
}

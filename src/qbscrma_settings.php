<?php
class QBSCRMA_Settings {
    const OPTION_NAME_API_SESSION = 'qbscrma-api-session';
    const OPTION_NAME_API = 'qbscrma-api';
    protected $data_api = array(
        'ws_url' => null,
        'ws_username' => null,
        'ws_password' => null,
    );
    
    public function __construct() {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'add_page'));
        register_activation_hook(QBSCRMA__PLUGIN_FILE, array($this, 'activate'));
    }
    
    public function admin_init() {
        register_setting('api_list_options', static::OPTION_NAME_API, array($this, 'validate'));
    }
    
    public function validate($input) {
        $valid = array();
        $valid['ws_url'] = sanitize_text_field($input['ws_url']);
        $valid['ws_username'] = sanitize_text_field($input['ws_username']);
        $valid['ws_password'] = sanitize_text_field($input['ws_password']);
        
        //API
        if (strlen($valid['ws_url']) == 0) {
            add_settings_error(
                'ws_url',
                'ws_url_texterror',
                _e("Please enter a 'URL' for 'API'", QBSCRMA__TEXT_DOMAIN),
                'error'
            );
            $valid['ws_url'] = $this->data['ws_url'];
        }
        if (strlen($valid['ws_username']) == 0) {
            add_settings_error(
                'ws_username',
                'ws_username_texterror',
                _e("Please enter a 'Username' for 'API'", QBSCRMA__TEXT_DOMAIN),
                'error'
            );
            $valid['ws_username'] = $this->data['ws_username'];
        }
        if (strlen($valid['ws_password']) == 0) {
            add_settings_error(
                'ws_password',
                'ws_password_texterror',
                _e("Please enter a 'Password' for 'API'", QBSCRMA__TEXT_DOMAIN),
                'error'
            );
            $valid['ws_password'] = $this->data['ws_password'];
        }
      
        return $valid;
    }
    
    public function add_page() {
        add_options_page('Qobo SugarCRM API', 'Qobo SugarCRM API', 'manage_options', 'api_list_options', array($this, 'options_do_page'));
    }
    
    public function options_do_page() {
        $options = get_option(static::OPTION_NAME_API);
        ?>
        <div class="wrap">
            <h2><?php _e('Qobo SugarCRM API', QBSCRMA__TEXT_DOMAIN) ?></h2>
            <p><?php _e('Settings for integration with SugarCRM API', QBSCRMA__TEXT_DOMAIN) ?></p>
            <form method="post" action="options.php">
                <?php settings_fields('api_list_options'); ?>
                
                <h3><?php _e('API') ?></h3>
                <p></p>
                <table class="form-table">
                    <tr valign="top"><th scope="row"><?php _e('URL', QBSCRMA__TEXT_DOMAIN) ?></th>
                        <td><input type="text" name="<?php echo static::OPTION_NAME_API?>[ws_url]" value="<?php echo $options['ws_url']; ?>" /></td>
                    </tr>
                    <tr valign="top"><th scope="row"><?php _e('Username', QBSCRMA__TEXT_DOMAIN) ?></th>
                        <td><input type="text" name="<?php echo static::OPTION_NAME_API?>[ws_username]" value="<?php echo $options['ws_username']; ?>" /></td>
                    </tr>
                    <tr valign="top"><th scope="row"><?php _e('Password', QBSCRMA__TEXT_DOMAIN) ?></th>
                        <td><input type="password" name="<?php echo static::OPTION_NAME_API?>[ws_password]" value="<?php echo $options['ws_password']; ?>" /></td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes', QBSCRMA__TEXT_DOMAIN) ?>" />
                </p>
            </form>
        </div>
        <?php
    }
    
    public function activate() {
        update_option(static::OPTION_NAME_API, $this->data_api);
    }
    
    public function deactivate() {
        delete_option(static::OPTION_NAME_API);
    }
}

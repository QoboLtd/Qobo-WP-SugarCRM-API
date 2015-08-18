<?php
class SugarCRM_API {
    const ERROR_DEFS_INVALID_SESSION = invalid_session;
    //extracted from SugarCRM instance, SoapErrorDefinitions
    static $error_defs = array(
        'no_error'=>array('number'=>0 , 'name'=>'No Error', 'description'=>'No Error'),
        'invalid_login'=>array('number'=>10 , 'name'=>'Invalid Login', 'description'=>'Login attempt failed please check the username and password'),
        'invalid_session'=>array('number'=>11 , 'name'=>'Invalid Session ID', 'description'=>'The session ID is invalid'),
        'user_not_configure'=>array('number'=>12 , 'name'=>'User Not Configured', 'description'=>'Please log into your instance of SugarCRM to configure your user. '),
        'no_portal'=>array('number'=>12 , 'name'=>'Invalid Portal Client', 'description'=>'Portal Client does not have authorized access'),
        'no_module'=>array('number'=>20 , 'name'=>'Module Does Not Exist', 'description'=>'This module is not available on this server'),
        'no_file'=>array('number'=>21 , 'name'=>'File Does Not Exist', 'description'=>'The desired file does not exist on the server'),
        'no_module_support'=>array('number'=>30 , 'name'=>'Module Not Supported', 'description'=>'This module does not support this feature'),
        'no_relationship_support'=>array('number'=>31 , 'name'=>'Relationship Not Supported', 'description'=>'This module does not support this relationship'),
        'no_access'=>array('number'=>40 , 'name'=>'Access Denied', 'description'=>'You do not have access'),
        'duplicates'=>array('number'=>50 , 'name'=>'Duplicate Records', 'description'=>'Duplicate records have been found. Please be more specific.'),
        'no_records'=>array('number'=>51 , 'name'=>'No Records', 'description'=>'No records were found.'),
        'cannot_add_client'=>array('number'=>52 , 'name'=>'Cannot Add Offline Client', 'description'=>'Unable to add Offline Client.'),
        'client_deactivated'=>array('number'=>53 , 'name'=>'Client Deactivated', 'description'=>'Your Offline Client instance has been deactivated.  Please contact your Administrator in order to resolve.'),
        'sessions_exceeded'=>array('number'=>60 , 'name'=>'Number of sessions exceeded.'),
        'upgrade_client'=>array('number'=>61 , 'name'=>'Upgrade Client', 'description'=>'Please contact your Administrator in order to upgrade your Offline Client'),
        'no_admin' => array('number' => 70, 'name' => 'Admin credentials are required', 'description' => 'The logged-in user is not an administrator'),
        'custom_field_type_not_supported' => array('number' => 80, 'name' => 'Custom field type not supported', 'description' => 'The custom field type you supplied is not currently supported'),
        'custom_field_property_not_supplied' => array('number' => 81, 'name' => 'Custom field property not supplied', 'description' => 'You are missing one or more properties for the supplied custom field type'),
        'resource_management_error' => array('number'=>90, 'name'=>'Resource Management Error', 'description'=>'The resource query limit specified in config.php has been exceeded during execution of the SOAP method'),
        'invalid_call_error' => array('number'=>1000, 'name'=>'Invalid call for this module', 'description'=>'This is an invalid call for this module. Please look at WSDL file for details'),
        'invalid_data_format' => array('number'=>1001, 'name'=>'Invalid data sent', 'description'=>'The data sent for this function is invalid. Please look at WSDL file for details'),
        'invalid_set_campaign_merge_data' => array('number'=>1005, 'name'=>'Invalid set_campaign_merge data', 'description'=>'set_campaign_merge: Merge action status will not be updated, because, campaign_id is null or no targets were selected'),
        'password_expired'     => array('number'=>1008, 'name'=> 'Password Expired', 'description'=>'Your password has expired. Please provide a new password.'),
        'lockout_reached'     => array('number'=>1009, 'name'=> 'Password Expired', 'description'=>'You have been locked out of the Sugar application and cannot log in using existing password. Please contact your Administrator.'),
        'ldap_error' => array('number'=>1012, 'name'=> 'LDAP Authentication Failed', 'description'=>'LDAP Authentication failed but supplied password was already encrypted.'),
    );
    
    var $url;
    var $username;
    var $password;
    var $session_id;
    
    public function __construct($url, $username, $password, $session_id=null) {
        $this->set_url($url);
        $this->set_username($username);
        $this->set_password($password);
        $this->set_session_id($session_id?:static::login($url, $username, $password));
    }
    
    public function get_url() {
        return $this->url;
    }
    
    public function set_url($url) {
        $this->url = $url;
    }
    
    public function get_username() {
        return $this->username;
    }
    
    public function set_username($username) {
        $this->username = $username;
    }
    
    public function get_password() {
        return $this->password;
    }
    
    public function set_password($password) {
        $this->password = $password;
    }
    
    public function get_session_id() {
        return $this->session_id;
    }
    
    public function set_session_id($session_id) {
        $this->session_id = $session_id;
    }
    
    /**
     * Makes a cURL request and returns parsed SugarCRM response in array format 
     *
     * @param string $method
     * @param array $parameters
     * @param string $url
     * @return array
     */
    public static function call($method, $parameters, $url) {
        $result = null;
        
        ob_start();
        $curl_request = curl_init();
        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_POST, 1);
        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
        
        $jsonEncodedData = json_encode($parameters);
        $post = array(
            'method' => $method,
            'input_type' => 'JSON',
            'response_type' => 'JSON',
            'rest_data' => $jsonEncodedData
        );
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
        
        $result = curl_exec($curl_request);
        curl_close($curl_request);
        $result = explode("\r\n\r\n", $result, 2);
        $result = json_decode($result[1], TRUE, 512);
        ob_end_flush();
        
        return $result;
    }
    
    /**
     * Logins to SugarCRM and returns session id
     * 
     * @param string $username
     * @param string $password
     * @param string $url
     * @return string
     */
    public static function login($url, $username, $password) {
        $session_id = null;
           
        $parameters = array(
            'user_auth' => array(
                'user_name' => $username,
                'password' => $password,
                'version' => '1',
            ),
            'application_name' => 'RestTest',
            'name_value_list' => array(),
        );
    
        $ws_return = static::call('login', $parameters, $url);
        
        if ($ws_return && isset($ws_return['id'])) {
            $session_id = $ws_return['id'];
        }
        
        return $session_id;
    }
    
    /**
     * Checks response if session has expired
     *
     * @param array $ws_return
     * @return string
     */
    public static function check_session_expired($ws_return) {
        $error_defs_invalid_session = static::$error_defs[static::ERROR_DEFS_INVALID_SESSION];
        if (isset($ws_return['name']) && $ws_return['name']==$error_defs_invalid_session['name']
            && isset($ws_return['number']) && $ws_return['number']==$error_defs_invalid_session['number']) {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Handle call to SugarCRM
     *
     * @param string $method
     * @param array $parameters
     * @return array
     */
    public function handle_call($method, $parameters) {
        $result = null;
        
        $url = $this->get_url();
        $username = $this->get_username();
        $password = $this->get_password();
        $session_id = $this->get_session_id();
        
        //add session id at the start parameters array
        $parameters = array_merge(array('session' => $session_id), $parameters);
        
        //call API method
        $ws_return = self::call($method, $parameters, $url);
    
        //if session has expired, login again and call method again
        if ($ws_return && static::check_session_expired($ws_return)) {
            $this->set_session_id(self::login($url, $username, $password));
            $parameters['session'] = $this->get_session_id();
            $ws_return = self::call($method, $parameters, $url);
        }
        if ($ws_return && isset($ws_return['name'])
            && isset($ws_return['number']) && $ws_return['number']==-1) {
            $ws_return = null;
        }
    
        return $ws_return;
    }
    
    /**
     * Retrieves a single bean based on record ID
     * 
     * @return array
     */
    public function get_entry($module_name, $id, array $select_fields=null, array $link_name_to_fields_array=null, $track_view=true){
        $method = 'get_entry';
        $parameters = array(
            'module_name' => $module_name,
            'id' => $id,
            'select_fields' => $select_fields,
            'link_name_to_fields_array' => $link_name_to_fields_array,
            'track_view' => $track_view,
        );
    
        return $this->handle_call($method, $parameters);
    }
    
    /**
     * Retrieves a list of beans based on query specifications
     * 
     * @return array
     */
    public function get_entry_list($module_name, $query=null, $order_by=null, $offset=0,
        array $select_fields=null, array $link_name_to_fields_array=null, $max_results=0,
        $deleted=0, $favorites=false){
        $method = 'get_entry_list';
        $parameters = array(
            'module_name' => $module_name,
            'query' => $query,
            'order_by' => $order_by,
            'offset' => $offset,
            'select_fields' => $select_fields,
            'link_name_to_fields_array' => $link_name_to_fields_array,
            'max_results' => $max_results,
            'deleted' => $deleted,
            'favorites' => $favorites,
        );
    
        return $this->handle_call($method, $parameters);
    }
    
    /**
     * Creates or updates a specific record
     * 
     * @return array
     */
    public function set_entry($module_name, array $name_value_list=null){
        $method = 'set_entry';
        $parameters = array(
            'module_name' => $module_name,
            'name_value_list' => $name_value_list,
        );
    
        return $this->handle_call($method, $parameters);
    }
    
    
    /**
     * Creates or updates a list of records
     * 
     * @return array
     */
    public function set_entries($module_name, array $name_value_lists=null){
        $method = 'set_entries';
        $parameters = array(
            'module_name' => $module_name,
            'name_value_lists' => $name_value_lists,
        );
    
        return $this->handle_call($method, $parameters);
    }
    
    /**
     * Creates a new document revision for a specific document record
     * 
     * @return array
     */
    public function set_document_revision($id, $file, $filename, $revision){
        $method = 'set_document_revision';
        $parameters = array(
            'note' => array(
                'id' => $id,
                'file' => base64_encode($file),
                'filename' => $filename,
                'revision' => $revision,
            ),
        );
    
        return $this->handle_call($method, $parameters);
    }
    
    /**
     * Sets relationships between two records. You can relate multiple records to a single record using this
     * 
     * @return array
     */
    public function set_relationship($module_name, $module_id, $link_field_name, array $related_ids, array $name_value_list=null,
        $delete=0){
        $method = 'set_relationship';
        $parameters = array(
            'module_name' => $module_name,
            'module_id' => $module_id,
            'link_field_name' => $link_field_name,
            'related_ids' => $related_ids,
            'name_value_list' => $name_value_list,
            'delete' => $delete,
        );
    
        return $this->handle_call($method, $parameters);
    }
    
    /**
     * Sets multiple relationships between multiple record sets
     * 
     * @return array
     */
    public function set_relationships(array $module_names, array $module_ids, array $link_field_names, array $related_ids,
        array $name_value_lists=null, array $delete_array=null){
        $method = 'set_relationships';
        $parameters = array(
            'module_names' => $module_names,
            'module_ids' => $module_ids,
            'link_field_names' => $link_field_names,
            'related_ids' => $related_ids,
            'name_value_lists' => $name_value_lists,
            'delete_array' => $delete_array,
        );
    
        return $this->handle_call($method, $parameters);
    }

}

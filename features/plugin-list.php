<?php
add_action( 'rest_api_init', 'csm_plugins_register_rest_routes' );
function csm_plugins_register_rest_routes() {
    $controller = new CSM_Plugins_Custom_Route();
    $controller->register_routes();
}

class CSM_Plugins_Custom_Route extends WP_REST_Controller {

    public function register_routes() {
        register_rest_route( CSM_PLUGIN_NAMESPACE, 'plugins', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'get_data' ),
                'permission_callback' => array( $this, 'get_data_permissions_check' ),
                'args'                => array(
                    'csmkey' => array(
                        'default' => '',
                      ),
                ),
            )
        ) );
    }

    public function get_data( $request ) {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
    
        if ( ! function_exists( 'plugins_api' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        }
    
        $plugin_array = array();
        $plugin_data = get_plugins();
        if($plugin_data) {
            foreach($plugin_data as $key => $plugin) {
                $version_latest='-';
                $args = array(
                    'slug' => $plugin['TextDomain'],
                    'fields' => array(
                        'version' => true,
                    )
                );
    
                $call_api = plugins_api( 'plugin_information', $args );
                /** Check for Errors & Display the results */
                if ( is_wp_error( $call_api ) ) {							
                    $api_error = $call_api->get_error_message();
                } else {
                    if ( ! empty( $call_api->version ) ) {
                        $version_latest = $call_api->version;
                    }
                }
    
                $status = 'Inactive';
                if(is_plugin_active($key)) {
                    $status = "Active";
                }
    
                if($version_latest == $plugin['Version'] || $version_latest == '-') {
                    $update_available = 'No';
                    $new_version = '-';
                } else {
                    $update_available = 'Yes';
                    $new_version = $version_latest;
                }
    
                $plugin_array[] = array(
                    'name' => $plugin['Name'],
                    'desc' => $plugin['Description'],
                    'author' => $plugin['Author'],
                    'status' => $status,
                    'version' => $plugin['Version'],
                    'update' => $update_available,
                    'new_version' => $new_version,
                );
            }
        }

        $response = new WP_REST_Response( $plugin_array, 200 );
        return $response;
    }

    public function get_data_permissions_check( $request ) {
        $key = $request->get_header('csmkey');
        return get_key_check( $key );
    }
}
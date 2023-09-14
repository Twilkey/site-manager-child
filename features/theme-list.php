<?php
add_action( 'rest_api_init', 'csm_theme_register_rest_routes' );
function csm_theme_register_rest_routes() {
    $controller = new CSM_Theme_Custom_Route();
    $controller->register_routes();
}

class CSM_Theme_Custom_Route extends WP_REST_Controller {

    public function register_routes() {
        register_rest_route( CSM_PLUGIN_NAMESPACE, 'theme', array(
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
        $theme_data = wp_get_theme();
        $theme = array(
            'name' => $theme_data->get( 'Name' ),
            'desc' => $theme_data->get( 'Description' ),
            'author' => $theme_data->get( 'Author' ),
            'isparent' => ($theme_data->parent() ? 'No' : 'Yes'),
            'version' => $theme_data->get( 'Version' ),
        );

        $response = new WP_REST_Response( $theme, 200 );
        return $response;
    }

    public function get_data_permissions_check( $request ) {
        $key = $request->get_header('csmkey');
        return get_key_check( $key );
    }
}
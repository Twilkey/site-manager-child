<?php
add_action( 'rest_api_init', 'csm_users_register_rest_routes' );
function csm_users_register_rest_routes() {
    $controller = new CSM_Users_Custom_Route();
    $controller->register_routes();
}

class CSM_Users_Custom_Route extends WP_REST_Controller {

    public function register_routes() {
        register_rest_route( CSM_PLUGIN_NAMESPACE, 'users', array(
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
        $users = get_users();
        $users_array = array();
        $postTypes = get_post_types();
        if($users) {
            foreach ( $users AS $user ) {
                $users_array[] = array(
                    'user_id' => esc_html( $user->ID ),
                    'display_name' => esc_html( $user->display_name ),
                    'display_name' => esc_html( $user->display_name ),
                    'user_email' => esc_html( $user->user_email ),
                    'user_login' => esc_html( $user->user_login ),
                    'postcount' => count_user_posts($user->ID, $postTypes),
                    'roles' => $user->roles,
                );
            }
        }

        $response = new WP_REST_Response( $users_array, 200 );
        return $response;
    }

    public function get_data_permissions_check( $request ) {
        $key = $request->get_header('csmkey');
        return get_key_check( $key );
    }
}
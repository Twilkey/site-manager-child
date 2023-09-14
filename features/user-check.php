<?php
add_action( 'rest_api_init', 'csm_user_check_register_rest_routes' );
function csm_user_check_register_rest_routes() {
    $controller = new CSM_User_Check_Custom_Route();
    $controller->register_routes();
}

class CSM_User_Check_Custom_Route extends WP_REST_Controller {

    public function register_routes() {
        register_rest_route( CSM_PLUGIN_NAMESPACE, 'user-exists', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'get_data' ),
                'permission_callback' => array( $this, 'get_data_permissions_check' ),
                'args'                => array(
                    'csmkey' => array(
                        'default' => '',
                    ),
                    'username' => array(
                        'default' => '',
                    ),
                    'useremail' => array(
                        'default' => '',
                    ),
                ),
            )
        ) );
    }

    public function get_data( $request ) {
        $username = $request->get_header('username');
        $useremail = $request->get_header('useremail');

        $result = array();
        if(!is_null($username) && !empty($username)) {
            $user_by_username = get_user_by('login', $username);

            $username_obj = $user_by_username;
            if($user_by_username) {
                $username_obj_meta = false;
                if($user_by_username->ID) {
                    $username_obj_meta = get_user_meta($user_by_username->ID);
                }

                $username_obj = (object) [
                    'ID' => $user_by_username->ID,
                    'username' => $user_by_username->user_login,
                    'email' => $user_by_username->user_email,
                    'roles' => $user_by_username->roles,
                    'firstname' => $username_obj_meta ? $username_obj_meta['first_name'] : '',
                    'lastname' => $username_obj_meta ? $username_obj_meta['last_name'] : ''
                ];
            }
            $result['username'] = $username_obj;
        } else {
            $result['username'] = 'No username was supplied';
        }
        
        if(!is_null($useremail) && !empty($useremail)) {
            $user_by_email = get_user_by('email', $useremail);

            $useremail_obj = false;
            if($user_by_email) {
                $useremail_obj_meta = false;
                if($user_by_email) {
                    $useremail_obj_meta = get_user_meta($user_by_email->ID);
                }

                $useremail_obj = (object) [
                    'ID' => $user_by_email->ID,
                    'username' => $user_by_email->user_login,
                    'email' => $user_by_email->user_email,
                    'roles' => $user_by_email->roles,
                    'firstname' => $useremail_obj_meta ? $useremail_obj_meta['first_name'] : '',
                    'lastname' => $useremail_obj_meta ? $useremail_obj_meta['last_name'] : ''
                ];
            }
            $result['useremail'] = $useremail_obj;
        } else {
            $result['useremail'] = 'No email was supplied';
        }

        if(
            is_object($user_by_username) && 
            is_object($user_by_email) && 
            $user_by_username->ID == $user_by_email->ID
        ) {
            $result['same'] = $username_obj;
        } else {
            $result['same'] = false;
        }

        $response = new WP_REST_Response( $result, 200 );
        return $response;
    }

    public function get_data_permissions_check( $request ) {
        $key = $request->get_header('csmkey');
        return get_key_check( $key );
    }
}
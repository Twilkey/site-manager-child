<?php
add_action( 'admin_menu', 'csm_menu' );
function csm_menu() {
    add_submenu_page(
		'options-general.php', // parent page slug
		'CSMC Options',
		'CSMC Options',
		'manage_options',
		'cdo',
		'csm_options_page'
	);
}

add_action( 'admin_init', 'csm_settings_fields' );
function csm_settings_fields(){

	$page_slug = 'cdo';
	$option_group = 'csm_settings';

	add_settings_section(
		'csm_section_1', // section ID
		'', // title (optional)
		'csm_section_callback', // callback function to display the section (optional)
		$page_slug
	);

	register_setting( $option_group, 'csm_secret_key', 'sanitize_text_field' );

	add_settings_field(
		'csm_secret_key',
		'Secret Key',
		'csm_secret_key', // function to print the field
		$page_slug,
		'csm_section_1' // section ID
	);

}

function csm_section_callback( $args ) {

}

function csm_secret_key( $args ) {
	$value = get_option( 'csm_secret_key' );
	?>
		<label>
			<input type="text" name="csm_secret_key" value="<?php echo esc_attr($value); ?>" />
		</label>
	<?php
}

function csm_options_page(){
	?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title() ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'csm_settings' ); // settings group name
					do_settings_sections( 'cdo' ); // just a page slug
					submit_button(); // "Save Changes" button
				?>
			</form>
		</div>
	<?php
}
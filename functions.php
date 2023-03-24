<?php
function woffice_child_scripts()
{
	if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
		$theme_info = wp_get_theme();
		wp_enqueue_style('woffice-child-stylesheet', get_stylesheet_uri(), array(), WOFFICE_THEME_VERSION);
	}

	if (is_rtl()) {
		wp_enqueue_style('woffice-child-rtl', get_template_directory_uri() . '/rtl.css', array(), WOFFICE_THEME_VERSION);
	}
}
add_action('wp_enqueue_scripts', 'woffice_child_scripts', 30);

add_action('after_setup_theme', function () {

	// Load custom translation file for the parent theme
	load_theme_textdomain('woffice', get_stylesheet_directory() . '/languages/');

	// Load translation file for the child theme
	load_child_theme_textdomain('woffice', get_stylesheet_directory() . '/languages');
});

//ajouter un role  et lui déterminer des roles specifiques 
function drh_add_custom_role()
{
	if (!get_option('custom_roles')) {
		$capabilities = array(
			'edit_posts' => true,
			'upload_files' => true,
			'manage_options' => true
		);
		add_role('drh', 'DRH', $capabilities);
		update_option('custom_roles', 1);
	}
}
add_action('init', 'drh_add_custom_role');
//supprimer un role 
// remove_role('toto'); // nom utilisé dans le role

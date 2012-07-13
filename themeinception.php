<?php
/*
Plugin Name: Themeinception
Description: A plugin that lets you quickly and easily create new themes. Plugin created with pluginception :P
Version: 1.0
Author: Ben Lobaugh
Author URI: http://ben.lobaugh.net
*/

// Load the textdomain
add_action('init', 'themeinception_load_textdomain');
function themeinception_load_textdomain() {
	load_plugin_textdomain('themeinception', false, dirname(plugin_basename(__FILE__)));
}


add_action('admin_menu', 'themeinception_admin_add_page');
function themeinception_admin_add_page() {
	add_theme_page(__('Create a New Theme','themeinception'), __('Create a New Theme','themeinception'), 'edit_plugins', 'themeinception', 'themeinception_options_page');
}

function themeinception_options_page() {
	$results = themeinception_create_theme();
	
	if ( $results === true ) return;
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e('Create a New Theme','themeinception'); ?></h2>
		<?php settings_errors(); ?>
		<form method="post" action="">
		<?php wp_nonce_field('themeinception_nonce'); ?>
		<table class="form-table">
		<?php
		$opts = array(
			'name'    => __('Theme Name', 'themeinception'),
			'uri'     => __('Theme URI (optional)', 'themeinception'),
			'description'   => __('Description (optional)', 'themeinception'),
			'version'       => __('Version (optional)', 'themeinception'),
			'author'        => __('Author (optional)', 'themeinception'),
			'author_uri'    => __('Author URI (optional)', 'themeinception'),
			'license'       => __('License (optional)', 'themeinception'),
			'license_uri'   => __('License URI (optional)', 'themeinception'),
                        'tags'          => __('Tags [comma seperated] (optional)', 'themeinception')
		);

		foreach ($opts as $slug=>$title) {
			$value = '';
			if (!empty($results['themeinception_'.$slug])) $value = esc_attr($results['themeinception_'.$slug]);
			echo "<tr valign='top'><th scope='row'>{$title}</th><td><input class='regular-text' type='text' name='themeinception_{$slug}' value='{$value}'></td></tr>\n";
		}
		?>
		</table>
		<?php submit_button( __('Create a blank theme and activate it!', 'themeinception') ); ?>
		</form>
	</div>
<?php
}

function themeinception_create_theme() {
	if ( 'POST' != $_SERVER['REQUEST_METHOD'] )
		return false;
	
	check_admin_referer('themeinception_nonce');
		
	// remove the magic quotes
	$_POST = stripslashes_deep( $_POST );

	if (empty($_POST['themeinception_name'])) {
		add_settings_error( 'themeinception', 'required_name',__('Theme Name is required', 'themeinception'), 'error' );
		return $_POST;
	}
	
//
//	
//	if ( file_exists(trailingslashit(WP_THEME_DIR).$_POST['themeinception_slug'] ) ) {
//		add_settings_error( 'themeinception', 'existing_plugin', __('That plugin appears to already exist. Use a different slug or name.', 'themeinception'), 'error' );
//		return $_POST;
//	}

	$form_fields = array ('themeinception_name', 'themeinception_slug', 'themeinception_uri', 'themeinception_description', 'themeinception_version', 
				'themeinception_author', 'themeinception_author_uri', 'themeinception_license', 'themeinception_license_uri');
	$method = ''; // TODO TESTING

	// okay, let's see about getting credentials
	$url = wp_nonce_url('themes.php?page=themeinception','themeinception_nonce');
	if (false === ($creds = request_filesystem_credentials($url, $method, false, false, $form_fields) ) ) {
		return true; 
	}

	// now we have some credentials, try to get the wp_filesystem running
	if ( ! WP_Filesystem($creds) ) {
		// our credentials were no good, ask the user for them again
		request_filesystem_credentials($url, $method, true, false, $form_fields);
		return true;
	}


	global $wp_filesystem;

	// create the theme directory
	$plugdir = $wp_filesystem->wp_themes_dir() . sanitize_title($_POST['themeinception_name']);
	
	if ( ! $wp_filesystem->mkdir($plugdir) ) {
		add_settings_error( 'themeinception', 'create_directory', __('Unable to create the theme directory.', 'themeinception'), 'error' );
		return $_POST;
	}
	
	// create the theme style.css file
	
	$header = <<<END
/*
Theme Name: {$_POST['themeinception_name']}
Theme URI: {$_POST['themeinception_uri']}
Description: {$_POST['themeinception_description']}
Version: {$_POST['themeinception_version']}
Author: {$_POST['themeinception_author']}
Author URI: {$_POST['themeinception_author_uri']}
Tags: {$_POST['themeinception_tags']}
License: {$_POST['themeinception_license']}
License URI: {$_POST['themeinception_license_uri']}
*/

END;

	$plugfile = trailingslashit($plugdir).'style.css';
	
	if ( ! $wp_filesystem->put_contents( $plugfile, $header, FS_CHMOD_FILE) ) {
		add_settings_error( 'themeinception', 'create_file', __('Unable to create the style.css file.', 'themeinception'), 'error' );
	}
        
        // create the theme header.php
        $header = <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">  
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>  
<head profile="http://gmpg.org/xfn/11">  
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>  
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />  
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen,projection" />  
  
<?php wp_head(); ?>  
  
</head>  
   
<body> 
        
END;
        
        $plugfile = trailingslashit($plugdir).'header.php';
	
	if ( ! $wp_filesystem->put_contents( $plugfile, $header, FS_CHMOD_FILE) ) {
		add_settings_error( 'themeinception', 'create_file', __('Unable to create the header.php file.', 'themeinception'), 'error' );
	}
        
        
        // create theme index.php file
        $dir = trailingslashit($plugdir);
        $header = <<<END
<?php get_header(); ?>
    Welcome to your new theme!Crack open the {$dir} files and get your design juices flowing
<?php get_footer(); ?>        
END;
        $plugfile = trailingslashit($plugdir).'index.php';
	
	if ( ! $wp_filesystem->put_contents( $plugfile, $header, FS_CHMOD_FILE) ) {
		add_settings_error( 'themeinception', 'create_file', __('Unable to create the header.php file.', 'themeinception'), 'error' );
	}
        
        // create theme footer.php file
        $dir = trailingslashit($plugdir);
        $header = <<<END
<?php get_header(); ?>
    Welcome to your new theme!Crack open the {$dir} files and get your design juices flowing
<?php get_footer(); ?>        
END;
        $plugfile = trailingslashit($plugdir).'footer.php';
	
	if ( ! $wp_filesystem->put_contents( $plugfile, $header, FS_CHMOD_FILE) ) {
		add_settings_error( 'themeinception', 'create_file', __('Unable to create the header.php file.', 'themeinception'), 'error' );
	}
        
        
        
        
        
        
	$plugslug = '%2Fthemes%2F'.sanitize_title($_POST['themeinception_name']).'%2F'.sanitize_title($_POST['themeinception_name']).'.php';
	$plugeditor = admin_url('theme-editor.php');//?file='.$plugslug);

//	if ( null !== activate_plugin( $plugslug, '', false, true ) ) {
//		add_settings_error( 'themeinception', 'activate_plugin', __('Unable to activate the new theme.', 'themeinception'), 'error' );
//	}
	
	
	
	$message = sprintf(__('The new theme has been created. You can %sgo to the editor</a> if your browser does not redirect you.', 'themeinception'), '<a href="'.$plugeditor.'">');
	
	add_settings_error('themeinception', 'plugin_active', $message, 'themeinception', 'updated');
	
        echo $message;
        
        // theme created and activated, redirect to the appearances editor
	?>
	<script type="text/javascript">
	<!--
	//window.location = "<?php echo admin_url('themes.php'); ?>"
	//-->
	</script>
	<?php
        
	return true;
}


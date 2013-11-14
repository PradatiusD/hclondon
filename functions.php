<?php
/**
 * Functions and configuration.
 */



/**
 * Define constants.
 */
define( 'EQFW_PATH', dirname(__FILE__) . '/eqfw/' );



/**
 * Load EQFW modules.
 */
$modules = array(
	'options',
	'envato-wordpress-toolkit',
	'contact_form', // requires options
	'google_maps',
	'jquery',
	//'modernizr',
	'jquery_masonry',
	'touchswipe',
	'fancybox',
	'wp-less',
	'meta-box',
	'normalize',
	'helpers',
	'general'
);
foreach ( $modules as $module )	require EQFW_PATH . $module . '/load.php';



/**
 * Include shortcodes.
 */
include 'functions/shortcodes.php';



/**
 * Include background scripts.
 */
include 'functions/background.php';



/**
 * Enqueue scripts.
 */
add_action( 'wp_enqueue_scripts', 'eqfw_enqueue_scripts');
function eqfw_enqueue_scripts() {
	
	wp_enqueue_script( 'theme-script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ) );

}



/**
 * Enqueue styles.
 */
add_action( 'wp_enqueue_scripts', 'eqfw_enqueue_styles' );
function eqfw_enqueue_styles() {

	wp_enqueue_style( 'theme-style-less', get_template_directory_uri() . '/css/style.less' );
	wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/style.css' );

}
add_action( 'admin_enqueue_scripts', 'eqfw_admin_enqueue_styles' );
function eqfw_admin_enqueue_styles() {

	wp_enqueue_style( 'admin-style', get_template_directory_uri() . '/css/admin.css' );

}
add_filter( 'wp_head', 'remove_admin_bar_margin', 99 );
function remove_admin_bar_margin() {

	?>
		<style type="text/css" media="screen"> 
			html { margin-top: 0px !important; } 
			* html body { margin-top: 0px !important; }
		</style>
	<?php

}



/**
 * Disable sidebars.
 */
register_sidebars(0);



/**
 * Set fonts.
 */
add_action( 'wp_enqueue_scripts', 'eqfw_convert_less' );
function eqfw_convert_less() {

	global $WPLessPlugin, $eqfw_options;

	$WPLessPlugin->addVariable( 'menuFont', $eqfw_options['menu_font'] );
	$WPLessPlugin->addVariable( 'titleFont', $eqfw_options['title_font'] );
	$WPLessPlugin->addVariable( 'condensedFont', $eqfw_options['condensed_font'] );
	$WPLessPlugin->addVariable( 'textFont', $eqfw_options['text_font'] );

	?>

	<style type="text/css">
		<?php echo $eqfw_options['include_fonts']; ?>
	</style>

	<?php
	
}



/**
 * Actions after theme setup.
 */
add_action( 'after_setup_theme', 'eqfw_theme_setup' );
function eqfw_theme_setup() {

	global $eqfw_options;
  
  	// Load translation domain.
    load_theme_textdomain( 'eqfw', get_template_directory() . '/eqfw/lang' ); 

    // Register menus.
	register_nav_menu( 'primary', __( 'Primary Menu', 'eqfw' ) );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// Enable featured images for posts.
	//add_theme_support( 'post-thumbnails', array( 'post' ) );

	// Add post formats.
	//add_theme_support( 'post-formats', array( 'gallery', 'quote', 'video', 'audio', 'status' ) );

	// Register thumbnail sizes.
	add_image_size( 'portfolio', 360, 225, true );
	add_image_size( 'background', $eqfw_options['background_width'], $eqfw_options['background_height'], true );
	add_image_size( 'gallery-background', $eqfw_options['gallery_background_width'], $eqfw_options['gallery_background_height'], true );
	add_image_size( 'gallery-thumbnail', 290, 180, true );
	add_image_size( 'gallery-thumbnail-masonry', 360, 9999, false );
	add_image_size( 'gallery-full', 960, 9999, false );
	add_image_size( 'admin-thumbnail', 100, 80, true );
	add_image_size( 'slider-gallery', 520, 300, true );
	add_image_size( 'simple-gallery', $eqfw_options['simple_gallery_width'], $eqfw_options['simple_gallery_height'], true );

	// Style for visual editor.
	add_editor_style();

}



/**
 * Register portfolio post type.
 */
add_action( 'init', 'register_post_types' );
function register_post_types() {

	global $eqfw_meta_boxes;

	// Portfolio type.
	register_post_type( 'portfolio' , array (
		'labels' => array(
			'name' => __( 'Portfolio', 'eqfw' ),
			'singular_name' => __( 'Portfolio item', 'eqfw' )
		),
		'public' => true,
		'supports' => array( 'title', 'editor' ),
		'taxonomies' => array( 'portfolio-category' )
	) );

		// Register portfolio taxonomy.
		register_taxonomy( 'portfolio-category', 'portfolio', array(
			'labels' => array(
				'name' => __( 'Portfolio Categories', 'eqfw' ),
				'singular_name' => __( 'Portfolio Category', 'eqfw' ),
				'search_items' =>  __( 'Search Categories', 'eqfw' ),
				'all_items' => __( 'All Categories', 'eqfw' ),
				'edit_item' => __( 'Edit Category', 'eqfw' ), 
				'update_item' => __( 'Update Category', 'eqfw' ),
				'add_new_item' => __( 'Add New Category', 'eqfw' ),
				'new_item_name' => __( 'New Category Name', 'eqfw' ),
				'menu_name' => __( 'Categories', 'eqfw' )
			)
		) );

		// Register portfolio meta boxes.
		$eqfw_meta_boxes[] = array(
			'id'		=> 'eqfw_portfolio_thumbnail',
			'title'		=> 'Thumbnail',
			'pages'		=> array( 'portfolio' ),
			'fields'	=> array(
				array(
					'name'	=> 'Thumbnail caption',
					'id'	=> 'eqfw_portfolio_thumbnail_caption',
					'type'	=> 'text',
					'desc'	=> 'Set description visible in portfolio thumbnails.'
				),
				array(
					'name'	=> 'Image',
					'id'	=> 'eqfw_portfolio_thumbnail_image',
					'type'	=> 'thickbox_image',
					'max_file_uploads' => 1
				),
				array(
					'name'	=> 'Link to another site',
					'id'	=> 'eqfw_portfolio_thumbnail_link',
					'type'	=> 'text',
					'desc'	=> 'Enter URL where to redirect after clicking thumbnail. <br> Leave empty to keep standard behavior - link to corresponding portfolio page.',
				)
			)
		);

		// Show in admin list.
		add_filter( 'manage_portfolio_posts_columns', 'portfolio_columns', 5 );
		add_action( 'manage_portfolio_posts_custom_column', 'portfolio_custom_columns', 5, 2 );
		function portfolio_columns( $defaults ) {
		    $defaults['eqfw_portfolio_caption'] = __( 'Thumbnail caption', 'eqfw' );
		    $defaults['eqfw_portfolio_image'] = __( 'Thumbnail', 'eqfw' );
		    return $defaults;
		}
		function portfolio_custom_columns( $column_name, $id ) {
	        if ( $column_name == 'eqfw_portfolio_caption' ) {
		        echo get_post_meta( $id, 'eqfw_portfolio_thumbnail_caption', true );
		    }
	        if ( $column_name == 'eqfw_portfolio_image' ) {
				$images = get_post_meta( $id, 'eqfw_portfolio_thumbnail_image', false );
				if ( $images ) echo wp_get_attachment_image( $images[0], 'admin-thumbnail' );
		    }
		}


	// Gallery type.
	register_post_type( 'gallery' , array (
		'labels' => array(
			'name' => __( 'Gallery', 'eqfw' ),
			'singular_name' => __( 'Gallery item', 'eqfw' )
		),
		'public' => true,
		'supports' => array( 'title', 'editor' ),
		'taxonomies' => array( 'gallery-category' )
	) );

		// Register gallery taxonomy.
		register_taxonomy( 'gallery-category', 'gallery', array(
			'labels' => array(
				'name' => __( 'Gallery Categories', 'eqfw' ),
				'singular_name' => __( 'Gallery Category', 'eqfw' ),
				'search_items' =>  __( 'Search Categories', 'eqfw' ),
				'all_items' => __( 'All Categories', 'eqfw' ),
				'edit_item' => __( 'Edit Category', 'eqfw' ), 
				'update_item' => __( 'Update Category', 'eqfw' ),
				'add_new_item' => __( 'Add New Category', 'eqfw' ),
				'new_item_name' => __( 'New Category Name', 'eqfw' ),
				'menu_name' => __( 'Categories', 'eqfw')
			)
		) );

		// Register gallery meta boxes.
		$eqfw_meta_boxes[] = array(
			'id'		=> 'eqfw_gallery',
			'title'		=> 'Gallery settings',
			'pages'		=> array( 'page' ),
			'templates' => array( 'archive-gallery.php' ),
			'fields'	=> array(
				array(
					'name'	=> 'Display type',
					'id'	=> 'eqfw_gallery_type',
					'type'	=> 'radio',
					'options' => array( 'standard' => 'Standard (slider)', 'masonry' => 'Masonry' ),
					'std' => 'masonry'
				),
				array(
					'name'	=> 'Slider interval',
					'id'	=> 'eqfw_gallery_interval',
					'type'	=> 'text',
					'validate' => 'numeric',
					'std' => 0,
					'desc'	=> 'Enter number in miliseconds. <br> Set to <code>0</code> to disable autoplay.'
				),
				array(
					'name'	=> 'Slider Animation',
					'id'	=> 'eqfw_gallery_animation',
					'type'	=> 'radio',
					'options' => array( 'fade' => 'Fade', 'slide' => 'Slide' ),
					'std' => 'fade'
				)
			)
		);
		$eqfw_meta_boxes[] = array(
			'id'		=> 'eqfw_gallery_thumbnail',
			'title'		=> 'Thumbnail',
			'pages'		=> array( 'gallery' ),
			'fields'	=> array(
				array(
					'name'	=> 'Display title',
					'id'	=> 'eqfw_gallery_display_title',
					'type'	=> 'checkbox'
				),
				array(
					'name'	=> 'Image',
					'id'	=> 'eqfw_gallery_image',
					'type'	=> 'thickbox_image',
					'max_file_uploads' => 1
				),
				array(
					'name'	=> 'Video',
					'id'	=> 'eqfw_gallery_video',
					'type'	=> 'text',
					'validate' => 'url',
					'desc'	=> 'Video to be displayed instead of full image (only in masonry). <br> Enter URL of video embed code. <br> To enable autoplay, add <code>?autoplay=1</code> at the end. <br> Example: <code>http://www.youtube.com/embed/Rk6_hdRtJOE</code> <br> Background type must be set to Video.',
				)
			)
		);

		// Show in admin list.
		add_filter( 'manage_gallery_posts_columns', 'gallery_columns', 5 );
		add_action( 'manage_gallery_posts_custom_column', 'gallery_custom_columns', 5, 2 );
		function gallery_columns( $defaults ) {
		    $defaults['eqfw_gallery_caption'] = __( 'Display title', 'eqfw' );
		    $defaults['eqfw_gallery_image'] = __( 'Thumbnail', 'eqfw' );
		    $defaults['eqfw_gallery_video'] = __( 'Video', 'eqfw' );
		    return $defaults;
		}
		function gallery_custom_columns( $column_name, $id ) {
	        if ( $column_name == 'eqfw_gallery_caption' ) {
		        echo ( get_post_meta( $id, 'eqfw_gallery_display_title', true ) ) ? __( 'Yes', 'eqfw' ) : __( 'No', 'eqfw' );
		    }
	        if ( $column_name == 'eqfw_gallery_image' ) {
				$images = get_post_meta( $id, 'eqfw_gallery_image', false );
				if ( $images ) echo wp_get_attachment_image( $images[0], 'admin-thumbnail' );
		    }
	        if ( $column_name == 'eqfw_gallery_video' ) {
				echo get_post_meta( $id, 'eqfw_gallery_video', true );
		    }
		}


	// Register contact meta boxes.
	$eqfw_meta_boxes[] = array(
		'id'		=> 'eqfw_contact',
		'title'		=> 'Map settings',
		'pages'		=> array( 'page' ),
		'templates' => array( 'contact.php' ),
		'fields'	=> array(
			array(
				'name'	=> 'Latitude',
				'id'	=> 'eqfw_contact_latitude',
				'type'	=> 'text',
				'validate' => 'numeric'
			),
			array(
				'name'	=> 'Longitude',
				'id'	=> 'eqfw_contact_longitude',
				'type'	=> 'text',
				'validate' => 'numeric'
			),
			array(
				'name'	=> 'Zoom',
				'id'	=> 'eqfw_contact_zoom',
				'type'	=> 'text',
				'validate' => 'numeric'
			),
			array(
				'name'	=> 'Map type',
				'id'	=> 'eqfw_contact_map_type',
				'type'	=> 'radio',
				'options' => array( 'HYBRID' => 'Hybrid', 'ROADMAP' => 'Roadmap', 'SATELLITE' => 'Satellite', 'TERRAIN' => 'Terrain' ),
				'std' => 'ROADMAP'
			)
		)
	);

}



/**
 * Render social media icons.
 */
function eqfw_social_media_icons() {

	global $eqfw_options, $eqfw_social_icons;

	echo '<ul class="social">';
	foreach ( $eqfw_social_icons as $icon )
		if ( !empty( $eqfw_options['social_' . $icon] ) )
			echo '<li><a href="' . $eqfw_options['social_' . $icon] . '"><img src="' . get_template_directory_uri() . '/img/social/' . $icon . '.png" /></a></li>';
	echo '</ul>';

}
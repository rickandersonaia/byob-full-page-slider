<?php
/*
Name: BYOB Full Page Slider
Author: Rick Anderson - BYOBWebsite.com
Description: Enables a full page slider.
Version: 2.6
Requires: 2.6.2
Class: byob_full_page_slider
Docs: https://www.byobwebsite.com
License: MIT

Copyright 2017 BYOBWebsite.
  DIYthemes, Thesis, and the Thesis Theme are registered trademarks of DIYthemes, LLC.
  Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */

class byob_full_page_slider extends thesis_box {

	public $type = 'rotator';
	private $prefix = 'byobfps';
	private $post_types = array();
	public $dependents = array(
		'thesis_post_headline',
		'thesis_post_content'
	);
	public $children = array(
		'thesis_post_headline',
		'thesis_post_content'
	);

	protected function translate() {
		$this->name = $this->title = __( 'BYOB Full Page Slider', 'byobfps' );
	}

	protected function construct() {
		global $byob_ah;;

		if ( ! defined( 'BYOBFPS_PATH' ) ) {
			define( 'BYOBFPS_PATH', dirname( __FILE__ ) );
		}
		if ( ! defined( 'BYOBFPS_URL' ) ) {
			define( 'BYOBFPS_URL', THESIS_USER_BOXES_URL . '/' . basename( __DIR__ ) );
		}


		if ( is_admin() ) {
			if ( ! class_exists( 'byob_asset_handler' ) ) {
				include_once( BYOBFPS_PATH . '/byob_asset_handler.php' );
			}
			if ( ! isset( $my_asset_handler ) ) {
				$byob_ah = new byob_asset_handler;
			}
		}
		$this->create_post_type();
		$this->create_custom_taxonomy();
		$this->post_types = get_post_types( '', 'names' );
		unset( $this->post_types['slide'] );
	}

	protected function options() {
		$terms_list = get_terms( 'byob_slideshow' );
		if ( ! empty( $terms_list ) ) {
			foreach ( $terms_list as $term ) {
				$terms[ $term->term_id ] = $term->name;
			}

			$options = array(
				'slideshow'     => array(
					'type'    => 'select',
					'label'   => __( 'Choose a Slideshow', 'byobfps' ),
					'tooltip' => __( 'Only slides from the chosen slideshow will be displayed', 'byobfps' ),
					'options' => $terms
				),
				'anchor'        => array(
					'type'        => 'text',
					'width'       => 'full',
					'code'        => true,
					'label'       => __( 'ID to scroll to', 'byobfps' ),
					'description' => __( 'Enter the HTML ID you want to scroll to when you hit the scroll button', 'byobfps' ),
					'placeholder' => __( '#content_area', 'byobfps' )
				),
				'anchor_tex'    => array(
					'type'        => 'text',
					'width'       => 'full',
					'code'        => true,
					'label'       => __( 'Lable for the scroll to button', 'byobfps' ),
					'description' => __( 'Enter the text you want displayed on the scroll button', 'byobfps' ),
					'placeholder' => __( 'EXPLORE', 'byobfps' )
				),
				'scroll_offset' => array(
					'type'        => 'text',
					'width'       => 'full',
					'code'        => true,
					'label'       => __( 'Adjust scroll by x pixels', 'byobfps' ),
					'description' => __( 'Enter the number of pixels to adjust the scroll', 'byobfps' ),
					'placeholder' => __( '100', 'byobfps' )
				)
			);

			return $options;
		}
	}

	protected function html_options() {
		global $thesis;
		$options = $thesis->api->html_options();
		unset( $options['id'] );

		return $options;
	}

	public function post_meta() {
		$options = array(
			'title'   => __( 'Slide Options', 'byobfps' ),
			'exclude' => $this->post_types,
			'fields'  => array(
				'link_url'     => array(
					'type'        => 'text',
					'width'       => 'full',
					'code'        => true,
					'label'       => __( 'Call to Action Button URL', 'byobfps' ),
					'description' => __( 'Enter the URL of the page you want the call to action button to link to', 'byobfps' ),
					'tooltip'     => __( 'Include the <code>http://</code>', 'byobfps' )
				),
				'link_text'    => array(
					'type'        => 'text',
					'width'       => 'full',
					'code'        => true,
					'label'       => __( 'Call to Action Button Text', 'byobfps' ),
					'description' => __( 'Enter the text you wish to display on the button', 'byobfps' ),
					'placeholder' => __( 'Click Here Now!', 'byobfps' )
				),
				'link_url2'    => array(
					'type'        => 'text',
					'width'       => 'full',
					'code'        => true,
					'label'       => __( '2nd Call to Action Button URL', 'byobfps' ),
					'description' => __( 'Enter the URL of the page you want the call to action button to link to', 'byobfps' ),
					'tooltip'     => __( 'Include the <code>http://</code>', 'byobfps' )
				),
				'link_text2'   => array(
					'type'        => 'text',
					'width'       => 'full',
					'code'        => true,
					'label'       => __( '2nd Call to Action Button Text', 'byobfps' ),
					'description' => __( 'Enter the text you wish to display on the button', 'byobfps' )
				),
				'color_scheme' => array(
					'type'        => 'select',
					'label'       => __( 'Choose Text Color Scheme', 'byobfps' ),
					'description' => __( 'Choose either light or dark text depending on the slide - light is default', 'byobfps' ),
					'tooltip'     => __( 'Light text looks best on dark slides and dark text looks best on light slides - choose one', 'byobfps' ),
					'options'     => array(
						'light_text' => __( 'Light text', 'byobfps' ),
						'dark_text'  => __( 'Dark text', 'byobfps' )
					)
				)
			)
		);

		return $options;
	}

	public function preload() {
		/* This runs only on templates on which this Box appears. */
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
	}

	public function load_styles() {
		wp_enqueue_style( 'superslide', BYOBFPS_URL . '/assets/css/slider.css' );
	}

	public function load_scripts() {
		wp_enqueue_script( 'jquery-easing', BYOBFPS_URL . '/assets/js/jquery.easing.1.3.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-animate-enhanced', BYOBFPS_URL . '/assets/js/jquery.animate-enhanced.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'hammer', BYOBFPS_URL . '/assets/js/hammer.min.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'superslide', BYOBFPS_URL . '/assets/js/jquery.superslides.min.js', array( 'jquery' ), '', true );
	}

	public function html( $args = false ) {
		global $thesis;
		extract( $args = is_array( $args ) ? $args : array() );
		$tab          = str_repeat( "\t", $depth = ! empty( $depth ) ? $depth : 0 );
		$simple_class = ! empty( $this->options['class'] ) ? trim( $thesis->api->esc( $this->options['class'] ) ) : false;
		if ( $simple_class ) {
			$class = ' class="' . $simple_class . '"';
		} else {
			$class = false;
		}
		$anchor        = ! empty( $this->options['anchor'] ) ? esc_attr( $this->options['anchor'] ) : '#content_area';
		$anchor_text   = ! empty( $this->options['anchor_text'] ) ? esc( $this->options['anchor_text'] ) : 'EXPLORE';
		$scroll_offset = ! empty( $this->options['scroll_offset'] ) ? int( $this->options['scroll_offset'] ) : 100;
		$args          = array(
			'post_type' => 'slide',
			'order'     => 'ASC',
			'orderby'   => 'menu_order'
		);
		if ( ! empty( $this->options['slideshow'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'byob_slideshow',
					'field'    => 'id',
					'terms'    => $this->options['slideshow']
				)
			);
		}

		$slide_query = new WP_Query( $args );
//        var_dump($slide_query);
		?>
		<div id="slider">
			<div id="slides">
				<div class="slides-container">
					<?php
					while ( $slide_query->have_posts() ) {
						$slide_query->the_post();
						$custom_fields = get_post_meta( $slide_query->post->ID, '_byob_full_page_slider' );
						$link_url      = ! empty( $custom_fields[0]['link_url'] ) ? esc_url( $custom_fields[0]['link_url'] ) : '#';
						$link_text     = ! empty( $custom_fields[0]['link_text'] ) ? wp_kses_post( $custom_fields[0]['link_text'] ) : "Click Here";
						$link_url2     = ! empty( $custom_fields[0]['link_url2'] ) ? esc_url( $custom_fields[0]['link_url2'] ) : "#";
						$link_text2    = ! empty( $custom_fields[0]['link_text2'] ) ? wp_kses_post( $custom_fields[0]['link_text2'] ) : false;
						if ( $link_text ) {
							$link_1 = "";
						}
						$color_scheme = ! empty( $custom_fields[0]['color_scheme'] ) ? esc_attr( $custom_fields[0]['color_scheme'] ) : 'light_text';
						echo "$tab<div class=\"$color_scheme\">\n";
						echo "$tab\t<div class='overlay'>\n";
						echo get_the_post_thumbnail( $slide_query->post->ID, 'full_page_slide' );
						echo "$tab\t</div>\n";
						echo "$tab\t<div class=\"slides-caption-table\">\n";
						echo "$tab\t\t<div class=\"slides-caption\">\n";
						echo $this->rotator( array_merge( $args, array( 'depth' => $depth + 4 ) ) );
						echo "$tab\t\t\t<a href=\"$link_url\">$link_text</a>";
						if ( false !== $link_text2 ) {
							echo "<a class=\"alt-link\" href=\"$link_url2\">$link_text2</a>";
						}
						echo "$tab\t\t</div>\n";
						echo "$tab\t</div>\n";
						echo "$tab</div>\n";
					}
					?>
				</div>
				<!-- Slider Navigation -->
				<div class="slides-navigation">
					<ul>
						<li><a href="#" class="prev">Previous</a></li>
						<li><span class="start"><?php echo $anchor_text; ?></span></li>
						<li><a href="#" class="next">Next</a></li>
					</ul>
				</div>
			</div>
		</div>
		<!-- Content Anchor for smooth scrolling -->
		<div id="content-anchor"></div>
		<script type="text/javascript">
            jQuery(document).ready(function () {
                // Slider
                jQuery('#slides').superslides({
                    hashchange: false,
                    animation: 'slide',
                    pagination: false,
                    play: 15000,
                    animation_speed: 500
                });
                // Slider fade
                var target = jQuery('#slides');
                var targetHeight = target.outerHeight();
                jQuery(window).on('scroll', function (e) {
                    var scrollPercent = 0;
                    scrollPercent = (targetHeight - jQuery(window).scrollTop()) / targetHeight;
                    if (scrollPercent >= 0) {
                        target.css('opacity', scrollPercent);
                    }
                });
                // Smooth Scroll
                var anchor = '<?php echo $anchor ?>';
                var scroll_offset = <?php echo $scroll_offset ?>;
                jQuery(".start").click(function () {
                    jQuery('html, body').animate({
                        scrollTop: jQuery(anchor).offset().top - scroll_offset
                    }, 1000);
                });
            });
		</script>
		<?php
		wp_reset_postdata();
	}

	public function create_post_type() {
		$slide_labels = array(
			'name'               => __( 'Slides', 'byobfps' ),
			'singular_name'      => __( 'Slide', 'byobfps' ),
			'menu_name'          => __( 'Slides', 'byobfps' ),
			'name_admin_bar'     => __( 'Slide', 'byobfps' ),
			'add_new'            => __( 'Add New', 'byobfps' ),
			'add_new_item'       => __( 'Add New Slide', 'byobfps' ),
			'new_item'           => __( 'New Slide', 'byobfps' ),
			'edit_item'          => __( 'Edit Slide', 'byobfps' ),
			'view_item'          => __( 'View Slide', 'byobfps' ),
			'all_items'          => __( 'All Slides', 'byobfps' ),
			'search_items'       => __( 'Search Slides', 'byobfps' ),
			'parent_item_colon'  => __( 'Parent Slides:', 'byobfps' ),
			'not_found'          => __( 'No slides found.', 'byobfps' ),
			'not_found_in_trash' => __( 'No slides found in Trash.', 'byobfps' )
		);

		$slide_args = array(
			'labels'             => $slide_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'slide' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-images-alt',
			'menu_position'      => 10,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' )
		);

		register_post_type( 'slide', $slide_args );
	}

	public function create_custom_taxonomy() {
		// Add new taxonomy, make it hierarchical (like categories)
		$slideshow_labels = array(
			'name'              => __( 'Slideshows', 'byobfps' ),
			'singular_name'     => __( 'Slideshow', 'byobfps' ),
			'search_items'      => __( 'Search Slideshows', 'byobfps' ),
			'all_items'         => __( 'All Slideshows', 'byobfps' ),
			'parent_item'       => __( 'Parent Slideshow', 'byobfps' ),
			'parent_item_colon' => __( 'Parent Slideshow:', 'byobfps' ),
			'edit_item'         => __( 'Edit Slideshow', 'byobfps' ),
			'update_item'       => __( 'Update Slideshow', 'byobfps' ),
			'add_new_item'      => __( 'Add New Slideshow', 'byobfps' ),
			'new_item_name'     => __( 'New Slideshow Name', 'byobfps' ),
			'menu_name'         => __( 'Slideshow', 'byobfps' ),
		);

		$slide_args = array(
			'hierarchical'      => false,
			'labels'            => $slideshow_labels,
			'show_ui'           => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'slideshow' ),
		);

		register_taxonomy( 'byob_slideshow', array( 'slide' ), $slide_args );
	}

}
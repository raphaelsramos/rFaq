<?php

	/***
	 *	Date: 2018-06-13
	 *
	 *	Dependencies
	 *		- rStr
	 *		- rHTML
	 */

	if( !class_exists( 'rFAQ_Backend' ) ){
		class rFAQ_Backend {


			protected static $cpt;


			public static function activate(){}


			public static function deactivate(){}


			public static function init() {

				self::$cpt = 'faq';
			
				// 1. create cpt
				add_action( 'init', array( __CLASS__, '_cpt' ), 0 );
				
				// 2. fix cpt link
				add_filter( 'post_type_link', array( __CLASS__, '_post_type_link' ), 10, 3 );
				
				// 3. module shortcode
				add_shortcode( self::$cpt, array( __CLASS__, '_sc' ) );

			}


			public static function _cpt(){
				
				$td = 'r-'. self::$cpt;
				$single = __( 'FAQ', $td );
				$plural = __( 'FAQs', $td );
				
				$labels = apply_filters( 'r/faq/cpt/labels', array(
					'name'                  => _x( 'FAQ', 'Post Type General Name', $td ),
					'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', $td ),
					'menu_name'             => $plural,
					'name_admin_bar'        => $single,
					'archives'              => sprintf( __( '%s Archives', $td ), $single ),
					'parent_item_colon'     => sprintf( __( 'Parent %s:', $td ), $single ),
					'all_items'             => sprintf( __( 'All %s', $td ), $plural ),
					'add_new_item'          => sprintf( __( 'Add New %s', $td ), $single ),
					'add_new'               => __( 'Add New', $td ),
					'new_item'              => sprintf( __( 'New %s', $td ), $single ),
					'edit_item'             => sprintf( __( 'Edit %s', $td ), $single ),
					'update_item'           => sprintf( __( 'Update %s', $td ), $single ),
					'view_item'             => sprintf( __( 'View %s', $td ), $single ),
					'search_items'          => sprintf( __( 'Search %s ', $td ), $single ),
					'not_found'             => sprintf( __( 'No %s found', $td ), $plural ),
					'not_found_in_trash'    => sprintf( __( 'No %s found in Trash', $td ), $plural ),
					'featured_image'        => __( 'Featured Image', $td ),
					'set_featured_image'    => __( 'Set featured image', $td ),
					'remove_featured_image' => __( 'Remove featured image', $td ),
					'use_featured_image'    => __( 'Use as featured image', $td ),
					'insert_into_item'      => sprintf( __( 'Insert into %s', $td ), $single ),
					'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', $td ), $single ),
					'items_list'            => sprintf( __( '%s item list', $td ), $single ),
					'items_list_navigation' => sprintf( __( '%s items list navigation', $td ), $single ),
					'filter_items_list'     => sprintf( __( 'Filter %s items list', $td ), $plural ),
				) );
				
				$supports = apply_filters( 'r/faq/cpt/supports', array( 'title', 'editor', 'revisions', 'page-attributes' ) );
				
				$args = apply_filters( 'r/faq/cpt/args', array(
					'label'                 => $single,
					'description'           => sprintf( __( '$s Post Type', $td ), $plural ),
					'labels'                => $labels,
					'supports'              => $supports,
					'hierarchical'          => false,
					'public'                => true, //true, false only if it dont need to show on website
					'show_ui'               => true,
					'show_in_menu'          => true,
					'menu_position'         => 25,
					'menu_icon'				=> apply_filters( 'r/faq/cpt/icon', 'dashicons-format-chat' ),
					'show_in_admin_bar'     => true,
					'show_in_nav_menus'     => false,
					'can_export'            => true,
					'has_archive'           => false,		
					'exclude_from_search'   => true,
					'publicly_queryable'    => true,
					'capability_type'       => 'page',
				) );
				
				register_post_type( self::$cpt, $args );
			}

			
			public static function _post_type_link( $url, $post, $leavename ){
				$base = apply_filters( 'r/faq/base_url', home_url( self::$cpt .'/' ), $url, $post );
				if( $post->post_type == self::$cpt ){
					$url = apply_filters( 'r/faq/item_url', $base .'#'. $post->post_name, $base, $post );
				}
				return $url;
			}
			
			
			/*** SC ***
			 *	[depoimentos
					query="{}"
					id="x||x,y,z"
					template=""
					class=""
					atts="{}"
				]
			 */
			public static function _sc( $args = array(), $content ){
				
				$query = array(
					'post_type' => self::$cpt,
					'posts_per_page' => '-1',
					'orderby' => 'menu_order, title',
					'order' => 'ASC'
				);

				// args[ query ]
				if( isset( $args[ 'query' ] ) ){
					$data_query = rStr::to_arr( urldecode( $args[ 'query' ] ) );
					$query = array_merge( $query, $data_query );
					unset( $atts[ 'query' ] );
				}


				// args[ id ]
				if( isset( $args[ 'id' ] ) && !empty( $args[ 'id' ] ) ){

					// ids separados por ,
					if( strpos( $args[ 'id' ], ',' ) !== false ){
						$query[ 'post__in' ] = explode( ',', $args[ 'id' ] );
						$query[ 'orderby' ] = 'post__in';
						unset( $query[ 'posts_per_page' ], $query[ 'order' ] );
					}
					
					// id unico
					else {
						$query[ 'p' ] = $args[ 'id' ];
					}

					unset( $args[ 'id' ] );
				}

				
				// args[ template ]
				$theme_templates = apply_filters( 'r/faq/sc/templates', array(
					'single-faq.php',
					'partials/faq/_item.php'
				) );

				if( isset( $args[ 'template' ] ) && !empty( $args[ 'template' ] ) ){
					$theme_templates[] = str_replace( '.php', '', $args[ 'template' ] ) .'.php';
					unset( $args[ 'template' ] );
				}

				$template_file = R_FAQ_PATH .'views/_item.php';
				if( $overridden_template = locate_template( array_reverse( $theme_templates ) ) ){
					$template_file = $overridden_template;
				}
				
				
				// args[ class ]
				$class = array( $query[ 'post_type' ] .'-list' );
			
				if( isset( $args[ 'class' ] ) && !empty( $args[ 'class' ] ) ){
					$class = array_merge( $class, explode( ' ', $args[ 'class' ] ) );
				}


				// args[ atts ]
				/*
				$atts = shortcode_atts( array(
					'tag' => 'div',
					'class' => $class,
					'data-type' => $query[ 'post_type' ]
				), $args );
				*/
				$atts = array(
					'tag' => 'div',
					'class' => $class,
					'data-type' => $query[ 'post_type' ]
				);

				if( isset( $args[ 'atts' ] ) ){
					$data_atts = (array) rStr::to_arr( urldecode( $args[ 'atts' ] ) );
					var_dump( 'rfaq', $data_atts );
					$atts = array_merge( $atts, $data_atts );
				}


				// tag attr
				$tag = $atts[ 'tag' ];
				unset( $atts[ 'tag' ] );
				
				
				// items
				$items = array();
				
				$loop = new WP_Query( $query );
				if( $loop->have_posts() ){
					while( $loop->have_posts() ){
						$loop->the_post();
						
						$template_file = apply_filters( 'r/faq/sc/template_file', $template_file, get_the_ID() );
						
						ob_start();
						load_template( $template_file, false );
						$item = ob_get_contents();
						ob_end_clean();
						
						$item = apply_filters( 'r/faq/sc/item_html', $item, get_the_ID() );
						
						$items[] = $item;
					}
					wp_reset_postdata();
				}
				
				// items
				$items_html = implode( "\n", $items );
				$items_html = apply_filters( 'r/faq/sc/items_html', $items_html, $loop );

				
				// items wrap
				$atts[ 'class' ] = implode( " ", (array)$atts[ 'class' ] );
				$items_atts = apply_filters( 'sc/faq/items_attrs', $atts, $loop );
				$items_wrap = rHtml::tag( $tag, $items_atts, $items_html );
				

				// template file data
				$template_file = str_replace( '\\', '/', $template_file );
				if( strpos( $template_file, 'faq/' ) !== false ){
					$template_arr = explode( 'faq/', $template_file );
					$template_name = $template_arr[ 1 ];
				}
				else {
					$template_arr = explode( '/partials/', $template_file );
					$template_name = 'partials/'. $template_arr[ 1 ];
				}
				$template_name = str_replace( '.php', '', $template_name );
				
				
				// output
				$output_atts = array( 'class' => 'faq-wrap', 'data-template' => $template_name );
				$output_atts = apply_filters( 'r/faq/sc/output_attrs', $output_atts );
				
				$output = rHtml::tag( 'div', $output_atts, $items_wrap );
				$output = apply_filters( 'r/faq/sc/output', $output, $loop );

				return $output;
			}
		}
	}

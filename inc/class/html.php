<?php

	/***
	 *	Date: 2017-12-24
	 *
	 *	Dependencies
	 */

	if( !class_exists( 'rCore' ) && !class_exists( 'rHTML' ) ){
		class rHTML {

			//return html tag
			public static function tag( $tag = '', $atts = array(), $text = '', $selfClose = false ){
				
				if( !is_array( $atts ) && empty( $text ) ){
					$text = $atts;
					$atts = array();
				}
				
				$atts = apply_filters( 'r/html/tag/atts', $atts );
				
				$t = '<'. $tag . rArr::to_atts( $atts );
				
				$t .= ( !!$selfClose )
						? ' />'
						: ">{$text}</{$tag}>";
				
				$t = apply_filters( 'r/html/tag/output', $t );

				return $t;

			}

		
			//link_to
			public static function link_to( $url, $atts = array(), $text = '' ){
				
				$text = ( !empty( $text ) ) ? $text : $url;
				
				$atts[ 'href' ] = $url;
				
				$atts = apply_filters( 'r/html/link_to/atts', $atts );
				
				$output = rHTML::tag( 'a', $atts, $text );
				
				$output = apply_filters( 'r/html/link_to/output', $output );
				
				return $output;

			}

			
			//create img tag
			public static function img( $file, $atts = array() ){
				
				if( empty( $file ) ) return '';
				
				$atts[ 'src' ] = $file;
				
				$atts = apply_filters( 'r/html/img/atts', $atts );
				
				$output = rHTML::tag( 'img', $attrs, '', true );
				
				$output = apply_filters( 'r/html/img/output', $output );
				
				return $output;

			}

		
			//create css tag
			public static function css( $file, $atts = array() ){
				
				$atts = array_merge( $atts, array(
					'href' => $file,
					'rel' => 'stylesheet'
				) );
				
				$atts = apply_filters( 'r/html/css/atts', $atts );
				
				$output = rHTML::tag( 'link', $attrs, '', true );
				
				$output = apply_filters( 'r/html/css/output', $output );
				
				return $output;
			}

		
			// uses css/index.php to compact css files
			public static function cssCompact( $files_arr = array(), $folder = '', $atts = array() ){
				
				if( !count( $files_arr ) ){
					return '';
				}
				
				$files_arr = apply_filters( 'r/html/cssCompact/files_arr', $files_arr );
				
				$files_str = implode( $files_arr, ',' );
				$files_str = trim( str_replace( array( '.css', '.php' ), '', $files ), ',' );
				
				$files = apply_filters( 'r/html/cssCompact/files', $files_arr, $files_str, $folder );
				
				$atts = apply_filters( 'r/html/cssCompact/atts', $atts );
				
				$output = rHTML::css( $folder .'?'. $files_str );
				
				$output = apply_filters( 'r/html/cssCompact/output', $output );
				
				return $output;

			}


			//create js tag
			public static function js( $file, $atts = array(), $content = '' ){
				
				$atts[ 'src' ] = $file;
				
				$atts = apply_filters( 'r/html/js/atts', $atts );
				
				$content = apply_filters( 'r/html/js/content', $content, $file );
				
				$output = rHTML::tag( 'script', $atts, $content ) ."\n";
				
				$output = apply_filters( 'r/html/js/output', $output );
				
				return $output;
	 
			}
		
		}
	}

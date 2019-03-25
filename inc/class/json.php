<?php
	
	/***
	 *	2018-03-15
	 */

	if( !class_exists( 'rCore' ) && !class_exists( 'rJSON' ) ){
		class rJSON {

			/***
			 *	check if $string is a valid JSON
			 *
			 *	@return boolean
			 */
			public static function is( $string ){
				
				if( !is_string( $string ) ){
					return false;
				}
				
				return ( is_object( json_decode( $string ) ) || is_array( json_decode( $string ) ) );
			}


			/***
			 *	converts a valid JSON to ARRAY
			 *
			 *	@return boolean / array
			 */
			public static function to_arr( $json, $assoc = false, $depth = 512, $options = 0 ){
				
				if( !is_string( $json ) ){
					return false;
				}
				
				// search and remove comments like /* */ and //
				$json = preg_replace( "#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json );
				
				if( version_compare( phpversion(), '5.4.0', '>=' ) ){ 
					return json_decode( $json, $assoc, $depth, $options );
				}
				
				elseif( version_compare( phpversion(), '5.3.0', '>=' ) ){ 
					return json_decode( $json, $assoc, $depth );
				}
				
				else {
					return json_decode( $json, $assoc );
				
				}
			}
		
		}
	}

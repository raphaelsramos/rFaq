<?php

	/***
	 *	2018-03-15
	 */

	if( !class_exists( 'rCore' ) && !class_exists( 'rArr' ) ){
		class rArr {

			/***
			 *	 convert array to html atts var2="value" var1='{"name1":"value1"}'
			 */
			public static function to_atts( $atts = array(), $as_json = false, $prefix = '', $union_on_array = ',' ){
			
				if( !is_array( $atts ) || !count( $atts ) ){
					return '';
				}
				
				$atts = apply_filters( 'r/arr/to_atts/atts', $atts, $as_json, $prefix, $union_on_array );
				
				$r = $atts;

				foreach( $atts as $name => $val ){
					$aspas = '"';
					if( is_array( $val ) ){
						if( !!$as_json ){
							$val = json_encode( $val );
							$aspas = "'";
						}
						else {
							$val = implode( $union_on_array, $val );
						}
					}
					
					$r[ $name ] = apply_filters( 'r/arr/to_atts/value', "{$prefix}{$name}={$aspas}{$val}{$aspas}", $name, $val, $prefix, $aspas );
				}
				
				$r = apply_filters( 'r/arr/to_atts/atts_html', $r, $atts, $as_json, $prefix, $union_on_array );
				
				return ' '. implode( ' ', $r );
			}

		
			public static function remove_empty( $array ){
				$filtered = array_filter( $array, 'rArr::remove_empty_filter' );
				return $filtered;
			}

			
			public static function remove_empty_filter( $item ){
				if( is_array( $item ) ){
					return array_filter( $item, 'rArr::remove_empty_filter' );
				}
				if( !empty( $item ) ){
					return true;
				}
			}
		
		}
	}

	
	# https://gist.github.com/puiu91/02b7feec4159cf14f9a9
	/***
	 *	This file is part of the array_column library
	 *
	 *	For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
	 *
	 *	@copyright Copyright (c) Ben Ramsey (http://benramsey.com)
	 *	@license http://opensource.org/licenses/MIT MIT
	 */
	if( !function_exists( 'array_column' ) ){
		/***
		 *	Returns the values from a single column of the input array, identified by the $columnKey.
		 *
		 *	Optionally, you may provide an $indexKey to index the values in the returned array by the values from the $indexKey column in the input array.
		 *
		 *	@param array $input		A multi-dimensional array (record set) from which to pull
		 *							a column of values.
		 *
		 *	@param mixed $columnKey	The column of values to return. This value may be the
		 *							integer key of the column you wish to retrieve, or it
		 *							may be the string key name for an associative array.
		 *
		 *	@param mixed $indexKey	(Optional.) The column to use as the index/keys for
		 *							the returned array. This value may be the integer key
		 *							of the column, or it may be the string key name.
		 *
		 *	@return array
		 */
		function array_column( $input = null, $columnKey = null, $indexKey = null ){
			// Using func_get_args() in order to check for proper number of
			// parameters and trigger errors exactly as the built-in array_column()
			// does in PHP 5.5.
			$argc = func_num_args();
			$params = func_get_args();

			if( $argc < 2 ){
				trigger_error(
					"array_column() expects at least 2 parameters, {$argc} given",
					E_USER_WARNING
				);
				return null;
			}

			if( !is_array( $params[ 0 ] ) ){
				trigger_error(
					'array_column() expects parameter 1 to be array, ' . gettype( $params[ 0 ] ) . ' given',
					E_USER_WARNING
				);
				return null;
			}

			if( !is_int( $params[ 1 ] )
				&& !is_float( $params[ 1 ] )
				&& !is_string( $params[ 1 ] )
				&& $params[ 1 ] !== null
				&& !( is_object( $params[ 1 ] ) && method_exists( $params[ 1 ], '__toString' ) )
			){
				trigger_error(
					'array_column(): The column key should be either a string or an integer',
					E_USER_WARNING
				);
				return false;
			}

			if( isset( $params[ 2 ] )
				&& !is_int( $params[ 2 ] )
				&& !is_float( $params[ 2 ] )
				&& !is_string( $params[ 2 ] )
				&& !(is_object( $params[ 2 ] ) && method_exists( $params[ 2 ], '__toString' ) )
			){
				trigger_error(
					'array_column(): The index key should be either a string or an integer',
					E_USER_WARNING
				);
				return false;
			}

			$paramsInput = $params[ 0 ];
			$paramsColumnKey = ( $params[ 1 ] !== null ) ? (string) $params[ 1 ] : null;

			$paramsIndexKey = null;
			if( isset( $params[ 2 ] ) ){
				if( is_float( $params[ 2 ] ) || is_int( $params[ 2 ] ) ){
					$paramsIndexKey = (int) $params[ 2 ];
				} else {
					$paramsIndexKey = (string) $params[ 2 ];
				}
			}

			$resultArray = array();

			foreach( $paramsInput as $row ){
				$key = $value = null;
				$keySet = $valueSet = false;

				if( $paramsIndexKey !== null && array_key_exists( $paramsIndexKey, $row ) ){
					$keySet = true;
					$key = (string) $row[ $paramsIndexKey ];
				}

				if( $paramsColumnKey === null ){
					$valueSet = true;
					$value = $row;
				}
				elseif( is_array( $row ) && array_key_exists( $paramsColumnKey, $row ) ){
					$valueSet = true;
					$value = $row[ $paramsColumnKey ];
				}

				if( $valueSet ){
					if( $keySet ){
						$resultArray[ $key ] = $value;
					} else {
						$resultArray[] = $value;
					}
				}

			}

			return $resultArray;
		}

	}

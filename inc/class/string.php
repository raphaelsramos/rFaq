<?php
	
	/***
	 *	2018-03-15
	 */
	
	if( !class_exists( 'rCore' ) && !class_exists( 'rStr' ) ){
		class rStr {

			/***
			 *	check if $str contains $find
			 *
			 *	@return boolean
			 */
			public static function has( $str, $find ){
				return ( strpos( $str, $find ) !== false );
			}


			/***
			 *	check if $str begin with $find
			 *
			 *	@return boolean
			 */
			public static function begin( $str, $find ){
				return ( substr( $str, 0, strlen( $find ) ) === $find );
			}


			/***
			 *	check if $str end with $find
			 *
			 *	@return boolean
			 */
			public static function end( $str, $find ){
				$length = strlen( $find );
				return ( !$length || substr( $str, -$length ) === $find );
			}


			/***
			 *	check if $str is not empty
			 *
			 *	@return boolean
			 */
			public static function filled( $str ){
				$str = trim( $str );
				return !empty( $str );
			}


			/***
			 *	try to convert the string $code to JSON
			 *
			 *	@return boolean / JSON
			 */
			public static function to_json( $code = '' ){
				
				if( empty( $code ) ){
					return false;
				}
				
				if( rJSON::is( $code ) ){
					return $code;
				}

				$code = trim( $code );
				
				if( substr( $code, 0, 1 ) !== '{' ){
					$code = '{'. $code;
				}
				
				if( substr( $code, -1, 1 ) !== '}' ){
					$code .= '}';
				}
				
				// troca aspas duplas por simples
				$code = str_replace( '\'', '"', $code );
				
				// remove \t, \n e \r
				$code = preg_replace( '/[\t\n\r\0\x0B]/', '', $code );
				
				// remove espaços duplicados
				$code = preg_replace( '/([\s])\1+/', ' ', $code );
				
				// remove space before and after : ; { } ( )
				$code = preg_replace( '/\s?(\:|\;|\,|\{|\}|\(|\))\s?/', '$1', $code );

				// troca parentes para arrays
				$code = str_replace( array( ':(', '),', ')}' ), array( ':[', '],', ']}' ), $code );

				
				// adiciona aspas duplas
				$code = str_replace( array( '{', '}' ), array( '{"', '"}' ), $code );
				$code = str_replace( array( '[', ']' ), array( '["', '"]' ), $code );
				$code = str_replace( array( ':', ';', ',' ), array( '":"', '";"', '","' ), $code );
				
				
				// fix aspas duplas x 2
				$code = str_replace( array( '""', '" "' ), '"', $code );
				
				// fix array
				$code = str_replace( array( '"["', '"[', '"]"', ']"' ), array( '["', '[', '"]', ']' ), $code );
				
				// fix object
				$code = str_replace( array( '"{"', '"{', '"}"', '}"', '{"{', '}"}' ), array( '{"', '{', '"}', '}', '{{', '}}' ), $code );
				
				// fix bool
				$code = str_replace( '"true"', 'true', $code );
				$code = str_replace( '"false"', 'false', $code );

				if( rJSON::is( $code ) ){
					return $code;
				}
				
				return false;
			}


			/***
			 *	try to convert the string $str to JSON, then ARRAY
			 *
			 *	@return boolean / ARRAY
			 */
			public static function to_arr( $str = '' ){
				
				if( rJSON::is( $str ) ){
					return rJSON::to_arr( $str, true );
				}
				
				$code = rStr::to_json( $str );
				
				if( rJSON::is( $code ) ){
					return rJSON::to_arr( $code, true );
				}
				
				return false;
			}


			/***
			 *	return $name plural version
			 *
			 *	@return string
			 */
			public static function pluralize( $name ){
			
				if( empty( $name ) ) return '';
			
				$irregular = array(
					'abdomen' => 'abdomens',
					'alemão' => 'alemães',
					'artesã' => 'artesãos',
					'ás' => 'áses',
					'bencão' => 'bencãos',
					'cão' => 'cães',
					'campus' => 'campi',
					'capelão' => 'capelães',
					'capitão' => 'capitães',
					'chão' => 'chãos',
					'charlatão' => 'charlatães',
					'cidadão' => 'cidadãos',
					'consul' => 'consules',
					'cristão' => 'cristãos',
					'difícil' => 'difíceis',
					'email' => 'emails',
					'escrivão' => 'escrivães',
					'fóssil' => 'fósseis',
					'germens' => 'germen',
					'grão' => 'grãos',
					'hífen' => 'hífens',
					'irmão' => 'irmãos',
					'liquens' => 'liquen',
					'mal' => 'males',
					'mão' => 'mãos',
					'orfão' => 'orfãos',
					'país' => 'países',
					'pai' => 'pais',
					'pão' => 'pães',
					'projétil' => 'projéteis',
					'réptil' => 'répteis',
					'sacristão' => 'sacristães',
					'sotão' => 'sotãos',
					'tabelião' => 'tabeliães',
					'gás' => 'gases',
					'álcool' => 'álcoois',
					// 'homem' => 'homens',
					'mulher' => 'mulheres',
				);
				$irregular = apply_filters( 'r/str/pluralize/irregular', $irregular );
				
				if( isset( $irregular[ $name ] ) ){
					return $irregular[ $name ];
				}
				
				
				// não aplicaveis
				$not_applicable = explode( ' ', 'atlas lapis lápis onibus ônibus pires virus status' );
				$not_applicable = apply_filters( 'r/str/pluralize/not_applicable', $not_applicable );
				
				if( in_array( $name, $not_applicable ) ){
					return $name;
				}

				
				// already plural
				if( substr( $name, -1 ) == 's' ){
					return $name;
				}
				
				// default plural
				$plural = array(
					'/^(.*)ão$/i'			=> '\1ões',
					'/^(.*)(r|s|z)$/i'		=> '\1\2es',
					'/^(.*)(a|e|o|u)l$/i'	=> '\1\2is',
					'/^(.*)il$/i'			=>'\1\2is',
					'/^(.*)(m|n)$/i'		=> '\1ns',
					'/^(.*)$/i'				=> '\1s',
				);
				$plural = apply_filters( 'r/str/pluralize/plural_regex', $plural );

				foreach( $plural as $reg => $rep ){
					if( preg_match( $reg, $name ) ){
						$name = preg_replace( $reg, $rep, $name );
						break;
					}
				}

				$name = apply_filters( 'r/str/pluralize/output', $name );
				return $name;
			}

		}
	}

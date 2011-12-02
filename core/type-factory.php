<?php

class P2P_Connection_Type_Factory {
	private static $instances = array();

	public static function register( $args ) {
		$args = wp_parse_args( $args, array(
			'name' => false,
			'from_object' => 'post',
			'to_object' => 'post',
			'from' => 'post',
			'to' => 'post',
			'from_query_vars' => array(),
			'to_query_vars' => array(),
			'data' => array(),
			'cardinality' => 'many-to-many',
			'prevent_duplicates' => true,
			'sortable' => false,
			'title' => array(),
			'from_labels' => '',
			'to_labels' => '',
			'reciprocal' => false,
		) );

		$sides = array();

		foreach ( array( 'from', 'to' ) as $direction ) {
			$post_type = _p2p_pluck( $args, $direction );

			if ( 'post' == $args[ $direction . '_object' ] )
				$args[ $direction . '_query_vars' ]['post_type'] = $post_type;
		}

		if ( !$args['name'] ) {
			$args['name'] = md5( serialize( array_values( wp_array_slice_assoc( $args, array(
				'from_object', 'to_object',
				'from_query_vars', 'to_query_vars',
				'data'
			) ) ) ) );
		}

		if ( $args['from_object'] == $args['to_object'] && 'post' == $args['from_object'] )
			$class = 'P2P_Connection_Type';
		else
			$class = 'Generic_Connection_Type';

		$ctype = new $class( $args );

		if ( isset( self::$instances[ $ctype->name ] ) ) {
			trigger_error( 'Connection type is already defined.', E_USER_NOTICE );
		}

		self::$instances[ $ctype->name ] = $ctype;

		return $ctype;
	}

	public static function get_all_instances() {
		return self::$instances;
	}

	public static function get_instance( $hash ) {
		if ( isset( self::$instances[ $hash ] ) )
			return self::$instances[ $hash ];

		return false;
	}
}


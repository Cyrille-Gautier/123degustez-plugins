<?php
namespace Jet_Engine\Modules\Data_Stores;

/**
 * Database manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base DB class
 */
class DB extends \Jet_Engine_Base_DB {

	public static $prefix = 'jet_data_store_';

	/**
	 * Insert
	 *
	 * @param  array $data Data to insert
	 * @return mixed
	 */
	public function insert( $data = array() ) {

		if ( ! empty( $this->defaults ) ) {
			foreach ( $this->defaults as $default_key => $default_value ) {
				if ( ! isset( $data[ $default_key ] ) ) {
					$data[ $default_key ] = $default_value;
				}
			}
		}

		if ( empty( $data['created'] ) ) {
			$data['created'] = time();
		}

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value        = maybe_serialize( $value );
				$data[ $key ] = $value;
			}
		}

		$inserted = self::wpdb()->insert( $this->table(), $data );

		if ( $inserted ) {
			return self::wpdb()->insert_id;
		} else {
			return false;
		}
	}

	/**
	 * Update
	 *
	 * @param  array  $new_data New data to update
	 * @param  array  $where    Where arguments
	 * @return mixed
	 */
	public function update( $new_data = array(), $where = array() ) {

		if ( ! empty( $this->defaults ) ) {
			foreach ( $this->defaults as $default_key => $default_value ) {
				if ( ! isset( $data[ $default_key ] ) ) {
					$data[ $default_key ] = $default_value;
				}
			}
		}

		foreach ( $new_data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value            = maybe_serialize( $value );
				$new_data[ $key ] = $value;
			}
		}

		$result = self::wpdb()->update( $this->table(), $new_data, $where );

		/**
		 * https://github.com/Crocoblock/suggestions/issues/7774
		 */
		$this->reset_found_items_cache();

		return $result;
	}

	/**
	 * Returns table columns schema
	 *
	 * @return string
	 */
	public function get_table_schema() {

		$charset_collate = $this->wpdb()->get_charset_collate();
		$table           = $this->table();
		$default_columns = array(
			'_ID'     => 'bigint(20) NOT NULL AUTO_INCREMENT',
			'created' => 'text',
		);

		$additional_columns = $this->schema;
		$columns_schema     = '';

		foreach ( $default_columns as $column => $desc ) {
			$columns_schema .= $column . ' ' . $desc . ',';
		}

		if ( is_array( $additional_columns ) && ! empty( $additional_columns ) ) {
			foreach ( $additional_columns as $column => $definition ) {

				if ( ! $definition ) {
					$definition = 'text';
				}

				$columns_schema .= $column . ' ' . $definition . ',';

			}
		}

		return "CREATE TABLE $table (
			$columns_schema
			PRIMARY KEY (_ID)
		) $charset_collate;";

	}

	/**
	 * Query data from db table
	 *
	 * @return mixed
	 */
	public function query( $args = array(), $limit = 0, $offset = 0, $order = array(), $filter = false, $rel = 'AND' ) {

		$table = $this->table();

		$query = "SELECT * FROM $table";

		if ( ! $rel ) {
			$rel = 'AND';
		}

		$search = ! empty( $args['_search'] ) ? $args['_search'] : false;

		if ( $search ) {
			unset( $args['_search'] );
		}

		$where  = $this->add_where_args( $args, $rel );
		$query .= $where;

		if ( $search ) {

			$search_str = array();
			$keyword    = $search['keyword'];
			$fields     = ! empty( $search['fields'] ) ? $search['fields'] : false;

			if ( ! $fields ) {
				$fields = array_keys( $this->schema );
			}

			if ( $fields ) {
				foreach ( $fields as $field ) {
					$search_str[] = sprintf( '`%1$s` LIKE \'%%%2$s%%\'', $field, $keyword );
				}

				$search_str = implode( ' OR ', $search_str );
			}

			if ( ! empty( $search_str ) ) {

				if ( $where ) {
					$query .= ' ' . $rel;
				} else {
					$query .= ' WHERE';
				}

				$query .= ' (' . $search_str . ')';

			}
		}

		if ( empty( $order ) ) {
			$order = array( array(
				'orderby' => '_ID',
				'order'   => 'desc',
			) );
		}

		$query .= $this->add_order_args( $order );

		if ( intval( $limit ) > 0 ) {
			$limit  = absint( $limit );
			$offset = absint( $offset );
			$query .= " LIMIT $offset, $limit";
		}

		$raw = self::wpdb()->get_results( $query, $this->get_format_flag() );

		if ( $filter && is_callable( $filter ) ) {
			return array_map( $filter, $raw );
		} else {
			return array_map( function( $item ) {

				if ( is_array( $item ) ) {
					foreach ( $item as $key => $value ) {

						$value = maybe_unserialize( $value );

						if ( is_string( $value ) ) {
							$item[ $key ] = wp_unslash( $value );
						} else {
							$item[ $key ] = $value;
						}
					}

				} elseif ( is_object( $item ) ) {

					foreach ( get_object_vars( $item )  as $key => $value ) {

						$value = maybe_unserialize( $value );

						if ( is_string( $value ) ) {
							$item->$key = wp_unslash( $value );
						} else {
							$item->$key = $value;
						}

					}

				}

				return $item;

			}, $raw );
		}

	}

	/**
	 * Delete row
	 */
	public function delete( $where = array() ) {

		if ( empty( $where ) ) {
			return false;
		}

		$table = $this->table();
		$query = "DELETE FROM $table";

		$query .= $this->add_where_args( $where );

		return self::wpdb()->query( $query );
	}

}

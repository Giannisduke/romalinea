<?php

/**
 * SearchWP PageBuilder.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

/**
 * Class PageBuilder is responsible for customizing SearchWP's Native
 * implementation to work with page builder queries by allowing Native
 * (and forcing Native) to run outside the main query.
 *
 * @since 4.1.5
 */
abstract class PageBuilder {

	/**
	 * Name used for canonical reference to Integration.
	 *
	 * @since 4.1.5
	 * @var   string
	 */
	protected $name = 'pagebuilder';

	/**
	 * Modify Native behavior by forcing execution outside the main query.
	 *
	 * @since 4.1.5
	 * @return void
	 */
	public function modify_native_behavior() {
		if ( ! apply_filters( 'searchwp\integration\pagebuilder\enabled', true, [
			'context' => $this->name
		] ) ) {
			return;
		}

		add_filter( 'searchwp\native\short_circuit', function( $short_circuit, $query ) {
			if ( $query->is_main_query() && isset( $_GET['s'] ) ) {
				$short_circuit = false;
			}

			return $short_circuit;
		}, 5, 2 );

		add_filter( 'searchwp\native\force', function() {
			return isset( $_GET['s'] );
		}, 990 );

		add_filter( 'searchwp\native\strict', function() {
			return ! isset( $_GET['s'] );
		}, 990 );

		add_action( 'pre_get_posts', function( $query ) {
			if ( ! $query->is_main_query() || ! isset( $_GET['s'] ) ) {
				return;
			}

			$engine = apply_filters( 'searchwp\integration\pagebuilder\engine', 'default', [
				'context' => $this->name,
				'query'   => $query,
			] );

			// Allow short circuit by emptying engine.
			if ( empty( $engine ) ) {
				return;
			}

			$query->set( 'searchwp', $engine );

			add_filter( 'searchwp\native\args', function( $args, $query ) use ( $engine ) {
				$args['s']      = stripslashes( $_GET['s'] );
				$args['engine'] = $engine;

				return $args;
			}, 990, 2 );
		}, -1 );
	}
}

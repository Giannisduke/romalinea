<?php

namespace WCML\Rest\Language;

use WPML\FP\Obj;

class Set {

	public static function fromUrlQueryVar() {
		$lang = self::sanitize( Obj::prop( 'lang', $_GET ) );

		if ( $lang ) {
			wpml_switch_language_action( $lang );
		}
	}

	/**
	 * @param \WP_REST_Response|\WP_HTTP_Response|\WP_Error|mixed $response
	 * @param array                                               $handler
	 * @param \WP_REST_Request                                    $request
	 *
	 * @return \WP_REST_Response|\WP_HTTP_Response|\WP_Error|mixed
	 */
	public static function beforeCallbacks( $response, $handler, \WP_REST_Request $request ) {
		$lang = self::getFromRequestParams( $request )
			?: self::getFromProduct( $handler, $request );

		if ( $lang ) {
			wpml_switch_language_action( $lang );
		}

		return $response;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return string
	 */
	private static function getFromRequestParams( \WP_REST_Request $request ) {
		return self::sanitize( $request->get_param( 'lang' ) );
	}

	/**
	 * @param array            $handler
	 * @param \WP_REST_Request $request
	 *
	 * @return string
	 */
	private static function getFromProduct( $handler, \WP_REST_Request $request ) {
		if (
			Obj::path( [ 'callback', 0 ], $handler ) instanceof \WC_REST_Products_Controller
			&& $request->get_param( 'id' )
		) {
			return (string) Obj::prop(
				'language_code',
				apply_filters( 'wpml_post_language_details', [], $request->get_param( 'id' ) )
			);
		}

		return '';
	}

	/**
	 * @param string $lang
	 *
	 * @return string
	 */
	private static function sanitize( $lang ) {
		return filter_var( $lang, FILTER_SANITIZE_STRING );
	}
}

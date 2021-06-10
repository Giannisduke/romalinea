<?php

/**
 * SearchWP Divi.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

/**
 * Class Divi is responsible for customizing SearchWP's Native implementation to work with Divi queries.
 *
 * @since 4.1.5
 */
class Divi extends PageBuilder {

	/**
	 * Name used for canonical reference to Integration.
	 *
	 * @since 4.1.5
	 * @var   string
	 */
	protected $name = 'divi';
}

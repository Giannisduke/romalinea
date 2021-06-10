<?php

/**
 * SearchWP BeaverBuilder.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

/**
 * Class BeaverBuilder is responsible for customizing SearchWP's Native implementation to work with BeaverBuilder queries.
 *
 * @since 4.1.5
 */
class BeaverBuilder extends PageBuilder {

	/**
	 * Name used for canonical reference to Integration.
	 *
	 * @since 4.1.5
	 * @var   string
	 */
	protected $name = 'beaver-builder';
}

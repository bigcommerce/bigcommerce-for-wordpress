<?php

namespace BigCommerce\Manager;

interface Task {

	public function handle( array $args ) : bool;

}

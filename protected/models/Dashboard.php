<?php

/**
 * Dummy model to used for things such as getnicename // beware cant be instantiated or will crash as no table
 */
class Dashboard extends ActiveRecord
{
	static function primaryKeyName() {
		return 'dummy';
	}
}
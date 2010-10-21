<?php
/* Category Fixture generated on: 2010-10-07 21:10:23 : 1286455403 */
class CategoryFixture extends CakeTestFixture {
	var $name = 'Category';

	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'key' => 'index'),
		'lft' => array('type' => 'integer', 'key' => 'index'),
		'rght' => array('type' => 'integer', 'key' => 'index'),
		'category' => array('type' => 'string', 'null' => false),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'parent_id' => array('column' => 'parent_id', 'unique' => 0), 'lft' => array('column' => 'lft', 'unique' => 0), 'rght' => array('column' => 'rght', 'unique' => 0)),
	);

	var $records = array();
}
?>
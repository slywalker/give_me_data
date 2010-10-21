<?php
function dearr($var = false, $showHtml = false, $showFrom = true) {
	if (Configure::read() > 0) {
		if ($showFrom) {
			$calledFrom = debug_backtrace();
			echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
			echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
		}
		echo "\n<pre class=\"cake-debug\">\n";

		$var = print_r($var, true);
		$var = preg_replace('/Array\n[^\(]*\(/', 'array(', $var);
		$var = preg_replace('/\[([^]]+)\]/', '\'$1\'', $var);
		$var = preg_replace('/=> ((?!array\().*)/', '=> \'$1\',', $var);
		$var = str_replace(')', '),', $var);
		if ($showHtml) {
			$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
		}
		echo $var . "\n</pre>\n";
	}
}

/* Discount Test cases generated on: 2010-10-07 22:10:03 : 1286459283*/
App::import('Model', 'Dummy.Dummy');

class Author extends AppModel {
	var $name = 'Author';
	var $hasMany = array('Post');
}

class Category extends AppModel {
	var $name = 'Category';
	var $hasMany = array('Post');
}

class Post extends AppModel {
	var $name = 'Post';
	var $belongsTo = array('Author', 'Category');
	var $hasAndBelongsToMany = array('Tag' => array('with' => 'PostsTag'));
}

class Tag extends AppModel {
	var $name = 'Tag';
	var $hasAndBelongsToMany = array('Post' => array('with' => 'PostsTag'));
}

class PostsTag extends AppModel {
	var $name = 'PostsTag';
	var $belongsTo = array('Post', 'Tag');
}

class DummyTestCase extends CakeTestCase {
	var $fixtures = array('plugin.dummy.dummy', 'plugin.dummy.author', 'plugin.dummy.post', 'plugin.dummy.tag', 'plugin.dummy.posts_tag', 'plugin.dummy.category');

	function startTest() {
		$this->Dummy =& ClassRegistry::init('Dummy');
	}

	function endTest() {
		unset($this->Dummy);
		ClassRegistry::flush();
	}

	function testInsertDummyAll() {
		$result = $this->Dummy->insertDummyAll(4);
		$this->assertTrue($result);
		$result = ClassRegistry::init('Post')->find('count');
		$this->assertTrue(($result > 0));

		$result = ClassRegistry::init('Post')->find('all');
		// debug($result);
	}

	function testInsertDummy() {
		$row = 4;
		$result = $this->Dummy->insertDummy('Tag', array('limit' => $row));
		$this->assertTrue($result);
		$result = ClassRegistry::init('Tag')->find('count');
		$this->assertEqual($result, $row);

		$result = $this->Dummy->insertDummy('Author', array('limit' => $row));
		$this->assertTrue($result);

		$result = $this->Dummy->insertDummy('Category', array('limit' => $row));
		$this->assertTrue($result);

		// $result = ClassRegistry::init('Author')->find('count');
		// $this->assertEqual($result, $row);
		//
		// $result = ClassRegistry::init('Post')->find('count');
		// $this->assertEqual($result, ($row * $row));
		//
		$result = ClassRegistry::init('Post')->find('all');
		// debug($result);
	}

	function testInitTables() {
		$result = $this->Dummy->initTables();
		$this->assertTrue($result);

		$result = $this->Dummy->find('count');
		$this->assertEqual($result, 30);
	}

	function testGetFields() {
		$result = $this->Dummy->getFields('Author');
		$expected = array(
			'id' => array(
				'type' => 'integer',
				'null' => false,
				'default' => null,
				'length' => 11,
				'key' => 'primary',
			),
			'user' => array(
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 255,
				'collate' => 'utf8_unicode_ci',
				'charset' => 'utf8',
			),
			'password' => array(
				'type' => 'string',
				'null' => true,
				'default' => null,
				'length' => 255,
				'collate' => 'utf8_unicode_ci',
				'charset' => 'utf8',
			),
			'created' => array(
				'type' => 'datetime',
				'null' => true,
				'default' => null,
				'length' => null,
			),
			'updated' => array(
				'type' => 'datetime',
				'null' => true,
				'default' => null,
				'length' => null,
			),
		);
		$this->assertEqual($result, $expected);
	}

	function testSortModels() {
		$modelNames = $this->Dummy->getAllModels();
		$result = $this->Dummy->sortModels($modelNames);
		$expected = array('Tag', 'Author', 'Category', 'Post', 'PostsTag');
		$this->assertEqual($result, $expected);
	}

	function testGetAllModels() {
		$result = $this->Dummy->getAllModels();
		$expected = array('Author', 'Category', 'Post', 'PostsTag', 'Tag');
		$this->assertEqual($result, $expected);
	}

	function testGetAllTables() {
		$result = $this->Dummy->getAllTables();
		$expected = array('authors', 'categories', 'posts', 'posts_tags', 'tags');
		$this->assertEqual($result, $expected);
	}
}
?>
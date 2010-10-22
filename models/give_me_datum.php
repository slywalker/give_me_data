<?php
App::import('Vendors', 'GiveMeData.Fake', array('file' => 'php-faker/faker.php'));

class GiveMeDatum extends GiveMeDataAppModel {
	var $name = 'GiveMeDatum';
	var $useTable = false;
	var $actsAs = array('Tree');
	var $order = array('lft' => 'ASC');

	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->_Faker = new Faker;
	}

	function insertDataAll($limit = 20, $useDbConfig = null) {
		$modelNames = $this->getAllModels($useDbConfig);
		$modelNames = $this->sortModels($modelNames);
		foreach ($modelNames as $modelNames) {
			if (!$this->insertData($modelNames, array('limit' => $limit))) {
				return false;
			}
		}
		return true;
	}

	function insertData($modelName, $options = array()) {
		$default = array(
			'limit' => 20,
			'insertId' => array(),
			'cascade' => false,
		);
		$options = Set::merge($default, $options);

		$this->__initModel($modelName);

		extract($this->__compactAssoc($modelName));

		$fields = $this->getFields($modelName);
		$foreignKeys = Set::extract($belongsTo, '{s}.foreignKey');
		$foreignKeys += Set::extract($hasAndBelongsToMany, '{s}.foreignKey');

		for ($i=0; $i < $options['limit']; $i++) {
			$_options = array('foreignKeys' => $foreignKeys, 'insertId' => $options['insertId']);
			$data = $this->_makeRecorde($modelName, $fields, $_options);

			if (!$options['cascade']) {
				$conditions = array();
				foreach ($foreignKeys as $_foreignKey) {
					if (isset($data[$_foreignKey])) {
						$conditions[$_foreignKey] = $data[$_foreignKey];
					}
				}
				if ($conditions && $this->{$modelName}->hasAny($conditions)) {
					continue;
				}
			}

			$this->{$modelName}->create();
			if (!$this->{$modelName}->save($data, array('validate' => false))) {
				return false;
			}
			$foreignKey = Inflector::underscore($modelName) . '_id';
			$options['insertId'][$foreignKey] = $this->{$modelName}->getInsertID();

			if ($options['cascade']) {
				$_options = array_merge($options, array('limit' => 1, 'cascade' => false));
				foreach ($hasOne as $alias => $assoc) {
					if (!$this->insertData($assoc['className'], $_options)) {
						return false;
					}
				}

				$_options = array_merge($options, array('cascade' => false));
				foreach ($hasMany as $alias => $assoc) {
					if (!$this->insertData($assoc['className'], $_options)) {
						return false;
					}
				}

				$_options = array_merge($options, array('limit' => 5, 'cascade' => false));
				foreach ($hasAndBelongsToMany as $alias => $assoc) {
					if (!empty($this->__ids[$assoc['associationForeignKey']])) {
						if (!$this->insertData($assoc['with'], 5, $_options)) {
							return false;
						}
					}
				}
			}
		}

		if (!$this->__setForeignKeyIds($modelName)) {
			return false;
		}

		return true;
	}

	function __setForeignKeyIds($modelName) {
		$this->__initModel($modelName);
		extract($this->__compactAssoc($modelName));

		if (!empty($hasOne) || !empty($hasMany) || !empty($hasAndBelongsToMany)) {
			$foreignKey = Inflector::underscore($modelName) . '_id';
			if (!isset($this->__ids[$foreignKey])) {
				$ids = $this->{$modelName}->find('list', array('fields' => array('id')));
				if (empty($ids)) {
					return false;
				}
				sort($ids);
				$this->__ids[$foreignKey] = $ids;
			}
		}
		return true;
	}

	function _makeRecorde($modelName, $fields, $options = array()) {
		$default = array(
			'foreignKeys' => array(),
			'insertId' => array(),
			'ignoreFields' => array('id', 'lft', 'rght'),
		);
		$options = array_merge($default, $options);

		$record = array();
		foreach ($fields as $fieldName => $field) {
			if (in_array($fieldName, $options['ignoreFields'])) {
				continue;
			}
			if (isset($field['key']) && $field['key'] === 'primary') {
				continue;
			}

			$insert = $this->__caseForeignKey($modelName, $fieldName, $options);
			if ($insert === false) {
				continue;
			}

			if (is_null($insert)) {
				$insert = $this->__caseGuess($fieldName, $field);
			}

			if (is_null($insert)) {
				$insert = $this->__caseDefault($field);
			}

			$record[$fieldName] = $insert;
		}

		return $record;
	}

	function __caseForeignKey($modelName, $fieldName, $options) {
		if (!empty($options['insertId'][$fieldName])) {
			return $options['insertId'][$fieldName];
		}

		if ($fieldName === 'parent_id' || in_array($fieldName, $options['foreignKeys'])) {
			if ($fieldName === 'parent_id') {
				$fieldName = Inflector::underscore($modelName) . '_id';
			}
			$count = isset($this->__ids[$fieldName]) ? count($this->__ids[$fieldName]) : 0;
			if (!$count) {
				return false;
			}
			$key = mt_rand(0, ($count - 1));
			return $this->__ids[$fieldName][$key];
		}

		return null;
	}

	function __caseGuess($fieldName, $field) {
		if (in_array($fieldName, array('email')) && $field['type'] === 'string') {
			return $this->_Faker->Internet->email();
		}

		if (in_array($fieldName, array('pass', 'password')) && $field['type'] === 'string') {
			return $this->_Faker->bothify('?#?#?#');
		}

		if (in_array($fieldName, array('phone', 'tel')) && $field['type'] === 'string') {
			return $this->_Faker->Phone_Number->phone_number();
		}

		if (in_array($fieldName, array('name', 'user', 'username', 'tag', 'category')) && $field['type'] === 'string') {
			return $this->_Faker->Internet->user_name();
		}

		if (in_array($fieldName, array('slug')) && $field['type'] === 'string') {
			return $this->_Faker->lexify('???_???_???????');
		}

		return null;
	}

	function __caseDefault($field) {
		switch ($field['type']) {
			case 'integer':
			case 'float':
				$insert = $this->_Faker->numerify('####');
			break;

			case 'string':
			case 'binary':
				$insert = $this->_Faker->Lorem->sentence();
				if (!empty($field['length'])) {
					 $insert = substr($insert, 0, (int)$field['length'] - 2);
				}
			break;

			case 'timestamp':
				$insert = time();
			break;

			case 'datetime':
				$insert = $this->_Faker->numerify('20##-0#-2# 0#:2#:3#');
			break;

			case 'date':
				$insert = $this->_Faker->numerify('20##-0#-2#');
			break;

			case 'time':
				$insert = $this->_Faker->numerify('0#:2#:3#');
			break;

			case 'boolean':
				$insert = mt_rand(0, 1);
			break;

			case 'text':
				$insert = $this->_Faker->Lorem->paragraph();
			break;
		}
		return $insert;
	}

	function initTables($useDbConfig = null) {
		$modelNames = $this->getAllModels();
		$modelNames = $this->sortModels($modelNames);

		foreach ($modelNames as $modelName) {
			$fields = $this->getFields($modelName);

			$options = array(
				'belongsTo' => $this->{$modelName}->belongsTo,
				'hasOne' => $this->{$modelName}->hasOne,
				'hasMany' => $this->{$modelName}->hasMany,
				'hasAndBelongsToMany' => $this->{$modelName}->hasAndBelongsToMany,
			);
			$data = array(
				'type' => 'model',
				'name' => $modelName,
				'options' => serialize($options),
			);
			$this->create();
			if (!$this->save($data)) {
				return false;
			}
			$parent_id = $this->getInsertID();

			foreach ($fields as $fieldName => $options) {
				$data = array(
					'parent_id' => $parent_id,
					'type' => 'field',
					'name' => $fieldName,
					'field_type' => $options['type'],
					'length' => isset($options['length']) ? $options['length'] : 0,
					'key' => isset($options['key']) ? $options['key'] : '',
					'options' => serialize($options),
				);
				$this->create();
				if (!$this->save($data)) {
					return false;
				}
			}
		}

		return true;
	}

	function getFields($modelName) {
		$this->__initModel($modelName);
		return $this->{$modelName}->schema(true);
	}

	function sortModels($modelNames) {
		$score = array();
		foreach ($modelNames as $modelName) {
			$score[$modelName] = $this->__loadAssocScore($modelName);
		}
		asort($score);

		$result = array();
		foreach ($score as $modelName => $_score) {
			$result[] = $modelName;
		}

		return $result;
	}

	function __loadAssocScore($modelName, $cascade = true) {
		$this->__initModel($modelName);

		$assocs = array(
			'belongsTo' => 5,
			'hasOne' => 1,
			'hasMany' => 1,
			'hasAndBelongsToMany' => -1,
		);

		$score = 0;
		foreach ($assocs as $assoc => $weight) {
			$_assoc = $this->{$modelName}->{$assoc};
			$score += count($_assoc) * $weight;
			// if ($cascade) {
			// 	foreach ($_assoc as $options) {
			// 		$score += $this->__loadAssocScore($options['className'], false);
			// 	}
			// }
		}

		return $score;
	}

	function getAllModels($useDbConfig = null) {
		$tables = $this->getAllTables($useDbConfig);

		$modelNames = array();
		foreach ($tables as $table) {
			$modelName = Inflector::camelize(Inflector::singularize($table));
			$this->__initModel($modelName);
			if (is_object($this->{$modelName}) && $modelName !== $this->name) {
				$modelNames[] = $modelName;
			}
		}
		return $modelNames;
	}

	function getAllTables($useDbConfig = null) {
		if (!isset($useDbConfig)) {
			$useDbConfig = $this->useDbConfig;
		}
		App::import('Model', 'ConnectionManager', false);

		$tables = array();
		$db =& ConnectionManager::getDataSource($useDbConfig);
		$db->cacheSources = false;
		$usePrefix = empty($db->config['prefix']) ? '' : $db->config['prefix'];
		if ($usePrefix) {
			foreach ($db->listSources() as $table) {
				if (!strncmp($table, $usePrefix, strlen($usePrefix))) {
					$tables[] = substr($table, strlen($usePrefix));
				}
			}
		} else {
			$tables = $db->listSources();
		}

		foreach ($tables as $key => $table) {
			if ($table === $this->useTable) {
				unset($tables[$key]);
			}
			sort($tables);
		}

		return $tables;
	}

	function __initModel($modelName) {
		if (!isset($this->{$modelName})) {
			$this->{$modelName} = ClassRegistry::init($modelName);
		}
	}

	function __compactAssoc($modelName) {
		$belongsTo = $this->{$modelName}->belongsTo;
		$hasOne = $this->{$modelName}->hasOne;
		$hasMany = $this->{$modelName}->hasMany;
		$hasAndBelongsToMany = $this->{$modelName}->hasAndBelongsToMany;

		return compact('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
	}
}
?>
<?php
class GiveMeDataController extends GiveMeDataAppController {
	var $name = 'GiveMeData';

	function index() {
		$models = $this->GiveMeDatum->getAllModels();
		$this->set(compact('models'));
	}

	function please($limit = 20) {
		if ($this->GiveMeDatum->insertDataAll(compact('limit'))) {
			$this->Session->setFlash(__('You got data!', true));
		} else {
			$this->Session->setFlash(__('You could not get data...', true));
		}
		$this->redirect($this->referer());
	}

	function more($cascade = null, $model = null) {
		if ($cascade !== 'more') {
			$model = $cascade;
			$cascade = false;
		} else {
			$cascade = true;
		}
		if (is_null($model)) {
			$this->Session->setFlash(__('What\'s model?', true));
			$this->redirect($this->referer());
		}

		if ($this->GiveMeDatum->insertData($model, compact('cascade'))) {
			$this->Session->setFlash(__('You got more data!', true));
		} else {
			$this->Session->setFlash(__('You could not get more data...', true));
		}
		$this->redirect($this->referer());
	}

	// function init_tables() {
	// 	if ($this->GiveMeDatum->initTables()) {
	// 		$this->Session->setFlash(__('Initialize done', true));
	// 	} else {
	// 		$this->Session->setFlash(__('Initialize error', true));
	// 	}
	// 	$this->redirect(array('action' => 'index'));
	// }
	//
	// function generate($id = null) {
	// 	if ($this->GiveMeDatum->generateGiveMeData($id)) {
	// 		$this->Session->setFlash(__('Generate done', true));
	// 	} else {
	// 		$this->Session->setFlash(__('Generate error', true));
	// 	}
	// 	$this->redirect($this->referer());
	// }
}
?>
<?php
class GiveMeDataController extends GiveMeDataAppController {
	var $name = 'GiveMeData';

	function index() {
		$giveMeData = $this->GiveMeDatum->find('threaded');
		$this->set(compact('giveMeData'));
	}

	function please($limit = 20) {
		if ($this->GiveMeDatum->insertDataAll($limit)) {
			$this->Session->setFlash(__('You got data!', true));
		} else {
			$this->Session->setFlash(__('You could not get data...', true));
		}
		$this->redirect(array('action' => 'index'));
	}

	function init_tables() {
		if ($this->GiveMeDatum->initTables()) {
			$this->Session->setFlash(__('Initialize done', true));
		} else {
			$this->Session->setFlash(__('Initialize error', true));
		}
		$this->redirect(array('action' => 'index'));
	}

	function generate($id = null) {
		if ($this->GiveMeDatum->generateGiveMeData($id)) {
			$this->Session->setFlash(__('Generate done', true));
		} else {
			$this->Session->setFlash(__('Generate error', true));
		}
		$this->redirect($this->referer());
	}
}
?>
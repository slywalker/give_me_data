<?php
class DummyController extends DummyAppController {
	var $name = 'Dummy';

	function index() {
		$dummys = $this->Dummy->find('threaded');
		$this->set(compact('dummys'));
	}

	function init_tables() {
		$this->Dummy->initTables();
		$this->redirect(array('action' => 'index'));
	}

	function generate($id = null) {
		if ($this->Dummy->generateDummy($id)) {
			$this->Session->setFlash(__('Generate done', true));
		} else {
			$this->Session->setFlash(__('Generate error', true));
		}
		$this->redirect($this->referer());
	}
}
?>
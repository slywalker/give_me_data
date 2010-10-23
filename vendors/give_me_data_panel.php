<?php
class GiveMeDataPanel extends DebugPanel {
	var $plugin = 'GiveMeData';
	var $title = 'Give me data!';

	function startup(&$controller) { }

	function beforeRender(&$controller) {
		return ClassRegistry::init('GiveMeData.GiveMeDatum')->getAllModels();
	}
}
?>
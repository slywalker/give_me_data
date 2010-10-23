<div class="debug-info">
	<h2><?php echo __d('give_me_data', 'Give me data!'); ?></h2>
	<?php
	echo $this->Html->link(__d('give_me_data', 'Give me all!', true), array(
		'plugin' => 'give_me_data',
		'controller' => 'give_me_data',
		'action' => 'please',
	));

	$rows = array();
	foreach ($content as $model) {
		$rows[] = array(
			$model,
			$this->Html->link(__d('give_me_data', 'More!', true), array(
				'plugin' => 'give_me_data',
				'controller' => 'give_me_data',
				'action' => 'more',
				$model,
			)),
			$this->Html->link(__d('give_me_data', 'More and More!', true), array(
				'plugin' => 'give_me_data',
				'controller' => 'give_me_data',
				'action' => 'more',
				'more',
				$model,
			)),
		);
	}
	echo $toolbar->table($rows, array(), array('title' => 'Models'));
	?>
</div>


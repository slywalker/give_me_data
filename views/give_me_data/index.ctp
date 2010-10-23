<h2><?php echo $this->Html->link(__d('give_me_data', 'Give Me Data!', true), array('action' => 'please')); ?></h2>
<table>
<?php foreach ($models as $model): ?>
	<tr>
		<td><?php echo $model; ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__d('give_me_data', 'More!', true), array('action' => 'more', $model)); ?>
			<?php echo $this->Html->link(__d('give_me_data', 'More and More!', true), array('action' => 'more', 'more', $model)); ?>
		</td>
	</tr>
<?php endforeach ?>
</table>
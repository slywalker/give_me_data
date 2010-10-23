<h2><?php echo $this->Html->link(__('Give Me Data!', true), array('action' => 'please')); ?></h2>
<table>
<?php foreach ($models as $model): ?>
	<tr>
		<td><?php echo $model; ?></td>
		<td class="actions">
			<?php echo $this->Html->link(__('More!', true), array('action' => 'more', $model)); ?>
			<?php echo $this->Html->link(__('More and More!', true), array('action' => 'more', 'more', $model)); ?>
		</td>
	</tr>
<?php endforeach ?>
</table>
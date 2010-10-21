<div class="dummy index">
	<h2><?php __('Dummy') ?></h2>
<?php foreach ($dummys as $dummy): ?>
	<h3><a name="<?php echo $dummy['Dummy']['name'] ?>"><?php echo $dummy['Dummy']['name']; ?></a></h3>

	<h4><?php __('Associations'); ?></h4>
	<?php
	$options = unserialize($dummy['Dummy']['options']);
	$associations = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
	?>
	<table>
	<?php foreach ($associations as $association): ?>
		<?php if (!empty($options[$association])) : ?>
		<tr>
			<td style="width:20em;"><?php echo $association; ?></td>
			<td>
				<?php
				$assocs = array_keys($options[$association]);
				foreach ($assocs as $key => $assoc) {
					$assocs[$key] = $this->Html->link($assoc, '#' . $assoc);
				}
				echo implode(' ', $assocs);
				?>
			</td>
		</tr>
		<?php endif; ?>
	<?php endforeach ?>
	</table>
	<?php echo $this->Html->link(__('Generate', true), array('action' => 'generate', $dummy['Dummy']['id'])); ?>

	<h4><?php __('Fields'); ?></h4>
	<table>
		<?php foreach ($dummy['children'] as $children): ?>
			<tr>
				<td><?php echo $children['Dummy']['name']; ?></td>
				<td style="width:10em;"><?php echo $children['Dummy']['field_type']; ?></td>
				<td style="width:5em;"><?php echo $children['Dummy']['length']; ?></td>
				<td style="width:10em;"><?php echo $children['Dummy']['key']; ?></td>
			</tr>
		<?php endforeach ?>
	</table>
<?php endforeach ?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Init Tables', true), array('action' => 'init_tables')); ?></li>
	</ul>
</div>
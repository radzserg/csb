

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'db-app-error-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
        array(
            'name' => 'ip',
            'value' => 'long2ip($data->ip)',
        ),
        array(
            'name' => 'create_time',
            'type' => 'datetime',
            'value' => 'strtotime($data->create_time)'
        ),
        array(
            'name' => 'till_time',
            'type' => 'datetime',
            'value' => 'strtotime($data->till_time)'
        ),
        array(
            'name' => 'user_id',
        ),
        array(
            'name' => 'details',
            'type' => 'html',
            'value' => 'htmlspecialchars($data->details)',
        ),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view}{delete}',
		),
	),
)); ?>

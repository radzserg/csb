
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'db-app-error-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
        array(
            'name' => 'ip',
            'value' => 'long2ip($data->ip)',
        ),
        array(
            'name' => 'time',
            'type' => 'datetime',
            'value' => 'strtotime($data->time)'
        ),
	),
)); ?>

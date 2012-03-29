<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
        array(
            'name' => 'ip',
            'value' => long2ip($model->ip),
        ),
        array(
            'name' => 'create_time',
            'type' => 'datetime',
            'value' => strtotime($model->create_time)
        ),
        array(
            'name' => 'till_time',
            'type' => 'datetime',
            'value' => strtotime($model->till_time)
        ),
        array(
            'name' => 'user_id',
        ),
        array(
            'name' => 'details',
            'type' => 'html',
            'value' => '<pre>' . htmlspecialchars($model->details) . '</pre>',
        ),
        array(
            'name' => 'ip_info',
            'type' => 'html',
            'value' => '<pre>' . htmlspecialchars($model->ip_info) . '</pre>',
        ),
        array(
            'name' => 'request_info',
            'type' => 'html',
            'value' => '<pre>' . htmlspecialchars($model->request_info) . '</pre>',
        ),
	),
)); ?>

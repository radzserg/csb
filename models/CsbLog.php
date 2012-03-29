<?php

/**
 * This is the model class for table "csb_log".
 *
 * The followings are the available columns in table 'csb_log':
 * @property integer $id
 * @property integer $ip
 * @property string $type
 * @property string $create_time
 * @property string $till_time
 * @property integer $user_id
 * @property string $request_info
 * @property string $ip_info
 * @property string $details
 */
class CsbLog extends CActiveRecord
{
    const TYPE_LOCK = 'lock';
    const TYPE_SEARCH_ENGINE = 'search_engine';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CsbLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'csb_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ip, create_time, till_time', 'required'),
			array('ip, user_id', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>13),
			array('request_info, ip_info, details', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ip, type, create_time, till_time, user_id, request_info, ip_info, details', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ip' => 'Ip',
			'type' => 'Type',
			'create_time' => 'Create Time',
			'till_time' => 'Till Time',
			'user_id' => 'User',
			'request_info' => 'Request Info',
			'ip_info' => 'Ip Info',
			'details' => 'Details',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('ip',$this->ip);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('till_time',$this->till_time,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('request_info',$this->request_info,true);
		$criteria->compare('ip_info',$this->ip_info,true);
		$criteria->compare('details',$this->details,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param $ip
     * @param $type
     * @param $tillTime
     * @param $details
     * @return mixed
     */
    public function log($ip, $type, $tillTime, $details)
    {
        $ipInfo = CsbHttpBl::getInstance()->getHttpBlInfo($ip);
        $model = new CsbLog();
        $model->setAttributes(array(
            'ip' => ip2long($ip),
            'type' => $type,
            'create_time' => date('Y-m-d H:i:s'),
            'till_time' => $tillTime,
            'account_id' => Yii::app()->user->getId(),
            'request_info' => CsbYaml::dump($this->_filterServerInfo()),
            'ip_info' => CsbYaml::dump($ipInfo),
            'details' => CsbYaml::dump($details),
        ));

        $model->save();

        return $model;
    }


    /**
     * FIlter server info
     * @return array
     */
    private function _filterServerInfo()
    {
        $server = $_SERVER;

        if (!empty($server['SHELL'])) {
            // do not log shell script env
            return array();
        }

        $unsetVariables = array('PATH', 'SERVER_SIGNATURE', 'SERVER_SOFTWARE', 'SERVER_NAME', 'SERVER_ADDR',
            'SERVER_PORT', 'DOCUMENT_ROOT', 'SERVER_ADMIN', 'SCRIPT_FILENAME', 'REMOTE_PORT');

        $filteredInfo = array();
        foreach ($server as $key => $value) {
            if (!in_array($key, $unsetVariables)) {
                $filteredInfo[$key] = $value;
            }
        }

        return $filteredInfo;
    }


}
<?php

/**
 * This is the model class for table "csb_ip_info".
 *
 * The followings are the available columns in table 'csb_ip_info':
 * @property integer $ip
 * @property string $ip_type
 * @property string $till_time
 */
class CsbIpInfo extends CActiveRecord
{

    const TYPE_BLOCKED  = 'blocked';
    const TYPE_SEARCH_ENGINE  = 'search_engine';

    const SEARCH_ENGINE_CACHE_TIME_DAYS = 30;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CsbIpInfo the static model class
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
		return 'csb_ip_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ip, ip_type, till_time', 'required'),
			array('ip', 'numerical', 'integerOnly'=>true),
			array('ip_type', 'length', 'max'=>13),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ip, ip_type, till_time', 'safe', 'on'=>'search'),
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
			'ip' => 'Ip',
			'ip_type' => 'Ip Type',
			'till_time' => 'Till Time',
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

		$criteria->compare('ip',$this->ip);
		$criteria->compare('ip_type',$this->ip_type,true);
		$criteria->compare('till_time',$this->till_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * Return IP info if it's exist
     * @param $ip
     * @return null
     */
    public function getIpType($ip)
    {
        $ipInfo = $this->find('ip = :ip AND till_time > :time', array(':ip' => ip2long($ip), ':time' => date('Y-m-d H:i:s')));
        return $ipInfo ? $ipInfo->ip_type : null;
    }

    public function isBlockIp($ip, $details=array())
    {
        $visitorType = CsbHttpBl::getInstance()->getVisitorType($ip);

        // for now only check is it search engine
        if ($visitorType === CsbHttpBl::VISITOR_SEARCH_ENGINE) {
            $tillTime = date('Y-m-d H:i:s', strtotime("+ " . self::SEARCH_ENGINE_CACHE_TIME_DAYS . " days"));
            $this->addIpInfo($ip, self::TYPE_SEARCH_ENGINE, $tillTime);
            CsbLog::model()->log($ip, CsbLog::TYPE_SEARCH_ENGINE, $tillTime, $details);
            return false;
        } else {
            if (empty($details['block time'])) {
                throw new App_Exception_System("Param 'blockTime' is not set");
            }
            $blockTillTime = date('Y-m-d H:i:s', strtotime("+ " . $details['block time'] . " second"));
            $this->addIpInfo($ip, self::TYPE_BLOCKED, $blockTillTime);
            CsbLog::model()->log($ip, CsbLog::TYPE_LOCK, $blockTillTime, $details);
            return true;
        }

    }

    /**
     * Add new ip info
     * @param $ip
     * @param $type
     * @param $tillTime
     * @return CsbIpInfo
     */
    public function addIpInfo($ip, $type, $tillTime)
    {
        $ipInfo = CsbIpInfo::model()->find('id = :ip', array(':ip' => ip2long($ip)));
        if (!$ipInfo) {
            $ipInfo = new CsbIpInfo();
            $ipInfo->ip = ip2long($ip);
        }
        $ipInfo->setAttributes(array(
            'ip_type' => $type,
            'till_time' => $tillTime,
        ));
        $ipInfo->save();
        return $ipInfo;
    }


}
<?php

/**
 * This is the model class for table "csb_request".
 *
 * The followings are the available columns in table 'csb_request':
 * @property integer $ip
 * @property string $time
 */
class CsbRequest extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CsbRequest the static model class
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
		return 'csb_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ip, time', 'required'),
			array('ip', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ip, time', 'safe', 'on'=>'search'),
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
			'time' => 'Time',
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
		$criteria->compare('time',$this->time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


    /**
     * Check request
     * @return bool
     * @throws CHttpException
     */
    public function checkRequest()
    {
        if (Yii::app() instanceof CConsoleApplication) {
            // do not log console command requests
            return false;
        }
        $ip = Yii::app()->request->getUserHostAddress();

        self::newRequest($ip);

        $ipType = CsbIpInfo::model()->getIpType($ip);
        if ($ipType == CsbIpInfo::TYPE_SEARCH_ENGINE) {
            return false;
        } elseif ($ipType == CsbIpInfo::TYPE_BLOCKED) {
            throw new CHttpException(503, 'Service Unavailable');
        }

        $shortIntervals = Yii::app()->params['csb']['shortIntervals'];
        foreach ($shortIntervals as $interval) {

            $currentRequestCount = self::getRequestCount($ip, $interval['time']);
            if ($currentRequestCount >= $interval['count']) {
                $details = array(
                    'current request count' => $currentRequestCount,
                    'interval seconds' => $interval['time'],
                    'request threshold value' => $interval['count'],
                    'block time' => $interval['blockTime'],
                );
                if (CsbIpInfo::model()->isBlockIp($ip, $details)) {
                    throw new CHttpException(503, 'Service Unavailable');
                }
            }
        }
    }

    /**
     * Check suspicious user's behavior at long intervals
     *
     */
    public function checkLongIntervals()
    {
        $longIntervals = Yii::app()->params['csb']['longIntervals'];

        foreach ($longIntervals as $interval) {
            $suspiciousRows = Yii::app()->db->createCommand()
                ->select('INET_NTOA(ip) as ip, COUNT(*) as cnt')
                ->from($this->tableName())
                ->where('time > :time', array(':time' => date('Y-m-d H:i:s', strtotime("-{$interval['time']} second"))))
                ->group('ip')
                ->having("cnt >= {$interval['count']}")
                ->queryAll();

            foreach ($suspiciousRows as $row) {
                $details = array(
                    'current request count' => $row['cnt'],
                    'interval seconds' => $interval['time'],
                    'request threshold value' => $interval['count'],
                    'block time' => $interval['blockTime'],
                );
                CsbIpInfo::model()->isBlockIp($row['ip'], $details);
            }
        }
    }

    /**
     * Log new request
     * @param $ip
     * @return int
     */
    public static function newRequest($ip)
    {
        $row = new CsbRequest();
        $row->time = date('Y-m-d H:i:s');
        $row->ip = ip2long($ip);
        $row->save();
    }

    /**
     * Get request count for ip for last time
     * @param $ip
     * @param $minusSecond
     * @return string
     */
    public static function getRequestCount($ip, $minusSecond)
    {
        return CsbRequest::model()->count('ip = :ip AND time > :time', array(
            ':ip' => ip2long($ip),
            ':time' => date('Y-m-d H:i:s', strtotime("- {$minusSecond} second"))
        ));
    }
}
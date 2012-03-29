<?php
/**
 * Test guide model
 * @author: radzserg
 * @date: 22.03.11
 */

class CsbRequestTest extends CDbTestCase
{
    

    public $fixtures=array(
		'requests' => 'CsbRequest',
        'ipInfos' => 'CsbIpInfo',
        'csbLogs' => 'CsbLog',
	);


    public function testRequestLog()
    {
        CsbRequest::model()->checkRequest();

        $this->assertEquals(1, CsbRequest::model()->count('ip = :ip', array(':ip' => ip2long(Yii::app()->request->getUserHostAddress()))));
    }

    /**
     * @expectedException CHttpException
     */
    public function testShortRequest()
    {
        $ip = Yii::app()->request->getUserHostAddress();

        // test 5 request per 5 last seconds
        for ($i = 1; $i <= 5; $i++) {
            $request = new CsbRequest;
            $request->ip = ip2long($ip);
            $request->time = date('Y-m-d H:i:s', strtotime("-" . ($i - 1) . " second"));
            $request->save();
        }
        CsbRequest::model()->checkRequest();

        // check logs
        $this->assertEquals(1, CsbIpInfo::model()->count('ip = :ip', array(':ip' => ip2long($ip))));
        $this->assertEquals(1, CsbLog::model()->count('ip = :ip', array(':ip' => ip2long($ip))));
    }

    public function testLongRequest()
    {
        $ip = '2.2.2.2';

        // test 100 requests per 600 last seconds
        for ($i = 1; $i <= 100; $i++) {
            $request = new CsbRequest;
            $request->ip = ip2long($ip);
            $request->time = date('Y-m-d H:i:s', strtotime("-" . ($i * 5) .  " second"));
            $request->save();
        }
        CsbRequest::model()->checkLongIntervals();

        // check logs
        $this->assertEquals(1, CsbIpInfo::model()->count('ip = :ip', array(':ip' => ip2long($ip))));
        $this->assertEquals(1, CsbLog::model()->count('ip = :ip', array(':ip' => ip2long($ip))));
    }


}

 

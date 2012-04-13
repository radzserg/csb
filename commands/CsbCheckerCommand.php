<?php

/**
 * Check suspicious behavior checker main command
 * Analyze logs in long interval, clear old logspcs
 *   
 * @package PCT
 * @author radzserg
 * @date 12/20/11
 */
class CsbCheckerCommand extends ConsoleCommand
{

    public function actionIndex()
    {
        $scheduleSeconds = (int)Yii::app()->params['csb']['checkScriptSchedule'];
        $fromCheckPointTime = date('Y-m-d H:i:s', strtotime("-{$scheduleSeconds} seconds"));
        
        $this->_verbose('Check long interval requests...');

        CsbRequest::model()->checkLongIntervals();

        // clear data
        CsbRequest::model()->deleteAll('time < :time', array(':time' => $fromCheckPointTime));
        CsbIpInfo::model()->deleteAll('till_time < :time', array(':time' => date('Y-m-d H:i:s')));

        $this->_sendNotification($fromCheckPointTime);
    }

    /**
     * Send notification to admin if need
     * @param $fromCheckPointTime
     * @return bool
     */
    private function _sendNotification($fromCheckPointTime)
    {        
        if (!Yii::app()->params['csb']['notifyAdmin'] || empty(Yii::app()->params['csb']['adminEmail'])) {
            return ;
        }

        // send log of blocked users for last period
        $lockedUsers = CsbLog::model()->findAll('create_time > :createTime AND type = :type',
            array(':createTime' => $fromCheckPointTime, ':type' => CsbLog::TYPE_LOCK)
        );
        if (!$lockedUsers) {
            $this->_verbose("There's no new locked IPs");
            return false;
        }
        
        $notification = 'Some users were blocked. Check Time ' . date('Y-m-d H:i:s') . '<br /><br />
            <table cellpadding="5px" cellspacing="5px">
            <tr>
                <th>IP</th>
                <th>Block Time</th>
                <th>Block Till Time</th>
                <th>User</th>
                <th>Details</th>
            </tr>';
        foreach ($lockedUsers as $row) {
            $notification .= '<tr>
                <td>' . long2ip($row->ip) . '</td>
                <td>' . $row->create_time . '</td>
                <td>' . $row->till_time . '</td>
                <td>' . $row->user_id . '</td>
                <td>' . $row->details . '</td>
            </tr>';
        }
        $notification .= '</table>';

        $mail = new Mail();
        $mail->setSubject('Some users were blocked')
            ->setBodyHtml($notification)
            ->addTo(Yii::app()->params['csb']['adminEmail'])
            ->send();

        $this->_verbose("Email was sent to admin", null, self::VERBOSE_INFO);
    }

}

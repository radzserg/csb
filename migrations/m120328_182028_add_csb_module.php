<?php

class m120328_182028_add_csb_module extends CDbMigration
{
	public function up()
	{
        $this->createTable('csb_request', array(
            'ip' => 'INT NOT NULL',
            'time' => 'DATETIME NOT NULL',
        ), 'ENGINE = Memory');
        $this->createIndex('csb_request_f_ip', 'csb_request', 'ip');
        $this->createIndex('csb_request_f_time', 'csb_request', 'time');

        $this->createTable('csb_ip_info', array(
            'ip' => 'INT NOT NULL',
            'ip_type' => "ENUM('blocked','search_engine') NOT NULL",
            'till_time' => 'DATETIME NOT NULL',
        ), 'ENGINE = Memory');
        $this->createIndex('csb_ip_info_f_ip', 'csb_ip_info', 'ip');
        $this->createIndex('csb_ip_info_f_till_time', 'csb_ip_info', 'till_time');

        $this->createTable('csb_log', array(
            'id' => 'pk',
            'ip' => 'INT NOT NULL',
            'type' => "enum('lock','search_engine')",
            'create_time' => 'DATETIME NOT NULL',
            'till_time' => 'DATETIME NOT NULL',
            'user_id' => 'INT',
            'request_info' => 'TEXT',
            'ip_info' => 'TEXT',
            'details' => 'TEXT',
        ));
	}

	public function down()
	{
		$this->dropTable('csb_request');
		$this->dropTable('csb_ip_info');
		$this->dropTable('csb_log');
	}

}
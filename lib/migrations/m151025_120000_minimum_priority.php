<?php
namespace canis\broadcaster\migrations;

class m151025_120000_minimum_priority extends \canis\db\Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->db->createCommand()->checkIntegrity(false)->execute();
        $this->addColumn('broadcast_subscription', 'minimum_priority', 'ENUM(\'low\', \'medium\', \'high\', \'critical\') NOT NULL DEFAULT \'low\' AFTER `broadcast_handler_id`');
        $this->db->createCommand()->checkIntegrity(true)->execute();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return false;
    }
}

<?php
namespace canis\broadcaster\migrations;

class m150925_120000_change_batch extends \canis\db\Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->db->createCommand()->checkIntegrity(false)->execute();
        $this->dropForeignKey('broadcastEventBatchHandler', 'broadcast_event_batch');
        $this->dropColumn('broadcast_event_batch', 'broadcast_handler_id');
        $this->addColumn('broadcast_event_batch', 'broadcast_subscription_id', 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL AFTER user_id');
        $this->addForeignKey('broadcastEventBatchSubscription', 'broadcast_event_batch', 'broadcast_subscription_id', 'broadcast_subscription', 'id', 'CASCADE', 'CASCADE');
        
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

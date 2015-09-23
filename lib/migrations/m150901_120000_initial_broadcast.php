<?php
namespace canis\broadcaster\migrations;

class m150901_120000_initial_broadcast extends \canis\db\Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->db->createCommand()->checkIntegrity(false)->execute();

        // drop existing notification (@todo drop these lines)
        $this->dropExistingTable('notification');
        $this->dropExistingTable('notification_endpoint');
        
        $this->dropExistingTable('broadcast_event_type');
        $this->createTable('broadcast_event_type', [
            'id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY',
            'system_id' => 'string(255) NOT NULL',
            'name' => 'string(255) NOT NULL'
        ]);
        $this->addForeignKey('broadcastEventTypeRegistry', 'broadcast_event_type', 'id', 'registry', 'id', 'CASCADE', 'CASCADE');

        $this->dropExistingTable('broadcast_handler');
        $this->createTable('broadcast_handler', [
            'id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY',
            'system_id' => 'string(255) NOT NULL',
            'name' => 'string(255) NOT NULL'
        ]);
        $this->addForeignKey('broadcastHandlerRegistry', 'broadcast_handler', 'id', 'registry', 'id', 'CASCADE', 'CASCADE');

        $this->dropExistingTable('broadcast_event');
        $this->createTable('broadcast_event', [
            'id' => 'bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'broadcast_event_type_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            'priority' => 'ENUM(\'low\', \'medium\', \'high\', \'critical\') DEFAULT \'medium\'',
            'object_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL',
            'payload' => 'longblob',
            'handled' => 'bool NOT NULL DEFAULT 0',
            'created' => 'datetime DEFAULT NULL',
        ]);
        $this->addForeignKey('broadcastEventEventType', 'broadcast_event', 'broadcast_event_type_id', 'broadcast_event_type', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventObject', 'broadcast_event', 'object_id', 'registry', 'id', 'SET NULL', 'CASCADE');


        $this->dropExistingTable('broadcast_event_deferred');
        $this->createTable('broadcast_event_deferred', [
            'id' => 'bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'broadcast_subscription_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            'broadcast_event_id' => 'bigint unsigned NOT NULL',
            'broadcast_event_batch_id' => 'bigint unsigned DEFAULT NULL',

            'result' => 'longblob',
            'handled' => 'bool NOT NULL DEFAULT 0',
            'scheduled' => 'datetime DEFAULT NULL',
            'created' => 'datetime DEFAULT NULL'
        ]);
        $this->addForeignKey('broadcastEventDeferredSubscription', 'broadcast_event_deferred', 'broadcast_subscription_id', 'broadcast_subscription', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventDeferredEvent', 'broadcast_event_deferred', 'broadcast_event_id', 'broadcast_event', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventDeferredBatch', 'broadcast_event_deferred', 'broadcast_event_batch_id', 'broadcast_event_batch', 'id', 'CASCADE', 'CASCADE');


        $this->dropExistingTable('broadcast_event_batch');
        $this->createTable('broadcast_event_batch', [
            'id' => 'bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'user_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            'broadcast_handler_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            'result' => 'longblob',
            'handled' => 'bool NOT NULL DEFAULT 0',
            'scheduled' => 'datetime DEFAULT NULL',
            'created' => 'datetime DEFAULT NULL'
        ]);
        $this->addForeignKey('broadcastEventBatchUser', 'broadcast_event_batch', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventBatchHandler', 'broadcast_event_batch', 'broadcast_handler_id', 'broadcast_handler', 'id', 'CASCADE', 'CASCADE');
        
        $this->dropExistingTable('broadcast_subscription');
        $this->createTable('broadcast_subscription', [
            'id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY',
            'user_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            'object_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL',
            'broadcast_handler_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            'all_events' => 'bool NOT NULL DEFAULT 0',
            'name' => 'string(255) DEFAULT NULL',
            'batch_type' => 'ENUM(\'hourly\', \'daily\', \'weekly\', \'monthly\') DEFAULT NULL',
            'config' => 'longblob DEFAULT NULL',
            'last_triggered' => 'datetime DEFAULT NULL',
            'created' => 'datetime DEFAULT NULL'
        ]);

        $this->addForeignKey('broadcastEventSubscriptionRegistry', 'broadcast_subscription', 'id', 'registry', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventSubscriptionObject', 'broadcast_subscription', 'object_id', 'registry', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventSubscriptionUser', 'broadcast_subscription', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventSubscriptionHandler', 'broadcast_subscription', 'broadcast_handler_id', 'broadcast_handler', 'id', 'CASCADE', 'CASCADE');


        $this->dropExistingTable('broadcast_subscription_event_type');
        $this->createTable('broadcast_subscription_event_type', [
            'id' => 'bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'broadcast_subscription_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
            'broadcast_event_type_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL',
        ]);

        $this->addForeignKey('broadcastEventSubscriptionType', 'broadcast_subscription_event_type', 'broadcast_event_type_id', 'broadcast_event_type', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('broadcastEventSubscriptionSubscription', 'broadcast_subscription_event_type', 'broadcast_subscription_id', 'broadcast_subscription', 'id', 'CASCADE', 'CASCADE');
        
        $this->db->createCommand()->checkIntegrity(true)->execute();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->db->createCommand()->checkIntegrity(false)->execute();

        $this->dropExistingTable('notification');
        $this->dropExistingTable('notification_endpoint');


        $this->dropExistingTable('broadcast_event_type');
        $this->dropExistingTable('broadcast_handler');
        $this->dropExistingTable('broadcast_event');
        $this->dropExistingTable('broadcast_event_deferred');
        $this->dropExistingTable('broadcast_event_batch');
        $this->dropExistingTable('broadcast_subscription');
        $this->dropExistingTable('broadcast_subscription_event_type');

        $this->db->createCommand()->checkIntegrity(true)->execute();

        return true;
    }
}

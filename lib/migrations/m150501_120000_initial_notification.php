<?php
namespace canis\notification\migrations;

class m150501_120000_initial_notification extends \canis\db\Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->db->createCommand()->checkIntegrity(false)->execute();

        // notification
        $this->dropExistingTable('notification');
        $this->createTable('notification', [
            'id' => 'bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'hash' => 'string(40) DEFAULT NULL',
            'notification' => 'longblob NOT NULL',
            'created' => 'datetime DEFAULT NULL'
        ]);
        $this->createIndex('notificationHash', 'notification', 'hash', false);

        // notification_endpoint
        $this->dropExistingTable('notification_endpoint');
        $this->createTable('notification_endpoint', [
            'id' => 'bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'user_id' => 'char(36) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL',
            'session_id' => 'string(40) DEFAULT NULL',
            'notification_id' => 'bigint unsigned NOT NULL',

            'endpoint' => 'longblob NOT NULL',
            'status' => 'enum(\'pending\', \'handling\', \'handled\') DEFAULT \'pending\'',
            'background' => 'int(3) unsigned NOT NULL DEFAULT 1',
            'error_message' => 'text DEFAULT \'\'',

            'scheduled' => 'datetime DEFAULT NULL',
            'attempted' => 'datetime DEFAULT NULL',
            'expires' => 'datetime DEFAULT NULL',
            'created' => 'datetime DEFAULT NULL'
        ]);
        $this->addForeignKey('notificationEndpointNotification', 'notification_endpoint', 'notification_id', 'notification', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('notificationEndpointUser', 'notification_endpoint', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
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

        $this->db->createCommand()->checkIntegrity(true)->execute();

        return true;
    }
}

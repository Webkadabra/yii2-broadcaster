<?php
/**
 * @link http://canis.io/
 *
 * @copyright Copyright (c) 2015 Canis
 * @license http://canis.io/license/
 */

namespace canis\broadcaster;

use canis\daemon\BaseTickDaemon;

/**
 * Daemon [[@doctodo class_description:canis\base\Daemon]].
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Daemon extends BaseTickDaemon
{
    public function getDescriptor()
    {
        return 'Broadcaster Daemon';
    }
}

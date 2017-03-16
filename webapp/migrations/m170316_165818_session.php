<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m170316_165818_session extends Migration
{
    public function up()
    {
        $this->createTable('session', [
            'id' => 'CHAR(40) NOT NULL PRIMARY KEY',
            'expire' => 'INTEGER',
            'data' => 'BYTEA',
        ]);
    }

    public function down()
    {
        $this->dropTable('session');
    }
}

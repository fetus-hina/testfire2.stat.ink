<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m170320_091331_controller_mode extends Migration
{
    public function up()
    {
        $this->createTable('controller_mode', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(64)->notNull()->unique(),
            'name'  => $this->string(64)->notNull(),
        ]);
        $this->batchInsert('controller_mode', ['key', 'name'], [
            [ 'procon',           'Pro Controller' ],
            [ 'joycon_with_grip', 'Joy-Con with Grip' ],
            [ 'joycon_wo_grip',   'Joy-Con without Grip' ],
            [ 'handheld',         'Handheld Mode' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('controller_mode');
    }
}

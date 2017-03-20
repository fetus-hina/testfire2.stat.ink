<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m170320_090508_display_mode extends Migration
{
    public function up()
    {
        $this->createTable('display_mode', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(64)->notNull()->unique(),
            'name'  => $this->string(64)->notNull(),
        ]);
        $this->batchInsert('display_mode', ['key', 'name'], [
            [ 'tv',       'TV Mode' ],
            [ 'tabletop', 'Tabletop Mode' ],
            [ 'handheld', 'Handheld Mode' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('display_mode');
    }
}

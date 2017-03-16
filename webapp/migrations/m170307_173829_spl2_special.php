<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m170307_173829_spl2_special extends Migration
{
    public function safeUp()
    {
        $this->delete('death_reason');
        $this->delete('weapon_attack');
        $this->delete('weapon');
        $this->delete('special');

        $this->batchInsert('special', ['key', 'name'], [
            [ 'jetpack', 'Inkjet' ],
            [ 'chakuchi', 'Splashdown' ],
            [ 'presser', 'Sting Ray' ],
            [ 'missile', 'Tenta Missiles' ],
        ]);
    }

    public function down()
    {
        echo "m170307_173829_spl2_special cannot be reverted.\n";
        return false;
    }
}

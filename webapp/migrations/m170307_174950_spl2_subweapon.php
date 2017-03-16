<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m170307_174950_spl2_subweapon extends Migration
{
    public function safeUp()
    {
        $this->delete('subweapon', ['not', ['key' => [
            'splashbomb',
            'kyubanbomb',
            'quickbomb',
        ]]]);
        $this->insert('subweapon', [
            'key' => 'curlingbomb',
            'name' => 'Curling Bomb',
        ]);
    }

    public function down()
    {
        echo "m170307_174950_spl2_subweapon cannot be reverted.\n";
        return false;
    }
}

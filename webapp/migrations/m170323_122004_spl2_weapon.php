<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\models\Special;
use app\models\Subweapon;
use app\models\WeaponType;
use yii\db\Expression;
use yii\db\Migration;

// https://twitter.com/SplatoonJP/status/844843062224314368
class m170323_122004_spl2_weapon extends Migration
{
    public function safeUp()
    {
        $this->update(
            'weapon',
            ['subweapon_id' => Subweapon::findOne(['key' => 'curlingbomb'])->id],
            ['weapon.key' => 'manueuver']
        );
        $this->update(
            'weapon',
            ['subweapon_id' => Subweapon::findOne(['key' => 'kyubanbomb'])->id],
            ['weapon.key' => 'splatroller']
        );

    }

    public function safeDown()
    {
        $this->update(
            'weapon',
            ['subweapon_id' => Subweapon::findOne(['key' => 'kyubanbomb'])->id],
            ['weapon.key' => 'manueuver']
        );
        $this->update(
            'weapon',
            ['subweapon_id' => Subweapon::findOne(['key' => 'curlingbomb'])->id],
            ['weapon.key' => 'splatroller']
        );
    }
}

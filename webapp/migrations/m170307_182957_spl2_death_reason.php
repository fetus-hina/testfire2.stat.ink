<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\models\DeathReasonType;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use yii\db\Migration;

class m170307_182957_spl2_death_reason extends Migration
{
    public function safeUp()
    {
        $typeOOB = DeathReasonType::findOne(['key' => 'suicide'])->id;
        $this->batchInsert('death_reason', ['type_id', 'key', 'name'], [
            [null, 'unknown', 'Unknown'],
            [$typeOOB, 'oob', 'Out of Bounds'],
            [$typeOOB, 'drown', 'Drowning'],
            [$typeOOB, 'fall', 'Fall'],
        ]);

        $typeMain = DeathReasonType::findOne(['key' => 'main'])->id;
        foreach (Weapon::find()->orderBy('[[id]]')->asArray()->all() as $weapon) {
            $this->insert('death_reason', [
                'type_id'   => $typeMain,
                'key'       => $weapon['key'],
                'name'      => $weapon['name'],
                'weapon_id' => $weapon['id'],
            ]);
        }
        $typeSub = DeathReasonType::findOne(['key' => 'sub'])->id;
        foreach (Subweapon::find()->orderBy('[[id]]')->asArray()->all() as $weapon) {
            $this->insert('death_reason', [
                'type_id'   => $typeSub,
                'key'       => $weapon['key'],
                'name'      => $weapon['name'],
            ]);
        }

        $typeSpecial = DeathReasonType::findOne(['key' => 'special'])->id;
        foreach (Special::find()->orderBy('[[id]]')->asArray()->all() as $weapon) {
            $this->insert('death_reason', [
                'type_id'   => $typeSpecial,
                'key'       => $weapon['key'],
                'name'      => $weapon['name'],
            ]);
        }
    }

    public function down()
    {
        echo "m170307_182957_spl2_death_reason cannot be reverted.\n";
        return false;
    }
}

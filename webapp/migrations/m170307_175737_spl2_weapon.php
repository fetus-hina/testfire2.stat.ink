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

class m170307_175737_spl2_weapon extends Migration
{
    public function safeUp()
    {
        $data = [
            [
                'type_id'        => WeaponType::findOne(['key' => 'shooter'])->id,
                'key'            => 'sshooter',
                'name'           => 'Splattershot',
                'subweapon_id'   => Subweapon::findOne(['key' => 'quickbomb'])->id,
                'special_id'     => Special::findOne(['key' => 'missile'])->id,
                'canonical_id'   => new Expression("currval('weapon_id_seq'::regclass)"),
                'main_group_id'  => new Expression("currval('weapon_id_seq'::regclass)"),
            ],
            [
                'type_id'        => WeaponType::findOne(['key' => 'shooter'])->id,
                'key'            => 'manueuver',
                'name'           => 'Splat Dualies',
                'subweapon_id'   => Subweapon::findOne(['key' => 'kyubanbomb'])->id,
                'special_id'     => Special::findOne(['key' => 'jetpack'])->id,
                'canonical_id'   => new Expression("currval('weapon_id_seq'::regclass)"),
                'main_group_id'  => new Expression("currval('weapon_id_seq'::regclass)"),
            ],
            [
                'type_id'        => WeaponType::findOne(['key' => 'roller'])->id,
                'key'            => 'splatroller',
                'name'           => 'Splat Roller',
                'subweapon_id'   => Subweapon::findOne(['key' => 'curlingbomb'])->id,
                'special_id'     => Special::findOne(['key' => 'chakuchi'])->id,
                'canonical_id'   => new Expression("currval('weapon_id_seq'::regclass)"),
                'main_group_id'  => new Expression("currval('weapon_id_seq'::regclass)"),
            ],
            [
                'type_id'        => WeaponType::findOne(['key' => 'charger'])->id,
                'key'            => 'splatcharger',
                'name'           => 'Splat Charger',
                'subweapon_id'   => Subweapon::findOne(['key' => 'splashbomb'])->id,
                'special_id'     => Special::findOne(['key' => 'presser'])->id,
                'canonical_id'   => new Expression("currval('weapon_id_seq'::regclass)"),
                'main_group_id'  => new Expression("currval('weapon_id_seq'::regclass)"),
            ],
        ];
        foreach ($data as $item) {
            $this->insert('weapon', $item);
        }
    }

    public function down()
    {
        echo "m170307_175737_spl2_weapon cannot be reverted.\n";
        return false;
    }
}

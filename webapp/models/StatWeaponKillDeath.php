<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "stat_weapon_kill_death".
 *
 * @property integer $weapon_id
 * @property integer $rule_id
 * @property integer $kill
 * @property integer $death
 * @property integer $battle
 * @property integer $win
 *
 * @property Rule $rule
 * @property Weapon $weapon
 */
class StatWeaponKillDeath extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon_kill_death';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weapon_id', 'rule_id', 'kill', 'death', 'battle', 'win'], 'required'],
            [['weapon_id', 'rule_id', 'kill', 'death', 'battle', 'win'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'weapon_id' => 'Weapon ID',
            'rule_id' => 'Rule ID',
            'kill' => 'Kill',
            'death' => 'Death',
            'battle' => 'Battle',
            'win' => 'Win',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::className(), ['id' => 'weapon_id']);
    }
}

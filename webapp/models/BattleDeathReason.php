<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "battle_death_reason".
 *
 * @property integer $battle_id
 * @property integer $reason_id
 * @property integer $count
 *
 * @property Battle $battle
 * @property DeathReason $reason
 */
class BattleDeathReason extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return parent::find()->with('reason');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_death_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'reason_id', 'count'], 'required'],
            [['battle_id', 'reason_id', 'count'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'battle_id' => 'Battle ID',
            'reason_id' => 'Reason ID',
            'count' => 'Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle::className(), ['id' => 'battle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReason()
    {
        return $this->hasOne(DeathReason::className(), ['id' => 'reason_id']);
    }

    public function toJsonArray()
    {
        return [
            'reason' => $this->reason->toJsonArray(),
            'count' => (int)$this->count,
        ];
    }
}

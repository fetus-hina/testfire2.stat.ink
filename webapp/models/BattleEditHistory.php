<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle_edit_history".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property string $diff
 * @property string $at
 *
 * @property Battle $battle
 */
class BattleEditHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_edit_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'diff'], 'required'],
            [['battle_id'], 'integer'],
            [['diff'], 'string'],
            [['at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'battle_id' => 'Battle ID',
            'diff' => 'Diff',
            'at' => 'At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasMany(Battle::className(), ['id' => 'battle_id']);
    }
}

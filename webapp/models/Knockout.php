<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "knockout".
 *
 * @property integer $map_id
 * @property integer $rule_id
 * @property integer $battles
 * @property integer $knockouts
 *
 * @property Map $map
 * @property Rule $rule
 */
class Knockout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'knockout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['map_id', 'rule_id', 'battles', 'knockouts'], 'required'],
            [['map_id', 'rule_id', 'battles', 'knockouts'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'map_id' => 'Map ID',
            'rule_id' => 'Rule ID',
            'battles' => 'Battles',
            'knockouts' => 'Knockouts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::className(), ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }
}

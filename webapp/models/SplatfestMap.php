<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "splatfest_map".
 *
 * @property integer $id
 * @property integer $splatfest_id
 * @property integer $map_id
 *
 * @property Map $map
 * @property Splatfest $splatfest
 */
class SplatfestMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatfest_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['splatfest_id', 'map_id'], 'required'],
            [['splatfest_id', 'map_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'splatfest_id' => 'Splatfest ID',
            'map_id' => 'Map ID',
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
    public function getSplatfest()
    {
        return $this->hasOne(Splatfest::className(), ['id' => 'splatfest_id']);
    }
}

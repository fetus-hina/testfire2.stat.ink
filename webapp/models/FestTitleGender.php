<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "fest_title_gender".
 *
 * @property integer $title_id
 * @property integer $gender_id
 * @property string $name
 *
 * @property FestTitle $title
 * @property Gender $gender
 */
class FestTitleGender extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fest_title_gender';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_id', 'gender_id', 'name'], 'required'],
            [['title_id', 'gender_id'], 'integer'],
            [['name'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title_id' => 'Title ID',
            'gender_id' => 'Gender ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitle()
    {
        return $this->hasOne(FestTitle::className(), ['id' => 'title_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGender()
    {
        return $this->hasOne(Gender::className(), ['id' => 'gender_id']);
    }
}

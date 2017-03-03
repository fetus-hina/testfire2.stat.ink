<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "winikalog_version".
 *
 * @property integer $id
 * @property integer $revision_id
 * @property string $build_at
 *
 * @property IkalogVersion $revision
 */
class WinikalogVersion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'winikalog_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['revision_id'], 'integer'],
            [['build_at'], 'required'],
            [['build_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'revision_id' => 'Revision ID',
            'build_at' => 'Build At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevision()
    {
        return $this->hasOne(IkalogVersion::className(), ['id' => 'revision_id']);
    }
}

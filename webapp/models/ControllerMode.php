<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "controller_mode".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property DisplayController[] $displayControllers
 * @property DisplayMode[] $displays
 */
class ControllerMode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'controller_mode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string', 'max' => 64],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDisplayControllers()
    {
        return $this->hasMany(DisplayController::class, ['controller_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDisplays()
    {
        return $this->hasMany(DisplayMode::class, ['id' => 'display_id'])
            ->viaTable('display_controller', ['controller_id' => 'id']);
    }

    public function toJsonArray() : array
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-switch', $this->name),
        ];
    }
}

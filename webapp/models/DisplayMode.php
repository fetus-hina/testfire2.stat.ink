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
 * This is the model class for table "display_mode".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property DisplayController[] $displayControllers
 * @property ControllerMode[] $controllers
 */
class DisplayMode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'display_mode';
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
        return $this->hasMany(DisplayController::class, ['display_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getControllers()
    {
        return $this->hasMany(ControllerMode::class, ['id' => 'controller_id'])
            ->viaTable('display_controller', ['display_id' => 'id']);
    }

    public function toJsonArray() : array
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-switch', $this->name),
        ];
    }
}

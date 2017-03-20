<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "display_controller".
 *
 * @property integer $display_id
 * @property integer $controller_id
 *
 * @property ControllerMode $controller
 * @property DisplayMode $display
 */
class DisplayController extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'display_controller';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['display_id', 'controller_id'], 'required'],
            [['display_id', 'controller_id'], 'integer'],
            [['controller_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ControllerMode::class,
                'targetAttribute' => ['controller_id' => 'id'],
            ],
            [['display_id'], 'exist', 'skipOnError' => true,
                'targetClass' => DisplayMode::class,
                'targetAttribute' => ['display_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'display_id' => 'Display ID',
            'controller_id' => 'Controller ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getController()
    {
        return $this->hasOne(ControllerMode::className(), ['id' => 'controller_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDisplay()
    {
        return $this->hasOne(DisplayMode::className(), ['id' => 'display_id']);
    }
}

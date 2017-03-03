<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "stat_agent_user".
 *
 * @property integer $id
 * @property string $agent
 * @property string $date
 * @property integer $battle_count
 * @property integer $user_count
 */
class StatAgentUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_agent_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agent', 'date', 'battle_count', 'user_count'], 'required'],
            [['date'], 'safe'],
            [['battle_count', 'user_count'], 'integer'],
            [['agent'], 'string', 'max' => 64],
            [['agent', 'date'], 'unique',
                'targetAttribute' => ['agent', 'date'],
                'message' => 'The combination of Agent and Date has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agent' => 'Agent',
            'date' => 'Date',
            'battle_count' => 'Battle Count',
            'user_count' => 'User Count',
        ];
    }
}

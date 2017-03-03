<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\db\Now;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "turfwar_win_bonus".
 *
 * @property integer $id
 * @property integer $bonus
 * @property string $start_at
 */
class TurfwarWinBonus extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return new class(get_called_class()) extends ActiveQuery {
            public function current() : self
            {
                return $this->at(new Now());
            }

            public function at($time) : self
            {
                return $this
                    ->orderBy('[[start_at]] DESC')
                    ->limit(1)
                    ->andWhere(['<=', '[[start_at]]',
                            ($time instanceof Expression)
                                ? $time
                                : (is_numeric($time)
                                    ? DateTimeFormatter::unixTimeToString($time, new DateTimeZone('Etc/UTC'))
                                    : (string)$time
                                )
                        ]);
            }
        };
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'turfwar_win_bonus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bonus', 'start_at'], 'required'],
            [['bonus'], 'integer'],
            [['start_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bonus' => 'Bonus',
            'start_at' => 'Start At',
        ];
    }
}

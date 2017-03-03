<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class BattleRerecognizePlayerForm extends Model
{
    public $is_me;
    public $team;
    public $rank_in_team;
    public $level;
    public $rank;
    public $weapon;
    public $kill;
    public $death;
    public $point;

    public function rules()
    {
        return [
            [['is_me', 'team', 'rank_in_team'], 'required'],
            [['is_me'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['team'], 'in', 'range' => ['my', 'his']],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['level'], 'integer', 'min' => 1, 'max' => 50],
            [['rank'], 'in', 'range' => ['c-', 'c', 'c+', 'b-', 'b', 'b+', 'a-', 'a', 'a+', 's', 's+']],
            [['weapon'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'key',
            ],
            [['kill', 'death'], 'integer', 'min' => 0, 'max' => 99],
            [['point'], 'integer', 'min' => 0],
        ];
    }

    public function getWeaponModel()
    {
        return ($this->weapon)
            ? Weapon::findOne(['key' => $this->weapon])
            : null;
    }
}

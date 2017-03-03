<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\helpers\Url;
use yii\web\ViewAction as BaseAction;
use yii\web\NotFoundHttpException;
use app\models\Map;
use app\models\Rule;
use app\models\StatWeaponKDWinRate;
use app\models\StatWeaponKillDeath;
use app\models\Weapon;
use app\models\WeaponType;

class WeaponAction extends BaseAction
{
    public $weapon;
    public $rule;

    public function init()
    {
        parent::init();
        $key = Yii::$app->request->get('weapon');
        if (is_scalar($key)) {
            $this->weapon = Weapon::findOne(['key' => $key]);
        }
        if (!$this->weapon) {
            throw new NotFoundHttpException(
                Yii::t('yii', 'Page not found.')
            );
        }

        $key = Yii::$app->request->get('rule');
        if ($key === '' || $key === null) {
            $this->controller->redirect(
                Url::to(['entire/weapon', 'weapon' => $this->weapon->key, 'rule' => 'nawabari'], 301)
            );
            return;
        }
        if (is_scalar($key)) {
            $this->rule = Rule::findOne(['key' => $key]);
        }
        if (!$this->rule) {
            throw new NotFoundHttpException(
                Yii::t('yii', 'Page not found.')
            );
        }
    }

    public function run()
    {
        return $this->controller->render('weapon.tpl', [
            'weapons' => $this->weapons,
            'weapon' => $this->weapon,
            'rules' => $this->rules,
            'rule' => $this->rule,
            'maps' => $this->maps,
            'killDeath' => $this->killDeath,
            'mapWP' => $this->mapWinPercentage,
            'useCount' => $this->useCount,
        ]);
    }

    public function getRules()
    {
        $list = Rule::find()->with('mode')->all();
        array_walk($list, function ($a) {
            $a->name = Yii::t('app-rule', $a->name);
        });
        usort($list, function ($a, $b) {
            if ($a->mode_id !== $b->mode_id) {
                return $a->mode->key === 'regular' ? -1 : 1;
            }
            return strnatcasecmp($a->name, $b->name);
        });
        return $list;
    }

    public function getKillDeath()
    {
        $tmp = StatWeaponKillDeath::find()
            ->andWhere([
                'weapon_id' => $this->weapon->id,
                'rule_id' => $this->rule->id,
            ])
            ->orderBy('kill, death')
            ->asArray()
            ->all();
        return array_map(function ($a) {
            return [
                'kill'   => (int)$a['kill'],
                'death'  => (int)$a['death'],
                'battle' => (int)$a['battle'],
                'win'    => (int)$a['win'],
            ];
        }, $tmp);
    }

    public function getWeapons()
    {
        $ret = [];
        foreach (WeaponType::find()->orderBy('id')->all() as $weaponType) {
            $ret[Yii::t('app-weapon', $weaponType->name)] = (function (array $weapons) {
                $ret = [];
                foreach ($weapons as $weapon) {
                    $ret[$weapon['key']] = Yii::t('app-weapon', $weapon['name']);
                }
                uasort($ret, 'strnatcasecmp');
                return $ret;
            })($weaponType->getWeapons()->asArray()->all());
        }
        return $ret;
    }

    public function getMaps()
    {
        $ret = array_map(
            function ($row) {
                return [
                    'key' => $row['key'],
                    'name' => Yii::t('app-map', $row['name']),
                ];
            },
            Map::find()->asArray()->all()
        );
        usort($ret, function ($a, $b) {
            return strnatcasecmp($a['name'], $b['name']);
        });
        return $ret;
    }

    public function getMapWinPercentage()
    {
        $table = StatWeaponKDWinRate::tableName();
        $map = Map::tableName();
        $query = (new \yii\db\Query())
            ->select([
                'map'       => "MAX({{{$map}}}.[[key]])",
                'battle'    => "SUM({{{$table}}}.[[battle_count]])",
                'win'       => "SUM({{{$table}}}.[[win_count]])",
            ])
            ->from($table)
            ->innerJoin($map, "{{{$table}}}.[[map_id]] = {{{$map}}}.[[id]]")
            ->andWhere([
                "{{{$table}}}.[[rule_id]]" => $this->rule->id,
                "{{{$table}}}.[[weapon_id]]" => $this->weapon->id,
            ])
            ->groupBy("{{{$table}}}.[[map_id]]");
        return (function ($rows) {
            $tmp = [];
            foreach ($rows as $row) {
                $tmp[$row['map']] = [
                    'battle' => (int)$row['battle'],
                    'win' => (int)$row['win'],
                ];
            }
            return $tmp;
        })($query->createCommand()->queryAll());
    }

    public function getUseCount()
    {
        $weaponId = (int)$this->weapon->id;
        $query = (new \yii\db\Query())
            ->select([
                'isoyear'       => 'isoyear',
                'isoweek'       => 'isoweek',
                'all_battles'   => 'SUM([[battles]])',
                'battles'       => "SUM(CASE WHEN [[weapon_id]] = {$weaponId} THEN [[battles]] ELSE 0 END)",
                'wins'          => "SUM(CASE WHEN [[weapon_id]] = {$weaponId} THEN [[wins]] ELSE 0 END)",
            ])
            ->from('stat_weapon_use_count_per_week')
            ->where(['and',
                ['rule_id' => $this->rule->id],
                ['or',
                    ['>', 'isoyear', 2015],
                    ['and',
                        ['=', 'isoyear', 2015],
                        ['>=', 'isoweek', 46],
                    ]
                ],
            ])
            ->groupBy('isoyear, isoweek')
            ->having(['>', 'SUM([[battles]])', 0])
            ->orderBy('isoyear, isoweek');
        return array_map(
            function (array $row) : array {
                return [
                    'date'      => date('Y-m-d', strtotime(sprintf('%04d-W%02d', $row['isoyear'], $row['isoweek']))),
                    'battles'   => (int)$row['all_battles'],
                    'use_pct'   => $row['battles'] / $row['all_battles'] * 100,
                    'win_pct'   => $row['battles'] > 0 ? $row['wins'] / $row['battles'] * 100 : 0,
                ];
            },
            $query->createCommand()->queryAll()
        );
    }
}

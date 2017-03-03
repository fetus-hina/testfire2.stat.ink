<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use DateInterval;
use DateTime;
use DateTimeZone;
use Yii;
use app\models\GameMode;
use app\models\Rule;
use app\models\Special;
use app\models\StatWeapon;
use app\models\StatWeaponBattleCount;
use app\models\Subweapon;
use app\models\Weapon;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

class WeaponsAction extends BaseAction
{
    public function run()
    {
        return $this->controller->render('weapons.tpl', [
            'uses' => $this->weaponUses,
            'entire' => $this->entireWeapons,
            'users' => $this->userWeapons,
        ]);
    }

    public function getWeaponUses()
    {
        $threshold = (function () {
            $date = (new DateTime('@' . $_SERVER['REQUEST_TIME']))
                ->setTimezone(new DateTimeZone('Asia/Tokyo'))
                ->sub(new DateInterval('P1W'));
            return [
                (int)$date->format('o'), // isoyear
                (int)$date->format('W'), // isoweek
            ];
        })();

        // 最近よく使われているブキを抽出
        $qTrend = (new Query())
            ->select([
                'weapon_id',
                'battles' => 'SUM(battles)',
            ])
            ->from('stat_weapon_use_count_per_week')
            ->where(['or',
                ['>', 'isoyear', $threshold[0]],
                ['and',
                    ['=', 'isoyear', $threshold[0]],
                    ['>=', 'isoweek', $threshold[1]],
                ]
            ])
            ->groupBy('weapon_id')
            ->orderBy('SUM(battles) DESC')
            ->limit(15);
        if (!$trends = $qTrend->all()) {
            return [];
        }
        $query = (new Query())
            ->select(array_merge(
                ['isoyear', 'isoweek', 'battles' => 'SUM([[battles]])'],
                (function () use ($trends) {
                    $ret = [];
                    foreach ($trends as $trend) {
                        $key = sprintf('w%d', $trend['weapon_id']);
                        $ret[$key] = sprintf(
                            'SUM(CASE WHEN [[weapon_id]] = %d THEN [[battles]] ELSE 0 END)',
                            $trend['weapon_id']
                        );
                    }
                    return $ret;
                })()
            ))
            ->from('stat_weapon_use_count_per_week')
            ->where(['or',
                ['>', 'isoyear', 2015],
                ['and',
                    ['=', 'isoyear', 2015],
                    ['>=', 'isoweek', 46],
                ]
            ])
            ->groupBy('isoyear, isoweek')
            ->orderBy('isoyear, isoweek');
        if (!$baselist = $query->all()) {
            return [];
        }

        $weapons = Weapon::findAll([
            'id' => array_map(function ($_) {
                return $_['weapon_id'];
            }, $trends),
        ]);

        return array_map(function (array $_) use ($trends, $weapons) : array {
            $w = [];
            $total = 0;
            foreach ($trends as $trend) {
                $key = 'w' . $trend['weapon_id'];
                $count = (int)$_[$key];
                $weapon = (function ($id) use ($weapons) {
                    foreach ($weapons as $weapon) {
                        if ($weapon->id == $id) {
                            return $weapon;
                        }
                    }
                    return null;
                })($trend['weapon_id']);
                
                $w[] = [
                    'name' => Yii::t('app-weapon', $weapon->name ?? '?'),
                    'battles' => $count,
                    'pct' => $_['battles'] > 0 ? ($count * 100 / $_['battles']) : null,
                ];
                $total += $count;
            }
            return [
                'date' => date('Y-m-d', strtotime(sprintf('%04d-W%02d', $_['isoyear'], $_['isoweek']))),
                'battles' => (int)$_['battles'],
                'weapons' => $w,
                'others' => $_['battles'] - $total,
                'others_pct' => $_['battles'] > 0 ? (($_['battles'] - $total) * 100 / $_['battles']) : null,
            ];
        }, $baselist);
    }

    public function getEntireWeapons()
    {
        $rules = [];
        foreach (GameMode::find()->orderBy('id ASC')->all() as $mode) {
            $tmp = [];
            foreach ($mode->rules as $rule) {
                $weapons = $this->getEntireWeaponsByRule($rule);
                $tmp[] = (object)[
                    'key' => $rule->key,
                    'name' => Yii::t('app-rule', $rule->name),
                    'data' => $weapons,
                    'sub' => $this->convertWeapons2Sub($weapons),
                    'special' => $this->convertWeapons2Special($weapons),
                ];
            }
            usort($tmp, function ($a, $b) {
                return strnatcasecmp($a->name, $b->name);
            });
            while (!empty($tmp)) {
                $rules[] = array_shift($tmp);
            }
        }
        return $rules;
    }

    private function getEntireWeaponsByRule(Rule $rule)
    {
        $query = StatWeapon::find()
            ->with([
                'weapon',
                'weapon.subweapon',
                'weapon.special',
            ])
            ->andWhere(['{{stat_weapon}}.[[rule_id]]' => $rule->id]);

        $totalPlayers = 0;
        $list = array_map(
            function ($model) use (&$totalPlayers) {
                $totalPlayers += $model->players;
                return (object)[
                    'key'       => $model->weapon->key,
                    'name'      => Yii::t('app-weapon', $model->weapon->name),
                    'subweapon' => (object)[
                        'key'   => $model->weapon->subweapon->key,
                        'name'  => Yii::t('app-subweapon', $model->weapon->subweapon->name),
                    ],
                    'special'   => (object)[
                        'key'   => $model->weapon->special->key,
                        'name'  => Yii::t('app-special', $model->weapon->special->name),
                    ],
                    'count'     => (int)$model->players,
                    'avg_kill'  => $model->players > 0 ? ($model->total_kill / $model->players) : null,
                    'sum_kill'  => $model->total_kill,
                    'avg_death' => $model->players > 0 ? ($model->total_death / $model->players) : null,
                    'sum_death' => $model->total_death,
                    'wp'        => $model->players > 0 ? ($model->win_count * 100 / $model->players) : null,
                    'win_count' => $model->win_count,
                    'avg_inked' => $model->point_available > 0 ? ($model->total_point / $model->point_available) : null,
                ];
            },
            $query->all()
        );

        usort($list, function ($a, $b) {
            foreach (['count', 'wp', 'avg_kill', 'avg_death'] as $key) {
                $tmp = $b->$key - $a->$key;
                if ($tmp != 0) {
                    return $tmp;
                }
            }
            return strnatcasecmp($a->name, $b->name);
        });

        $battleCount = StatWeaponBattleCount::findOne(['rule_id' => $rule->id]);

        return (object)[
            'battle_count' => $battleCount ? $battleCount->count : 0,
            'player_count' => $totalPlayers,
            'weapons' => $list,
        ];
    }

    public function getUserWeapons()
    {
        $favWeaponQuery = (new Query())
            ->select('*')
            ->from('{{user_weapon}} AS {{m}}')
            ->andWhere([
                'not exists',
                (new Query())
                    ->select('(1)')
                    ->from('{{user_weapon}} AS {{s}}')
                    ->andWhere('{{m}}.[[user_id]] = {{s}}.[[user_id]]')
                    ->andWhere('{{m}}.[[count]] < {{s}}.[[count]]')
            ]);

        $query = (new Query())
            ->select(['weapon_id', 'count' => 'COUNT(*)'])
            ->from(sprintf(
                '(%s) AS {{tmp}}',
                $favWeaponQuery->createCommand()->rawSql
            ))
            ->groupBy('{{tmp}}.[[weapon_id]]')
            ->orderBy('COUNT(*) DESC');

        $list = $query->createCommand()->queryAll();
        $weapons = $this->getWeapons(array_map(function ($row) {
            return $row['weapon_id'];
        }, $list));

        return array_map(function ($row) use ($weapons) {
            return (object)[
                'weapon_id' => $row['weapon_id'],
                'user_count' => $row['count'],
                'weapon' => @$weapons[$row['weapon_id']] ?: null,
            ];
        }, $list);
    }

    public function getWeapons(array $weaponIdList)
    {
        $list = Weapon::find()
            ->andWhere(['in', '{{weapon}}.[[id]]', $weaponIdList])
            ->all();
        $ret = [];
        foreach ($list as $weapon) {
            $ret[$weapon->id] = $weapon;
        }
        return $ret;
    }

    private function convertWeapons2Sub($in)
    {
        $ret = [];
        foreach (Subweapon::find()->all() as $sub) {
            $ret[$sub->key] = (object)[
                'name'      => Yii::t('app-subweapon', $sub->name),
                'count'     => 0,
                'sum_kill'  => 0,
                'sum_death' => 0,
                'win_count' => 0,
                'avg_kill'  => null,
                'avg_death' => null,
                'wp'        => null,
                'encounter_3' => null,
                'encounter_4' => null,
            ];
        }
        foreach ($in->weapons as $weapon) {
            $o = $ret[$weapon->subweapon->key];
            $o->count     += $weapon->count;
            $o->sum_kill  += $weapon->sum_kill;
            $o->sum_death += $weapon->sum_death;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill  = $o->sum_kill / $o->count;
                $o->avg_death = $o->sum_death / $o->count;
                $o->wp = $o->win_count * 100 / $o->count;
                $encounterRate = $o->count / $in->player_count;
                $o->encounter_3 = 100 * (1 - pow(1 - $encounterRate, 3));
                $o->encounter_4 = 100 * (1 - pow(1 - $encounterRate, 4));
            }
        }

        usort($ret, function ($a, $b) {
            foreach (['count', 'wp', 'avg_kill', 'avg_death'] as $key) {
                $tmp = $b->$key - $a->$key;
                if ($tmp != 0) {
                    return $tmp;
                }
            }
            return strnatcasecmp($a->name, $b->name);
        });
        return $ret;
    }

    private function convertWeapons2Special($in)
    {
        $ret = [];
        foreach (Special::find()->all() as $spe) {
            $ret[$spe->key] = (object)[
                'name'      => Yii::t('app-special', $spe->name),
                'count'     => 0,
                'sum_kill'  => 0,
                'sum_death' => 0,
                'win_count' => 0,
                'avg_kill'  => null,
                'avg_death' => null,
                'wp'        => null,
                'encounter_3' => null,
                'encounter_4' => null,
            ];
        }
        foreach ($in->weapons as $weapon) {
            $o = $ret[$weapon->special->key];
            $o->count     += $weapon->count;
            $o->sum_kill  += $weapon->sum_kill;
            $o->sum_death += $weapon->sum_death;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill  = $o->sum_kill / $o->count;
                $o->avg_death = $o->sum_death / $o->count;
                $o->wp = $o->win_count * 100 / $o->count;
                $encounterRate = $o->count / $in->player_count;
                $o->encounter_3 = 100 * (1 - pow(1 - $encounterRate, 3));
                $o->encounter_4 = 100 * (1 - pow(1 - $encounterRate, 4));
                $o->encounter_r = $encounterRate * 100;
            }
        }

        usort($ret, function ($a, $b) {
            foreach (['count', 'wp', 'avg_kill', 'avg_death'] as $key) {
                $tmp = $b->$key - $a->$key;
                if ($tmp != 0) {
                    return $tmp;
                }
            }
            return strnatcasecmp($a->name, $b->name);
        });
        return $ret;
    }
}

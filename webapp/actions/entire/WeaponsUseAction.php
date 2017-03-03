<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use app\components\validators\WeaponKeyValidator;
use app\models\Event;
use app\models\GameMode;
use app\models\Rule;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponCompareForm;
use app\models\WeaponType;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

class WeaponsUseAction extends BaseAction
{
    public function run()
    {
        $form = Yii::createObject(WeaponCompareForm::class);
        if (!$form->load($_GET) || !$form->validate()) {
            $form = Yii::createObject(WeaponCompareForm::class);
        }
        // 意味のある情報がセットされていないとき、デモ用表示にする
        if (!$form->toQueryParams()) {
            $form->weapon1 = '@shooter';
            $form->weapon2 = '@roller';
            $form->weapon3 = '@charger';
            $form->weapon4 = '@slosher';
            $form->weapon5 = '@splatling';
        }

        return $this->controller->render('weapons-use.tpl', [
            'form' => $form,
            'weapons' => $this->weapons,
            'rules' => $this->rules,
            'data' => $this->getData($form),
        ]);
    }

    public function getWeapons() : array
    {
        return array_merge(
            [ '' => '-' ],
            $this->getWeaponGroups(),
            [
                Yii::t('app', 'Main Weapon') => $this->getMainWeapon(),
                Yii::t('app', 'Sub Weapon') => $this->getSubWeapon(),
                Yii::t('app', 'Special') => $this->getSpecialWeapon(),
            ]
        );
    }

    public function getWeaponGroups() : array
    {
        $ret = [];
        foreach (WeaponType::find()->orderBy('[[id]] ASC')->all() as $type) {
            $typeName = Yii::t('app-weapon', $type->name);
            $ret[$typeName] = array_merge(
                [ "@{$type->key}" => Yii::t('app-weapon', 'All of {0}', $typeName) ],
                (function (WeaponType $type) : array {
                    $ret = [];
                    foreach ($type->weapons as $weapon) {
                        $ret[$weapon->key] = Yii::t('app-weapon', $weapon->name);
                    }
                    uasort($ret, 'strnatcasecmp');
                    return $ret;
                })($type)
            );
        }
        return $ret;
    }

    public function getMainWeapon() : array
    {
        $ret = [];
        foreach (WeaponType::find()->orderBy('[[id]] ASC')->all() as $type) {
            $ret = array_merge(
                $ret,
                (function (WeaponType $type) : array {
                    $ret = [];
                    $weapons = $type->getWeapons()
                        ->andWhere('[[id]] = [[main_group_id]]')
                        ->asArray()
                        ->all();
                    foreach ($weapons as $weapon) {
                        $ret['~' . $weapon['key']] = Yii::t('app', '{0} etc.', [Yii::t('app-weapon', $weapon['name'])]);
                    }
                    uasort($ret, 'strnatcasecmp');
                    return $ret;
                })($type)
            );
        }
        return $ret;
    }

    public function getSubWeapon() : array
    {
        $ret = [];
        foreach (Subweapon::find()->asArray()->all() as $weapon) {
            $ret['+' . $weapon['key']] = Yii::t('app-subweapon', $weapon['name']);
        }
        uasort($ret, 'strnatcasecmp');
        return $ret;
    }

    public function getSpecialWeapon() : array
    {
        $ret = [];
        foreach (Special::find()->asArray()->all() as $weapon) {
            $ret['*' . $weapon['key']] = Yii::t('app-special', $weapon['name']);
        }
        uasort($ret, 'strnatcasecmp');
        return $ret;
    }

    public function getRules() : array
    {
        $modes = [
            '' => Yii::t('app-rule', 'Any Mode'),
        ];

        foreach (GameMode::find()->with('rules')->orderBy('[[id]] ASC')->all() as $mode) {
            $modeName = Yii::t('app-rule', $mode->name);

            $all = (count($mode->rules) > 1)
                    ? ["@{$mode->key}" => Yii::t('app-rule', 'All of {0}', [$modeName])]
                    : [];

            $rules = [];
            foreach ($mode->rules as $rule) {
                $rules[$rule->key] = Yii::t('app-rule', $rule->name);
            }
            uasort($rules, 'strnatcasecmp');
            $modes[$modeName] = array_merge($all, $rules);
        }

        return $modes;
    }

    public function getData(WeaponCompareForm $form) : array
    {
        $list = $this->queryData($form);
        $ret = [
            'data' => [],
            'events' => count($list) ? $this->getEventData($list[0], $list[count($list) - 1]) : [],
        ];
        foreach (range(1, WeaponCompareForm::NUMBER) as $i) {
            $columnKey = "w{$i}";
            $bColumnKey = "b{$i}";
            if (!isset($list[0][$columnKey])) {
                continue;
            }
            $ret['data'][] = [
                'legend' => $this->makeLegend($form->{"weapon{$i}"}, $form->{"rule{$i}"}),
                'data' => array_map(
                    function (array $row) use ($columnKey, $bColumnKey) : array {
                        $battles = (int)$row[$bColumnKey];
                        return [
                            date('Y-m-d', strtotime(sprintf('%04d-W%02d', $row['isoyear'], $row['isoweek']))),
                            $battles > 0 ? $row[$columnKey] * 100 / $battles : null,
                        ];
                    },
                    $list
                ),
            ];
        }
        return $ret;
    }

    protected function getEventData($firstData, $lastData) : array
    {
        $first = strtotime(sprintf('%04d-W%02d', $firstData['isoyear'], $firstData['isoweek']));
        $last  = strtotime(sprintf('%04d-W%02d', $lastData['isoyear'], $lastData['isoweek']));

        $query = Event::find()
            ->andWhere(['between', 'date', date('Y-m-d\TH:i:sO', $first), date('Y-m-d\TH:i:sO', $last)])
            ->orderBy('[[date]] ASC');

        return array_map(function (array $row) : array {
            return [
                date('Y-m-d', strtotime(date('o-\WW', strtotime($row['date'])))),
                Yii::t('app-event', $row['name']),
                $row['icon'],
            ];
        }, $query->asArray()->all());
    }

    protected function makeLegend($weapon, $rule) : string
    {
        $weaponName = (function ($key) {
            switch (substr($key, 0, 1)) {
                case WeaponKeyValidator::PREFIX_WEAPON_GROUP:
                    $type = WeaponType::findOne(['key' => substr($key, 1)]);
                    return Yii::t('app-weapon', 'All of {0}', [Yii::t('app-weapon', $type->name ?? $key)]);

                case WeaponKeyValidator::PREFIX_MAIN_WEAPON:
                    $weapon = Weapon::findOne(['key' => substr($key, 1)]);
                    return Yii::t('app', '{0} etc.', [Yii::t('app-weapon', $weapon->name ?? $key)]);

                case WeaponKeyValidator::PREFIX_SUB_WEAPON:
                    $sub = Subweapon::findOne(['key' => substr($key, 1)]);
                    return Yii::t('app-subweapon', $sub->name ?? $key);

                case WeaponKeyValidator::PREFIX_SPECIAL_WEAPON:
                    $special = Special::findOne(['key' => substr($key, 1)]);
                    return Yii::t('app-special', $special->name ?? $key);

                default:
                    $weapon = Weapon::findOne(['key' => $key]);
                    return Yii::t('app-weapon', $weapon->name ?? $key);
            }
        })($weapon);

        $ruleName = (function ($key) {
            if ($key == '') {
                return '';
            }
            switch (substr($key, 0, 1)) {
                case '@':
                    $mode = GameMode::findOne(['key' => substr($key, 1)]);
                    return Yii::t('app-rule', $mode->name ?? $key);

                default:
                    $rule = Rule::findOne(['key' => $key]);
                    return Yii::t('app-rule', $rule->name ?? $key);
            }
        })($rule);

        if ($ruleName != '') {
            return sprintf('%s (%s)', $weaponName, $ruleName);
        }
        return (string)$weaponName;
    }

    protected function queryData(WeaponCompareForm $form) : array
    {
        $db = Yii::$app->db;
        $query = (new Query())
            ->select([
                'isoyear' => '{{stat}}.[[isoyear]]',
                'isoweek' => '{{stat}}.[[isoweek]]',
                'battles' => 'SUM({{stat}}.[[battles]])',
            ])
            ->from('stat_weapon_use_count_per_week stat')
            ->innerJoin('rule', 'stat.rule_id = rule.id')
            ->innerJoin('game_mode', 'rule.mode_id = game_mode.id')
            ->innerJoin('weapon', 'stat.weapon_id = weapon.id')
            ->innerJoin('weapon_type', 'weapon.type_id = weapon_type.id')
            ->innerJoin('subweapon', 'weapon.subweapon_id = subweapon.id')
            ->innerJoin('special', 'weapon.special_id = special.id')
            ->innerJoin('weapon w_group', 'weapon.main_group_id = w_group.id')
            ->where(['or',
                ['>', '{{stat}}.[[isoyear]]', 2015],
                ['and',
                    ['=', '{{stat}}.[[isoyear]]', 2015],
                    ['>=', '{{stat}}.[[isoweek]]', 46],
                ]
            ])
            ->groupBy('{{stat}}.[[isoyear]], {{stat}}.[[isoweek]]')
            ->orderBy('{{stat}}.[[isoyear]], {{stat}}.[[isoweek]]');
        foreach (range(1, WeaponCompareForm::NUMBER) as $i) {
            $when = [];
            $whenRule = [];
            $weapon = $form->{"weapon{$i}"};
            if ($weapon == '') {
                continue;
            }
            switch (substr($weapon, 0, 1)) {
                case WeaponKeyValidator::PREFIX_WEAPON_GROUP:
                    $when[] = '{{weapon_type}}.[[key]] = ' . $db->quoteValue(substr($weapon, 1));
                    break;

                case WeaponKeyValidator::PREFIX_MAIN_WEAPON:
                    $when[] = '{{w_group}}.[[key]] = ' . $db->quoteValue(substr($weapon, 1));
                    break;

                case WeaponKeyValidator::PREFIX_SUB_WEAPON:
                    $when[] = '{{subweapon}}.[[key]] = ' . $db->quoteValue(substr($weapon, 1));
                    break;

                case WeaponKeyValidator::PREFIX_SPECIAL_WEAPON:
                    $when[] = '{{special}}.[[key]] = ' . $db->quoteValue(substr($weapon, 1));
                    break;

                default:
                    $when[] = '{{weapon}}.[[key]] = ' . $db->quoteValue($weapon);
                    break;
            }

            $rule = $form->{"rule{$i}"};
            if ($rule != '') {
                if (substr($rule, 0, 1) === '@') {
                    $when[] = '{{game_mode}}.[[key]] = ' . $db->quoteValue(substr($rule, 1));
                    $whenRule[] = '{{game_mode}}.[[key]] = ' . $db->quoteValue(substr($rule, 1));
                } else {
                    $when[] = '{{rule}}.[[key]] = ' . $db->quoteValue($rule);
                    $whenRule[] = '{{rule}}.[[key]] = ' . $db->quoteValue($rule);
                }
            }

            if (!$whenRule) {
                $whenRule[] = '1 = 1';
            }
            $query->select['w' . $i] = sprintf(
                'SUM(CASE WHEN (%s) THEN {{stat}}.[[battles]] ELSE 0 END)',
                implode(' AND ', $when)
            );
            $query->select['b' . $i] = sprintf(
                'SUM(CASE WHEN (%s) THEN {{stat}}.[[battles]] ELSE 0 END)',
                implode(' AND ', $whenRule)
            );
        }
        return $query->all();
    }
}

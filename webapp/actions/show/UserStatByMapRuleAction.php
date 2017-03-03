<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\BattleFilterForm;
use app\models\Map;
use app\models\Rule;
use app\models\User;

class UserStatByMapRuleAction extends BaseAction
{
    use UserStatFilterTrait;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        $filter->validate();

        return $this->controller->render('user-stat-by-map-rule.tpl', array_merge(
            [
                'user' => $user,
                'filter' => $filter,
            ],
            $this->getData($user, $filter)
        ));
    }

    private function getData(User $user, BattleFilterForm $filter)
    {
        $query = (new \yii\db\Query())
            ->select([
                'map_key'   => 'MAX({{map}}.[[key]])',
                'rule_key'  => 'MAX({{rule}}.[[key]])',
                'result'    => '(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN \'win\' ELSE \'lose\' END)',
                'count'     => 'COUNT(*)',
            ])
            ->from('battle')
            ->innerJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->leftJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->leftJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
            ->leftJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->leftJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->leftJoin('rank', '{{battle}}.[[rank_id]] = {{rank}}.[[id]]')
            ->leftJoin('rank_group', '{{rank}}.[[group_id]] = {{rank_group}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $user->id])
            ->andWhere(['in', '{{battle}}.[[is_win]]', [ true, false ]])
            ->groupBy(['{{battle}}.[[map_id]]', '{{battle}}.[[rule_id]]', '{{battle}}.[[is_win]]']);
        
        if ($filter && !$filter->hasErrors()) {
            $this->filter($query, $filter);
        }

        $maps = Map::find()->all();
        $rules = Rule::find()->all();

        $ret = [];
        foreach ($maps as $map) {
            $tmp = [];
            foreach ($rules as $rule) {
                $tmp[$rule->key] = [
                    'win' => 0,
                    'lose' => 0,
                ];
            }
            $ret[$map->key] = $tmp;
        }

        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            $ret[$row->map_key][$row->rule_key][$row->result] = (int)$row->count;
        }

        $maps2 = [];
        foreach ($maps as $map) {
            $maps2[$map->key] = Yii::t('app-map', $map->name);
        }
        asort($maps2);

        $rules2 = [];
        foreach ($rules as $rule) {
            $rules2[$rule->key] = Yii::t('app-rule', $rule->name);
        }
        asort($rules2);

        return [
            'data' => $ret,
            'mapNames' => $maps2,
            'ruleNames' => $rules2,
        ];
    }
}

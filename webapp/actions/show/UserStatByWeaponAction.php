<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\BattleFilterForm;
use app\models\User;
use app\models\Weapon;

class UserStatByWeaponAction extends BaseAction
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

        return $this->controller->render('user-stat-by-weapon.tpl', [
            'user' => $user,
            'list' => $this->getList($user, $filter),
            'filter' => $filter,
        ]);
    }

    public function getList(User $user, BattleFilterForm $filter)
    {
        $query = (new Query())
            ->select([
                'weapon_key'    => 'MAX({{weapon}}.[[key]])',
                'weapon_name'   => 'MAX({{weapon}}.[[name]])',
                'battles'       => 'COUNT(*)',
                'battles_win'   => 'SUM(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)',
                'kills'         => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle}}.[[kill]]',
                ])),
                'deaths'        => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle}}.[[death]]',
                ])),
                'kd_available'  => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[death]] IS NULL THEN 0',
                    'ELSE 1'
                ])),
            ])
            ->from('battle')
            ->innerJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->innerJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
            ->innerJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->innerJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->leftJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->leftJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->leftJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->leftJoin('rank', '{{battle}}.[[rank_id]] = {{rank}}.[[id]]')
            ->leftJoin('rank_group', '{{rank}}.[[group_id]] = {{rank_group}}.[[id]]')
            ->andWhere([
                '{{battle}}.[[user_id]]' => $user->id,
                '{{battle}}.[[is_win]]' => [ true, false ],
            ])
            ->groupBy('{{battle}}.[[weapon_id]]');
        if ($filter && !$filter->hasErrors()) {
            $this->filter($query, $filter);
        }
        $list = $query->all();
        usort($list, function ($a, $b) {
            return $b['battles'] <=> $a['battles'];
        });
        return $list;
    }
}

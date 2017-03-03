<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Rule;
use app\models\Knockout;

class KnockoutAction extends BaseAction
{
    public function run()
    {
        $rules = [];
        $query = Rule::find()
            ->innerJoinWith('mode')
            ->andWhere(['{{game_mode}}.[[key]]' => 'gachi']);
        foreach ($query->all() as $rule) {
            $rules[$rule->key] = Yii::t('app-rule', $rule->name);
        }
        asort($rules);

        $data = [];
        foreach (Knockout::find()->with('map', 'rule')->each() as $row) {
            $map = $row->map->key;
            $rule = $row->rule->key;
            if (!isset($data[$map])) {
                $row->map->name = Yii::t('app-map', $row->map->name);
                $data[$map] = (object)[
                    'map' => $row->map,
                    'rules' => new \stdClass(),
                ];
            }
            $data[$map]->rules->{$rule} = (object)[
                'battles' => $row->battles,
                'knockouts' => $row->knockouts,
            ];
        }

        uasort($data, function ($a, $b) {
            return strnatcasecmp($a->map->name, $b->map->name);
        });

        return $this->controller->render('knockout.tpl', [
            'rules' => $rules,
            'data' => $data,
        ]);
    }
}

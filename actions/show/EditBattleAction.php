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
use app\models\Battle;
use app\models\BattleForm;
use app\models\GameMode;
use app\models\Lobby;
use app\models\Map;
use app\models\Rule;
use app\models\Weapon;
use app\models\WeaponType;

class EditBattleAction extends BaseAction
{
    private $battle;

    public function init()
    {
        parent::init();
        $this->battle = null;
        if ($user = Yii::$app->user->identity) {
            $this->battle = Battle::findOne([
                'id' => Yii::$app->request->get('battle'),
                'user_id' => $user->id,
            ]);
        }
    }

    public function getIsEditable()
    {
        return !!$this->battle;
    }

    public function run()
    {
        $form = new BattleForm();
        if (Yii::$app->request->isPost) {
            if ($form->load($_POST) && $form->validate()) {
                $this->battle->attributes = $form->attributes;
                if ($this->battle->save()) {
                    $this->controller->redirect([
                        'show/battle',
                        'screen_name' => $this->battle->user->screen_name,
                        'battle' => $this->battle->id,
                    ]);
                    return;
                }
            }
        } else {
            $form->attributes = $this->battle->attributes;
        }
        return $this->controller->render('edit-battle.tpl', [
            'user' => $this->battle->user,
            'battle' => $this->battle,
            'form' => $form,
            'lobbies' => $this->makeLobbies(),
            'rules' => $this->makeRules(),
            'maps' => $this->makeMaps(),
            'weapons' => $this->makeWeapons(),
        ]);
    }

    private function makeLobbies()
    {
        $ret = ['' => Yii::t('app', 'Unknown')];
        foreach (Lobby::find()->orderBy('[[id]] ASC')->all() as $model) {
            $ret[$model->id] = Yii::t('app-rule', $model->name);
        }
        return $ret;
    }

    private function makeRules()
    {
        $ret = ['' => Yii::t('app', 'Unknown')];
        $gameModes = GameMode::find()->orderBy('[[id]] ASC')->all();
        foreach ($gameModes as $gameMode) {
            $gameModeText = Yii::t('app-rule', $gameMode->name); // "ナワバリバトル"
            $rules = Rule::find()
                ->andWhere(['mode_id' => $gameMode->id])
                ->orderBy('[[id]] ASC')
                ->all();
            $mode = [];
            foreach ($rules as $rule) {
                $mode[$rule->id] = Yii::t('app-rule', $rule->name);
            }
            asort($mode);
            $ret[$gameModeText] = $mode;
        }
        return $ret;
    }

    private function makeMaps()
    {
        $ret = [];
        foreach (Map::find()->all() as $map) {
            $ret[$map->id] = Yii::t('app-map', $map->name);
        }
        asort($ret);
        return static::array_merge(
            ['' => Yii::t('app', 'Unknown')],
            $ret
        );
    }

    private function makeWeapons()
    {
        $ret = [];
        $types = WeaponType::find()->orderBy('[[id]] ASC')->all();
        foreach ($types as $type) {
            $typeName = Yii::t('app-weapon', $type->name);

            $tmp = [];
            $weapons = Weapon::find()->andWhere(['type_id' => $type->id])->all();
            foreach ($weapons as $weapon) {
                $tmp[$weapon->id] = Yii::t('app-weapon', $weapon->name);
            }
            asort($tmp);
            $ret[$typeName] = $tmp;
        }
        return static::array_merge(
            ['' => Yii::t('app', 'Unknown')],
            $ret
        );
    }

    private static function array_merge()
    {
        $ret = [];
        foreach (func_get_args() as $arg) {
            foreach ($arg as $k => $v) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }
}
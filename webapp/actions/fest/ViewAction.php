<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use Yii;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\Splatfest;
use app\models\SplatfestBattleSummary;
use app\models\SplatfestTeam;

class ViewAction extends BaseAction
{
    public $fest;
    public $alpha;
    public $bravo;

    public function init()
    {
        $r = parent::init();
        $this->doInit();
        return $r;
    }

    private function doInit()
    {
        if (!$form = $this->createAndValidateRequestForm()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $this->fest = Splatfest::find()
            ->innerJoinWith('region', false)
            ->andWhere([
                '{{region}}.[[key]]' => $form->region,
                '{{splatfest}}.[[order]]' => $form->order,
            ])
            ->limit(1)
            ->one();
        if (!$this->fest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $this->alpha = SplatfestTeam::findOne(['fest_id' => $this->fest->id, 'team_id' => 1]);
        $this->bravo = SplatfestTeam::findOne(['fest_id' => $this->fest->id, 'team_id' => 2]);
        if (!$this->alpha || !$this->bravo) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    private function createAndValidateRequestForm()
    {
        $req = Yii::$app->request;
        $model = DynamicModel::validateData(
            [
                'region' => $req->get('region'),
                'order' => $req->get('order'),
            ],
            [
                [['region', 'order'], 'required'],
                [['region'], 'string', 'min' => 2, 'max' => 2],
                [['order'], 'integer', 'min' => 1],
            ]
        );
        if ($model->validate()) {
            return $model;
        }
        return false;
    }

    public function run()
    {
        return $this->controller->render('view.tpl', [
            'fest'  => $this->fest,
            'alpha' => $this->alpha,
            'bravo' => $this->bravo,
            'results' => $this->results,
        ]);
    }

    public function getResults()
    {
        $query = SplatfestBattleSummary::find()
            ->andWhere(['{{splatfest_battle_summary}}.[[fest_id]]' => $this->fest->id])
            ->orderBy('{{splatfest_battle_summary}}.[[timestamp]] ASC');

        return array_map(
            function ($a) {
                return [
                    'at' => strtotime($a->timestamp),
                    'alpha' => $a->alpha_win + $a->bravo_lose,
                    'bravo' => $a->bravo_win + $a->alpha_lose,
                ];
            },
            $query->all()
        );
    }
}

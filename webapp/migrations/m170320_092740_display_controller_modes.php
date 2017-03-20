<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\models\ControllerMode;
use app\models\DisplayMode;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

class m170320_092740_display_controller_modes extends Migration
{
    public function up()
    {
        $this->execute(sprintf(
            'CREATE TABLE {{display_controller}} (%s)',
            implode(', ', [
                '[[display_id]] INTEGER NOT NULL REFERENCES {{display_mode}}([[id]])',
                '[[controller_id]] INTEGER NOT NULL REFERENCES {{controller_mode}}([[id]])',
                'PRIMARY KEY([[display_id]], [[controller_id]])',
            ])
        ));
        
        $d = ArrayHelper::map(DisplayMode::find()->asArray()->all(), 'key', 'id');
        $c = ArrayHelper::map(ControllerMode::find()->asArray()->all(), 'key', 'id');

        $this->batchInsert('display_controller', ['display_id', 'controller_id'], [
            [ $d['tv'], $c['procon'] ],
            [ $d['tv'], $c['joycon_with_grip'] ],
            [ $d['tv'], $c['joycon_wo_grip'] ],
            [ $d['tabletop'], $c['procon'] ],
            [ $d['tabletop'], $c['joycon_with_grip'] ],
            [ $d['tabletop'], $c['joycon_wo_grip'] ],
            [ $d['handheld'], $c['handheld'] ],
        ]);
    }

    public function down()
    {
        $this->dropTable('display_controller');
    }
}

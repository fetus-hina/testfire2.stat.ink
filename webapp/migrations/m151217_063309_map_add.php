<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;
use app\models\Map;

class m151217_063309_map_add extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('map', [ 'key', 'name' ], [
            ['shottsuru', 'Piranha Pit'],
            ['anchovy',   'Ancho-V Games'],
        ]);

        $this->batchInsert(
            'splapi_map',
            [ 'map_id', 'name' ],
            [
                [ Map::findOne(['key' => 'shottsuru'])->id, 'ショッツル鉱山' ],
                [ Map::findOne(['key' => 'anchovy'])->id,   'アンチョビットゲームズ' ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('splapi_map', [
            'map_id' => array_map(
                function ($a) {
                    return $a->id;
                },
                Map::findAll(['key' => ['shottsuru', 'anchovy']])
            )
        ]);
        $this->delete('map', ['key' => ['shottsuru', 'anchovy']]);
    }
}

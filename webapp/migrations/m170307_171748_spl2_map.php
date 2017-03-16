<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m170307_171748_spl2_map extends Migration
{
    public function safeUp()
    {
        $this->delete('splapi_map');
        $this->delete('map');
        $this->batchInsert('map', ['key', 'name', 'short_name', 'release_at'], [
            ['battera',   'The Reef',             'Reef',         '2017-03-25 04:00:00+09'],
            ['fujitsubo', 'Musselforge Fitness',  'Fitness',      '2017-03-25 04:00:00+09'],
            ['gangaze',   'Diadema Amphitheater', 'Amphitheater', '2017-03-25 04:00:00+09'],
        ]);
    }

    public function down()
    {
        echo "m170307_171748_spl2_map cannot be reverted.\n";
        return false;
    }
}

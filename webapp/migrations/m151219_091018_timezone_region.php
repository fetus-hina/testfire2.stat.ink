<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;
use app\models\Region;

class m151219_091018_timezone_region extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{timezone}} ADD COLUMN [[region_id]] INTEGER');
        $this->addForeignKey('fk_timezone_1', 'timezone', 'region_id', 'region', 'id');

        // Japan
        $this->update(
            'timezone',
            ['region_id' => Region::findOne(['key' => 'jp'])->id],
            ['identifier' => 'Asia/Tokyo']
        );

        // Europe/Oceania
        $this->update(
            'timezone',
            ['region_id' => Region::findOne(['key' => 'eu'])->id],
            ['or like', 'identifier', [
                    'Etc/UTC',
                    'Europe/%',
                    'Australia/%',
                ], false]
        );

        // North America
        $this->update(
            'timezone',
            ['region_id' => Region::findOne(['key' => 'na'])->id],
            ['or like', 'identifier', [
                    'America/%',
                    'Pacific/Honolulu',
                ], false]
        );

        $this->execute('ALTER TABLE {{timezone}} ALTER COLUMN [[region_id]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{timezone}} DROP COLUMN [[region_id]]');
    }
}

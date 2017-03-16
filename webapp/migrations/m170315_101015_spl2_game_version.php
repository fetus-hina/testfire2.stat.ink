<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\models\SplatoonVersion;
use yii\db\Migration;

class m170315_101015_spl2_game_version extends Migration
{
    public function safeUp()
    {
        $this->insert('splatoon_version', [
            'tag'           => 'v0.0.1',
            'name'          => 'Testfire',
            'released_at'   => '2017-01-01T00:00:00+00',
        ]);
        $id = SplatoonVersion::findOne(['name' => 'Testfire'])->id;
        
        // 今迄に登録されているバトルがあれば全て Testfire 版として上書きする
        $this->update('battle', ['version_id' => $id]);

        // Testfire 以外のバージョンを全削除
        $this->delete('splatoon_version', ['<>', 'id', $id]);
    }

    public function down()
    {
        echo "m170315_101015_spl2_game_version cannot be reverted.\n";
        return false;
    }
}

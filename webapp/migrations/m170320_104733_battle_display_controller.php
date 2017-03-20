<?php
use yii\db\Migration;

class m170320_104733_battle_display_controller extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[display_id]] INTEGER NULL REFERENCES {{display_mode}}([[id]])',
            'ADD COLUMN [[controller_id]] INTEGER NULL REFERENCES {{controller_mode}}([[id]])',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'DROP COLUMN [[display_id]]',
            'DROP COLUMN [[controller_id]]',
        ]));
    }
}

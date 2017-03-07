<?php
use yii\db\Migration;

class m170307_174950_spl2_subweapon extends Migration
{
    public function safeUp()
    {
        $this->delete('subweapon', ['not', ['key' => [
            'splashbomb',
            'kyubanbomb',
            'quickbomb',
        ]]]);
        $this->insert('subweapon', [
            'key' => 'curlingbomb',
            'name' => 'Curling Bomb',
        ]);
    }

    public function down()
    {
        echo "m170307_174950_spl2_subweapon cannot be reverted.\n";
        return false;
    }
}

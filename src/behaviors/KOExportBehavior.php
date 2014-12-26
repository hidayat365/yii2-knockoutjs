<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 27/12/14
 * Time: 7:16 AM
 */

namespace damiandennis\knockoutjs;

use \yii\base\Behavior;

class KOExportBehavior extends Behavior
{
    public function toJSON($scenario = 'toJSON')
    {
        $data = [];
        $scenarios = $this->owner->scenarios();
        if (isset($scenarios[$scenario])) {
            foreach($scenarios[$scenario] as $field) {
                $data[$field] = $this->owner->{$field};
            }
        }
        return $data;
    }
}

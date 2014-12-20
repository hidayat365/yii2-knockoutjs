<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 19/12/14
 * Time: 8:04 PM
 */

namespace damiandennis\knockoutjs;

class KOPostAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'damiandennis\knockoutjs\KnockoutAsset',
    ];

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../../../../bower/knockout-postbox/src');
        $this->setupAssets('js', ['knockout-postbox']);
        parent::init();
    }
}
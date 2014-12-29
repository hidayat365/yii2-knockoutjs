<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 30/12/14
 * Time: 5:37 AM
 */

namespace damiandennis\knockoutjs;

class KOClassBindingProvider extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'damiandennis\knockoutjs\KnockoutAsset',
    ];

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../../../../npm/knockout-classBindingProvider/src');
        $this->setupAssets('js', ['knockout-classBindingProvider']);
        parent::init();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 19/12/14
 * Time: 8:46 PM
 */
namespace damiandennis\knockoutjs;

class KODeferredUpdatesAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'damiandennis\knockoutjs\KnockoutAsset',
    ];

    public function init()
    {
        $this->setSourcePath('@npm/knockout-deferred-updates');
        $this->setupAssets('js', ['knockout-deferred-updates']);
        parent::init();
    }
}
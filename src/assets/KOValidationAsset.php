<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 19/12/14
 * Time: 8:04 PM
 */

namespace damiandennis\knockoutjs;

class KOValidationAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
        'damiandennis\knockoutjs\KnockoutAsset',
    ];

    public function init()
    {
        $this->setSourcePath('@bower/ko-validation/dist');
        $this->setupAssets('js', ['knockout.validation']);
        $this->setSourcePath(__DIR__ . '/../js/');
        $this->setupAssets('js', ['ko.yii.validation']);
        parent::init();
    }
}
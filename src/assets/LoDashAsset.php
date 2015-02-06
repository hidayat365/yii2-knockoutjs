<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 7/02/15
 * Time: 6:31 AM
 */

namespace damiandennis\knockoutjs;

class LoDashAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        $this->setSourcePath('@bower/lodash');
        $this->setupAssets('js', ['lodash']);
        parent::init();
    }
}
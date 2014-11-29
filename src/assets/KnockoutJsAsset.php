<?php
/**
 * User: Damian
 * Date: 19/05/14
 * Time: 6:05 AM
 */

namespace damiandennis\stickymojo;

class KnockoutAsset extends AssetBundle
{

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../../../../bower/knockout/dist');
        $this->setupAssets('js', ['knockout']);
        parent::init();
    }
}
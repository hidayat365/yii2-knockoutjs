<?php
/**
 * Created by PhpStorm.
 * User: damian
 * Date: 27/01/15
 * Time: 5:13 AM
 */
namespace damiandennis\knockoutjs;

use Yii;
use yii\helpers\Json;
use yii\validators\BooleanValidator;
use yii\validators\EmailValidator;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\web\View;

class KOCustomAsset extends AssetBundle
{

    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../js/');
        $this->setupAssets('js', ['ko.yii.validation']);
        $view = Yii::$app->getView();
        $required = new RequiredValidator();
        $bool = new BooleanValidator();
        $email = new EmailValidator();
        $string = new StringValidator();
        $number = new NumberValidator();
        $integer = new NumberValidator(['integerOnly' => true]);

        $messages = Json::encode([
            'required' => ['default' => $required->message],
            'string'   => [
                'default'  => $string->message,
                'tooLong'  => $string->tooLong,
                'tooShort' => $string->tooShort,
            ],
            'integer'  => [
                'default'     => $number->message,
                'integerOnly' => $integer->message,
                'tooSmall'    => $number->tooSmall,
                'tooBig'      => $number->tooBig
            ],
            'boolean'  => ['default' => $bool->message],
            'email'    => ['default' => $email->message],
        ]);

        $view->registerJs(
            "$(function() {
                ko.yii.validation({$messages});
            });
            \n",
            View::POS_END
        );
        parent::init();
    }
}
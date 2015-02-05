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
        $string = new StringValidator(['min' => 1, 'max' => 1, 'length' => 1]);
        $number = new NumberValidator(['min' => 1, 'max' => 1]);
        $integer = new NumberValidator(['integerOnly' => true]);

        $messages = Json::encode([
            'required' => ['default' => $required->message],
            'string'   => [
                'default'  => Yii::t('yii', $string->message),
                'tooLong'  => Yii::t('yii', $string->tooLong),
                'tooShort' => Yii::t('yii', $string->tooShort),
            ],
            'integer'  => [
                'default'     => Yii::t('yii', $number->message),
                'integerOnly' => Yii::t('yii', $integer->message),
                'tooSmall'    => Yii::t('yii', $number->tooSmall),
                'tooBig'      => Yii::t('yii', $number->tooBig)
            ],
            'boolean'  => ['default' => Yii::t('yii', $bool->message)],
            'email'    => ['default' => Yii::t('yii', $email->message)],
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
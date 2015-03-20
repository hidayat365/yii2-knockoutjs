<?php
/**
 * @file helper class for KnockoutJS.
 *
 * @user: Damian Dennis <damiandennis@gmail.com>
 * Date: 14/02/15
 * Time: 7:21 AM
 */
namespace damiandennis\knockoutjs;

use yii\base\Exception;
use yii\helpers\Json;

class KO
{

    public static function virtual($start)
    {
        return self::beginVirtual($start) . self::endVirtual();
    }

    /**
     * @param string $start The KO command
     * @return string Start the virtual attribute.
     */
    public static function beginVirtual($start)
    {
        return "<!-- ko {$start} -->";
    }

    /**
     * @return string End the virtual attribute.
     */
    public static function endVirtual()
    {
        return '<!-- /ko -->';
    }

    /**
     * Turns models in json
     *
     * @param yii\base\Model|array $models the models to convert.
     * @param array $relations an array of string which represent relations of relations. i.e restaurants.menus
     * @param string $scenario The scenario in which pulls back the required data for the model.
     * @return string an JSON encode string of the data.
     */
    public static function modelsToJson($models, $relations = [], $scenario = 'default')
    {
        return JSON::encode(self::prepareToJson($models, $relations, $scenario));
    }

    /**
     * Prepares models for conversion to json.
     *
     * @param yii\base\Model|array $models the models to convert.
     * @param array $relations an array of string which represent relations of relations. i.e restaurants.menus
     * @param string $scenario The scenario in which pulls back the required data for the model.
     * @return string an JSON encode string of the data.
     */
    public static function prepareToJson($models, $relations = [], $scenario = 'default')
    {
        $data = false;
        if (is_array($models)) {
            $relations = array_fill(0, count($models), $relations);
            $scenario = array_fill(0, count($models), $scenario);
            $data = array_map(
                function ($row, $relations, $scenario) {
                    return self::extractData($row, $relations, $scenario);
                },
                $models,
                $relations,
                $scenario
            );
        } else {
            $data = self::extractData($models, $relations, $scenario);
        }
        return $data;
    }

    protected static function extractData($model, $relations, $scenario)
    {
        $data = self::getModelDetails($model, $scenario, self::getDirectRelations($relations));

        if ($relations) {
            foreach ($relations as $relation) {
                $paths = explode('.', $relation);
                self::recursePath($paths, $model, $data, [], $scenario);
            }
        }
        return $data;
    }

    protected static function getDirectRelations($relations)
    {
        return array_flip(array_map(function ($item) {
            $temp = explode('.', $item);
            return $temp[0];
        }, $relations));
    }

    protected static function getPathlessClass($model)
    {
        $class = explode('\\', get_class($model));
        return end($class);
    }



    /**
     * Recursively pulls back model data.
     *
     * @param array $paths The paths to recurse over.
     * @param yii\base\Model $model The current model.
     * @param array $data The data to return
     * @param array $pathsOut The paths that have been converted.
     * @param string $scenario The scenario to receive.
     */
    protected static function recursePath($paths, $model, &$data, $pathsOut, $scenario)
    {
        if ($paths) {
            $path = array_shift($paths);
            array_push($pathsOut, $path);

            if (!is_array($model->{$path})) {
                if ($model->{$path}) {
                    $data['relations'][$path] = self::getModelDetails($model->{$path}, $scenario, self::getDirectRelations($paths));
                    $model = $model->{$path};
                }
                self::recursePath($paths, $model, $data['relations'][$path], $pathsOut, $scenario);
            } else {
                $newData = &$data;
                foreach ($pathsOut as $k => $i) {
                    if ($k == count($pathsOut) - 1) {
                        $newData['relations'] = isset($newData['relations']) ? $newData['relations'] : [];
                        $newData = &$newData['relations'];
                        $newData[$i] = [];
                        $newData = &$newData[$i];
                    }
                }
                foreach ($model->{$path} as $key => $relation) {
                    $newData[$key] = self::getModelDetails($relation, $scenario, self::getDirectRelations($paths));
                    $model = $relation;
                    self::recursePath($paths, $model, $newData[$key], $pathsOut, $scenario);
                }
            }
        } else {
            return;
        }
    }

    public static function getModelDetails($model, $scenario, $relations)
    {
        if (method_exists($model, 'toJSON')) {
            $attributes = $model->toJSON();
        } else {
            $model->attachBehavior('toJSON', new KOExportBehavior());
            $attributes = $model->toJSON($scenario);
        }

        $data = [
            'isNewRecord'       => $model->isNewRecord,
            'values'            => $attributes,
            'attributeLabels'   => $model->attributeLabels(),
            'relations'         => $relations,
            'rules'             => $model->rules(),
            'attributes'        => [],
            'className'             => self::getPathlessClass($model),
            'primaryKey'        => $model->primaryKey()
        ];

        foreach ($attributes as $key => $attribute) {
            $data['attributes'][$key] = null;
        }

        return $data;
    }

    public static function validJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}


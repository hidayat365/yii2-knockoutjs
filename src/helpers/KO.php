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
use yii\base\Model;
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
     * @param Model|array $models the models to convert.
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
     * @param Model|array $models the models to convert.
     * @param array $relations an array of string which represent relations of relations. i.e restaurants.menus
     * @param string $scenario The scenario in which pulls back the required data for the model.
     * @return string an JSON encode string of the data.
     */
    public static function prepareToJson($models, $relations = [], $scenario = 'default')
    {
        $data = false;
        if (is_array($models)) {
            $data = array_map(
                function ($row, $relations, $scenario) use ($relations, $scenario) {
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

    /**
     * @param $model
     * @param $relations
     * @param $scenario
     * @return array
     */
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

    /**
     * @param $relations
     * @return array
     */
    protected static function getDirectRelations($relations)
    {
        return array_flip(array_map(function ($item) {
            $temp = explode('.', $item);
            return $temp[0];
        }, $relations));
    }

    /**
     * @param $model
     * @return mixed
     */
    protected static function getPathlessClass($model)
    {
        $class = explode('\\', get_class($model));
        return end($class);
    }

    /**
     * Recursively pulls back model data.
     *
     * @param array $paths The paths to recurse over.
     * @param Model $model The current model.
     * @param array $data The data to return
     * @param array $pathsOut The paths that have been converted.
     * @param string $scenario The scenario to receive.
     */
    protected static function recursePath($paths, Model $model, &$data, $pathsOut, $scenario)
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

    /**
     * Creates a data structure for use in knockout model class.
     *
     * @param Model $model The model to fetch required data for.
     * @param string $scenario The scenario to get attributes (ignored if model has a toJSON method)
     * @param array $relations The relations to get .
     * @return array
     */
    public static function getModelDetails(Model $model, $scenario, $relations)
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
            'className'         => self::getPathlessClass($model),
            'primaryKey'        => $model->primaryKey()
        ];

        foreach ($attributes as $key => $attribute) {
            $data['attributes'][$key] = null;
        }

        return $data;
    }

    /**
     * Checks for a correct JSON string.
     *
     * @param string $string The json string to check.
     * @return bool true if the json string is correct or false otherwise.
     */
    public static function validJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}


<?php
/**
 * Created by PhpStorm.
 * User: mager_v
 * Date: 03.06.2019
 * Time: 10:02
 */

namespace app\components;


use app\models\Category;
use app\models\Configuration;
use app\models\ConfigurationRelations;
use app\models\Product;
use yii\db\Query;
use yii\web\BadRequestHttpException;

class SelectionComponents
{
    /**
     * @param Configuration $configuration
     * @param $request
     * @return \app\models\ProductQuery
     * @throws BadRequestHttpException
     */
    public static function productsProcessing(Configuration $configuration, $request)
    {
        if (!$request) {
            throw new BadRequestHttpException();
        }

        unset($request['configuration_id']);

        $category_id = $request['category_id'];
        $requestCategory = Category::findOne(['id' => $category_id]);

        $configurationRelation = $configuration->configurationRelations;
        $product_ids = self::getProductsIdsOnRelation($configurationRelation);
        $productsInComponents = self::getProducts($product_ids);

        $main_property_values = [];
        $main_property_ids = [];

        /** @var Product $product */
        foreach ($productsInComponents as $product) {
            $category = $product->category;
            foreach ($category->propertyCategories as $propertyCategory) {
                if (!$propertyCategory->property->main) {
                    continue;
                }
                if ($propertyCategory->category_id === $category_id) {
                    continue;
                }
                foreach ($product->propertyRelations as $propertyRelation) {
                    if ($propertyRelation->property_id === $propertyCategory->property->id) {
                        foreach ($requestCategory->propertyCategories as $propertyRequestCategory) {
                            if ($propertyRequestCategory->property_id === $propertyCategory->property->id) {
                                $main_property_values[] = $propertyRelation->value;
                                $main_property_ids[] = $propertyCategory->property->id;
                            }
                        }
                    }
                }
            }
        }

        if (isset($request['property'])) {
            $property_values = array_map(function($property) {
                $array = json_decode($property, true);
                return $array['value'];
            }, $request['property']);
            $property_ids = array_map(function ($property) {
                $array = json_decode($property, true);
                return $array['id'];
            }, $request['property']);

            $property_values = array_merge($property_values, $main_property_values);
            $property_ids = array_merge($property_ids, $main_property_ids);
        } else {
            $property_values = $main_property_values;
            $property_ids = $main_property_ids;
        }

        $products = Product::find()
            ->with(['category', 'productRelations.store', 'productMedia', 'propertyRelations.property.group', 'productStocks.stock'])
            ->where(['category_id' => $category_id, 'status' => Product::PRODUCT_STATUS_VALUE_PUBLIC]);
            if (isset($request['property']) || $property_ids && $property_values) {
                $products->andWhere([
                    'in',
                    'id',
                    (new Query())
                        ->select('product_id')
                        ->from('property_relations')
                        ->where(['value' => array_unique($property_values)])
                        ->andWhere(['in', 'property_id', array_unique($property_ids)])
                ]);
            } else {
                $products->where($request);
            }

//            var_dump($products); die;

        return $products;
    }



    public static function getProducts($ids)
    {
        return Product::find()->where(['id' => $ids])->all();
    }

    private static function getProductsIdsOnRelation($relations)
    {
        /**
         * @var $relations ConfigurationRelations[]
         */

        $product_ids = [];

        foreach ($relations as $relation) {
            $product_ids[] = $relation->product_id;
        }

        return $product_ids;
    }
}
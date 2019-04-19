<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Admin application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/admin.css'
    ];
    public $js = [];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        if (YII_ENV_TEST) {
            parent::init();
            return;
        }
        $this->css = array_map(function ($el) {
            return $el . '?' . filemtime($el);
        }, $this->css);
        $this->js = array_map(function ($el) {
            return $el . '?' . filemtime($el);
        }, $this->js);
        parent::init();
    }
}

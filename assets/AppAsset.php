<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//        (YII_ENV_DEV) ? 'css/style.css' : 'css/style.min.css',
    'css/temp.css'
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
        if (YII_ENV_DEV) {
            $this->js[] = 'js/manifest.js';
            $this->js[] = 'js/vendor.js';
            $this->js[] = 'js/app.js';
        } else {
            $this->js[] = 'js/app.min.js';
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

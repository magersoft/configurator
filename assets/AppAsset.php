<?php


namespace app\assets;


use yii\web\AssetBundle;

/**
 * Vuetify application asset bundle.
 * Class AppAsset
 * @package app\assets
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        (YII_ENV_DEV) ? 'css/style.css' : 'css/style.min.css'
    ];
    public $js = [];
    public $depends = [];

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
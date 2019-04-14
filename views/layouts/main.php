<?php
/* @var $this \yii\web\View */
/* @var $content string */
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>

    <div class="wrap" id="app">
        <div style="width: 1170px; margin-right: auto; margin-left: auto;">
            <nav id="w0" class="navbar-inverse navbar-fixed-top navbar">
                <div class="container">
                    <div class="navbar-header">
                        <router-link to="/" class="navbar-brand">My Application</router-link>
                    </div>

                    <div id="w0-collapse" class="collapse navbar-collapse">
                        <ul id="w1" class="navbar-nav navbar-right nav">
                            <router-link tag="li" exact to="/" active-class="active">
                                <a>Home</a>
                            </router-link>
                            <router-link tag="li" to="/about" active-class="active">
                                <a>About</a>
                            </router-link>
                            <router-link tag="li" to="/login" active-class="active">
                                <a>Login</a>
                            </router-link>
                            <router-link tag="li" to="/categories" active-class="active">
                                <a>Categories</a>
                            </router-link>
                            <li>
                                <a href="<?= \yii\helpers\Url::to(['/user/login']) ?>">
                                    Yii2 Login
                                </a>
                            </li>
                            <li>
                                <a href="<?= \yii\helpers\Url::to(['/parser/citilink/index']) ?>">
                                    Ситилинк
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <transition>
            <router-view></router-view>
        </transition>
        <div v-if="this.$route.matched.length == 0">
            <div class="container" style="padding-top: 50px;">
                <?= $content ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

            <p class="pull-right">Powered by <a href="http://www.yiiframework.com/" rel="external">Yii Framework</a></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
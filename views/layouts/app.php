<?php

use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div id="app">
    <v-app :dark="dark">
        <v-navigation-drawer
            v-model="primaryDrawer.model"
            :clipped="primaryDrawer.clipped"
            absolute
            overflow
            app
        >
            <app-aside></app-aside>
        </v-navigation-drawer>
        <v-toolbar :clipped-left="primaryDrawer.clipped" app absolute>
            <v-toolbar-side-icon @click.stop="primaryDrawer.model = !primaryDrawer.model"></v-toolbar-side-icon>
            <v-toolbar-title>Vuetify</v-toolbar-title>
        </v-toolbar>
        <v-content>
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-content>
        <v-footer app><app-footer></app-footer></v-footer>
    </v-app>
</div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
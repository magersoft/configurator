<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class MakeController extends Controller
{
    public $path;

    public $vue;

    public function options($actionID)
    {
        return ['path', 'vue'];
    }

    public function optionAliases()
    {
        return ['p' => 'path', 'v' => 'vue'];
    }

    /**
     * This command creates an empty vue.js template within the provided path.
     * @param string $name the name for template.
     * @return int Exit code
     */
    public function actionTemplate()
    {
        if ($this->path) {
            $path = Yii::getAlias('@app').'/'.$this->path;
        } else {
            $path = Yii::getAlias('@app').'/app/pages/site/'.$this->vue;
        }
        if (@file_put_contents($path, $this->basicVueTemplate()) === false) {
            echo "Unable to write the file '{$path}'.";
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    private function basicVueTemplate()
    {
        return "<template>
    <div>

    </div>
</template>

<style lang='scss'>

</style>

<script>
    export default {
        data() {
            return {
            }
        },
        mounted: function () {

        },
        methods: {

        },
        watch: {

        }
    }
</script>";
    }

    public function actionComponent()
    {
        $path = Yii::getAlias('@app').'/app/components/'.$this->path;

        if (@file_put_contents($path, $this->basicVueComponent()) === false) {
            echo "Unable to write the file '{$path}'.";
            return ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }

    private function basicVueComponent()
    {
        return "<template>
  <div></div>
</template>

<style lang='scss'>

</style>

<script>
export default {
  data () {
    return {
      
    }
  }
}
</script>
        ";
    }
}

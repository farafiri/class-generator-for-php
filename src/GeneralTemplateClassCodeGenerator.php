<?php
/**
 * Created by PhpStorm.
 * User: Rafał
 * Date: 24.05.15
 * Time: 11:57
 */

namespace ClassGenerator;

class GeneralTemplateClassCodeGenerator {
    public function generate($newClass, $template, $extraData)
    {
        extract($extraData);
        $generatorNamespace = __NAMESPACE__;

        $template = preg_replace_callback('/\{\{([a-zA-Z_0-9]+)\}\}/', function ($a) {
            return '<?php echo $' . $a[1] . '; ?>';
        }, $template);

        $template = preg_replace_callback('/\{\{((.|\n)+?)\}\}/', function ($a) {
            return '<?php echo ' . $a[1] . '; ?>';
        }, $template);

        ob_start();
        eval('?>' . $template );
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
} 
<?php

namespace CRUDDemo;

use OLOG\CRUD\Sanitize;

class BT
{
    /**
     * @param $url
     * @param $text
     * @return string
     */
    static public function a($url, $text){
        return '<a href="' . Sanitize::sanitizeUrl($url). '">' . Sanitize::sanitizeTagContent($text) . '</a>';
    }

    /**
     * @param $arr Array of sanitized html
     * @return mixed
     */
    static public function breadcrumbs($arr){
        ob_start();

        echo '<ol class="breadcrumb">';

        foreach ($arr as $html){
            echo '<li class="active">' . $html . '</li>';
        }

        echo '</ol>';

        return ob_get_clean();
    }
    
}
<?php

namespace OLOG\CRUD;

class Sanitize
{
    static public function sanitizeTagContent($value){
        $value = htmlspecialchars($value);
        //$value = preg_replace('@\R@mu', '<br>', $value); // использовать white-space: pre-wrap для вывода строк с переносами внутри
        return $value;
    }

    static public function sanitizeUrl($url){
        //return filter_var(FILTER_SANITIZE_URL, $url);
        return $url; // TODO: sanitize
    }

    static public function sanitizeAttrValue($value){
        return htmlspecialchars($value);
    }
}
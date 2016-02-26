<?php

namespace OLOG\CRUD;

class EditorContext
{
    use \OLOG\Model\ProtectProperties;

    public $bubble_key = '';
    public $object_id = '';
    public $tab_key = '';

    public function __construct($bubble_key, $object_id, $tab_key){
        $this->bubble_key = $bubble_key;
        $this->object_id = $object_id;
        $this->tab_key = $tab_key;
    }
}
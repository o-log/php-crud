<?php

namespace OLOG\CRUD;

interface InterfaceCRUDFormRow
{
    public function __construct($title, InterfaceCRUDFormWidget $widget_obj);
    public function html($obj);
    
}
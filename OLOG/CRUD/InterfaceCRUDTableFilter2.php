<?php

namespace OLOG\CRUD;

interface InterfaceCRUDTableFilter2
{
    public function getTitle();
    public function sqlConditionAndPlaceholderValue();
    public function getHtml();
}
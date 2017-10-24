<?php

namespace OLOG\CRUD;

interface TFInterface
{
    public function getTitle();
    public function sqlConditionAndPlaceholderValue();
    public function getHtml();
}
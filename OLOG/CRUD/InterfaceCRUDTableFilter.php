<?php

namespace OLOG\CRUD;

interface InterfaceCRUDTableFilter
{
    public function getFieldName();
    public function getOperationCode();
    public function getValue();
    public function setValue($value);
}
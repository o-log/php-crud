<?php

namespace OLOG\CRUD;


use OLOG\HTML;

class CRUDTableWidgetReferenceSelect implements InterfaceCRUDTableWidget
{
    protected $title_field_name;
    protected $id_field_name;

    public function __construct($title_field_name, $id_field_name = '')
    {
        $this->setTitleFieldName($title_field_name);
        $this->setIdFieldName($id_field_name);
    }

    public function html($obj)
    {
        assert($obj);

        $title_field_name = $this->getTitleFieldName();

        $obj_title = CRUDFieldsAccess::getObjectFieldValue($obj, $title_field_name);

        $id_field_name = $this->getIdFieldName();
        if($id_field_name == '') {
            $id = CRUDFieldsAccess::getObjId($obj);
        } else {
            $id = CRUDFieldsAccess::getObjectFieldValue($obj, $id_field_name);
        }

        $o = '';
        $o .= '<button class="btn btn-sm btn-secondary js-ajax-form-select" type="submit" data-id="' . HTML::attr($id) . '" data-title="' . HTML::attr($obj_title) . '">Выбор</button>';

        return $o;
    }

    /**
     * @return mixed
     */
    public function getTitleFieldName()
    {
        return $this->title_field_name;
    }

    /**
     * @param mixed $title_field_name
     */
    public function setTitleFieldName($title_field_name)
    {
        $this->title_field_name = $title_field_name;
    }

    /**
     * @return mixed
     */
    public function getIdFieldName()
    {
        return $this->id_field_name;
    }

    /**
     * @param mixed $id_field_name
     */
    public function setIdFieldName($id_field_name)
    {
        $this->id_field_name = $id_field_name;
    }
}
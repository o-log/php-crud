<?php

namespace CRUDDemo;

class DemoNode implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave,
    \OLOG\Model\InterfaceDelete
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = \CRUDDemo\Config::DB_NAME_PHPCRUDDEMO;
    const DB_TABLE_NAME = 'node';

    protected $created_at_ts = 0;
    protected $is_published = 0;
    protected $published_at_datetime_str = "0001-01-01";
    protected $expiration_date;
    protected $image_path_in_images;
    protected $body2;
    protected $id;

    public function getBody2(){
        return $this->body2;
    }

    public function setBody2($value){
        $this->body2 = $value;
    }


    public function getImagePathInImages(){
        return $this->image_path_in_images;
    }

    public function setImagePathInImages($value){
        $this->image_path_in_images = $value;
    }


    public function getReverseTitle(){
        $title = $this->getTitle();
        return strrev($title);
    }

    public function getExpirationDate(){
        return $this->expiration_date;
    }

    public function setExpirationDate($value){
        $this->expiration_date = $value;
    }


    public function getPublishedAtDatetimeStr(){
        return $this->published_at_datetime_str;
    }

    public function setPublishedAtDatetimeStr($value){
        $this->published_at_datetime_str = $value;
    }

    protected $state_code = 0;
    protected $body = '';
    protected $title = '';


    public function getIsPublished(){
        return $this->is_published;
    }

    public function setIsPublished($value){
        $this->is_published = $value;
    }


    public function getCreatedAtTs(){
        return $this->getcreated_at_ts;
    }

    public function setCreatedAtTs($value){
        $this->created_at_ts = $value;
    }

    /**
     * @return int
     */
    public function getStateCode()
    {
        return $this->state_code;
    }

    /**
     * @param int $state_code
     */
    public function setStateCode($state_code)
    {
        $this->state_code = $state_code;
    }

    /**
     * @return int
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param int $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
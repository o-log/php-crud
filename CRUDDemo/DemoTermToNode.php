<?php

namespace CRUDDemo;

class DemoTermToNode implements
    \OLOG\Model\ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'phpcrud';
    const DB_TABLE_NAME = 'termtonode';

    protected $id;
    protected $node_id;
    protected $term_id;

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
     * @return mixed
     */
    public function getNodeId()
    {
        return $this->node_id;
    }

    /**
     * @param mixed $node_id
     */
    public function setNodeId($node_id)
    {
        $this->node_id = $node_id;
    }

    /**
     * @return mixed
     */
    public function getTermId()
    {
        return $this->term_id;
    }

    /**
     * @param mixed $term_id
     */
    public function setTermId($term_id)
    {
        $this->term_id = $term_id;
    }



}
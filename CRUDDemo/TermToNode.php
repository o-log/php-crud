<?php

/*

create table termtonode (
id int not null auto_increment primary key,
node_id int not null,
term_id int not null,
foreign key(node_id) references node(id),
foreign key(term_id) references term(id)
)
engine InnoDB
default charset utf8;

alter table termtonode add unique key (term_id, node_id);

 */

namespace CRUDDemo;

class TermToNode implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

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
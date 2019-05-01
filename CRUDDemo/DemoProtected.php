<?php
declare(strict_types=1);

namespace CRUDDemo;

use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ActiveRecordTrait;

class DemoProtected implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;

    const DB_ID = 'phpcrud';
    const DB_TABLE_NAME = 'cruddemo_demoprotected';

    const _CREATED_AT_TS = 'created_at_ts';
    protected $created_at_ts;
    const _INT_VAL_NULLABLE = 'int_val_nullable';
    protected $int_val_nullable;
    const _STRING_VAL_NOTNULL = 'string_val_notnull';
    protected $string_val_notnull;
    const _ID = 'id';
    protected $id;

    public function getStringValNotnull(): string
    {
        return $this->string_val_notnull;
    }

    public function setStringValNotnull(string $value): DemoProtected
    {
        $this->string_val_notnull = $value;
        return $this;
    }



    public function getIntValNullable(): ?int
    {
        return $this->int_val_nullable;
    }

    public function setIntValNullable(?int $value): DemoProtected
    {
        $this->int_val_nullable = $value;
        return $this;
    }


    
    public function __construct(){
        $this->created_at_ts = time();
    }
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAtTs(): int
    {
        return $this->created_at_ts;
    }
}
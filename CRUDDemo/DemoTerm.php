<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\Model\WeightInterface;
use OLOG\Model\WeightTrait;

class DemoTerm implements
    \OLOG\Model\ActiveRecordInterface,
    WeightInterface
{
    use \OLOG\Model\ActiveRecordTrait;
    use WeightTrait;

    const DB_ID         = 'phpcrud';
    const DB_TABLE_NAME = 'term';

    const VOCABULARY_MAIN = 1;
    const VOCABULARY_TAGS = 2;
    const VOCABULARY_PEOPLE = 3;

    const VOCABULARIES_ARR = [
        self::VOCABULARY_MAIN => 'main',
        self::VOCABULARY_TAGS => 'tags',
        self::VOCABULARY_PEOPLE => 'people'
    ];

	const GENDER_MALE = 1;
	const GENDER_FEMALE = 2;

	const GENDER_ARR = [
		self::GENDER_MALE => 'male',
		self::GENDER_FEMALE => 'female'
	];

    const _TITLE = 'title';
    public $title = '';
    public $gender  = null;
    const _PARENT_ID = 'parent_id';
    public $parent_id;

    protected $chooser = null;
    protected $options = null;
    protected $vocabulary_id = 1;
    protected $weight = 0;
    const _ID = 'id';
    protected $id;

    public function parent(): ?DemoTerm {
        return DemoTerm::factory($this->parent_id, false);
    }

    public function beforeSave(): void{
        $this->initWeight(
            ['parent_id' => $this->getParentId()]
        );
    }

    public function getWeight(){
        return $this->weight;
    }

    public function setWeight($value){
        $this->weight = $value;
    }

    static public function getIdsArrForVocabularyIdByCreatedAtDesc($value, $offset = 0, $page_size = 30){
        if (is_null($value)) {
            return \OLOG\DB\DB::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where vocabulary_id is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            return \OLOG\DB\DB::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where vocabulary_id = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($value)
            );
        }
    }


    public function getVocabularyId(){
        return $this->vocabulary_id;
    }

    public function setVocabularyId($value){
        $this->vocabulary_id = $value;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($value)
    {
        $this->options = $value;
    }

    public function getChooser()
    {
        return $this->chooser;
    }

    public function setChooser($value)
    {
        $this->chooser = $value;
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

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }
}

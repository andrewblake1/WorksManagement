<?php

/**
 * This is the model class for table "tbl_drawing".
 *
 * The followings are the available columns in table 'tbl_drawing':
 * @property integer $id
 * @property integer $standard_id
 * @property string $description
 * @property string $alias
 * @property integer $deleted
 * @property integer $updated_by
 *
 * The followings are the available model relations:
 * @property AssemblyToDrawing[] $assemblyToDrawings
 * @property User $updatedBy
 * @property Standard $standard
 * @property DrawingAdjacencyList[] $drawingAdjacencyLists
 * @property DrawingAdjacencyList[] $drawingAdjacencyLists1
 */
class Drawing extends AdjacencyListActiveRecord
{
	public $parent_id;
	public $parent;		// the missing relation
	
	/**
	 * @var string nice model name for use in output
	 */
	static $niceName = 'Drawing';

	protected $defaultSort = array('t.id');

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array_merge(parent::rules(), array(
			array('standard_id, description', 'required'),
			array('parent_id, standard_id', 'numerical', 'integerOnly'=>true),
			array('description alias', 'length', 'max'=>255),
//			array('file', 'file', 'types'=>'jpg, gif, png, pdf', 'allowEmpty' => true),
//			array('id, standard_id, parent_id, description, alias', 'safe', 'on'=>'search'),
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assemblyToDrawings' => array(self::HAS_MANY, 'AssemblyToDrawing', 'drawing_id'),
            'updatedBy' => array(self::BELONGS_TO, 'User', 'updated_by'),
            'standard' => array(self::BELONGS_TO, 'Standard', 'standard_id'),
            'drawingAdjacencyLists' => array(self::HAS_MANY, 'DrawingAdjacencyList', 'parent_id'),
            'drawingAdjacencyLists1' => array(self::HAS_MANY, 'DrawingAdjacencyList', 'child_id'),
        );
    }


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'standard_id' => 'Standard',
		));
	}

	/**
	 * @return DbCriteria the search/filter conditions.
	 */
	public function getSearchCriteria()
	{
		$criteria=new DbCriteria;

		// select
		$criteria->select=array(
			't.id',	// needed for delete and update buttons
			't.description',
			'drawingAdjacencyLists1.parent_id AS parent_id',
			't.alias',
		);

		// where
		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.description', $this->description, true);
		$criteria->compare('t.alias', $this->alias,true);
		$criteria->compare('t.standard_id', $this->standard_id);
		if(!empty($this->parent_id))
		{
			$criteria->compare('drawingAdjacencyLists1.parent_id',$this->parent_id);
		}

		// NB: without this the has_many relations aren't returned and some select columns don't exist
		$criteria->together = true;

		// with
		$criteria->with = array(
			'drawingAdjacencyLists1',
		);
		
		return $criteria;
	}

	public static function getDisplayAttr()
	{
		return array(
			'id',
			'description',
			'alias',
		);
	}
 
// TODO: probably breaking mvc here again calling controller code	
	public function linkColumnAdjacencyList($name, &$columns, $primaryKeyName = 'id', $parentAttrib = 'parent_id')
	{
		$modelName = str_replace('View', '', get_class($this));
		$controllerName = "{$modelName}Controller";

		// add addtional columns for managment of the adjacency list if user has write access
		if($controllerName::checkAccess(Controller::accessWrite))
		{
			if(!is_array($columns) || !in_array($primaryKeyName, $columns))
			{
				$columns[] = $primaryKeyName;
			}
			if(!in_array($parentAttrib, $columns))
			{
				$columns[] = $parentAttrib;
			}
		}

		// if the user has at least read access
		if($controllerName::checkAccess(Controller::accessRead))
		{
			// NB: want id intead of $this->tableSchema->primaryKey because yii wants a variable by the same as in the function signature
			// even though this confusing here
			// create a link
			$params = var_export(Controller::getAdminParams($modelName), true);
			$columns[] = array(
				'name'=>$name,
				'value'=> 'DrawingAdjacencyList::model()->findByAttributes(array("'.$parentAttrib.'" => $data->'.$primaryKeyName.')) !== null
					? CHtml::link($data->'.$name.', Yii::app()->createUrl("'."$modelName/admin".'", array("'.$parentAttrib.'"=>$data->'.$primaryKeyName.') + '.$params.'))
					: $data->'.$name,
				'type'=>'raw',
			);
		}
		else
		{
			// create text
			$columns[] = $name;
		}
	}
	public function getAdminColumns()
	{
 		// link to admin displaying children or if no children then just description without link
        $this->linkColumnAdjacencyList('description', $columns);
		$columns[] = 'alias';
		
		return $columns;
	}

	public function scopeStandard($standard_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('standard_id', $standard_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
	public function afterFind() {
		if($this->drawingAdjacencyLists1)
		{
			$this->parent = $this->drawingAdjacencyLists1[0]->parent;
			$this->parent_id = $this->parent->parent_id;
		}
		
		parent::afterFind();
	}
	
	public function insert($attributes = null) {
		
		$return = parent::insert($attributes);

		// if parent_id is not null
		if($this->parent_id !== NULL)
		{
			$drawingAdjacencyList = new DrawingAdjacencyList();
			$drawingAdjacencyList->updated_by = $this->updated_by;
			$drawingAdjacencyList->child_id = $this->id;
			$drawingAdjacencyList->parent_id = $this->parent_id;
			$drawingAdjacencyList->insert();
		}
		
		return $return;
	}
	
	public function update($attributes = null) {

		// if there is currently a parent - if find record in adjacency list
		if($drawingAdjacencyList = DrawingAdjacencyList::model()->findByAttributes(array('child_id' => $this->id)))
		{
			// if removing parent
			if($this->parent_id === NULL)
			{
				// remove
				$this->delete();
			}
			else
			{
				$drawingAdjacencyList->updated_by = $this->updated_by;
				$drawingAdjacencyList->parent_id = $this->parent_id;
				$drawingAdjacencyList->save();
			}
		}
		// otherwise no parent currently - if adding parent
		elseif($this->parent_id)
		{
			$drawingAdjacencyList = new DrawingAdjacencyList();
			$drawingAdjacencyList->child_id = $this->id;
			$drawingAdjacencyList->parent_id = $this->parent_id;
			$drawingAdjacencyList->updated_by = $this->updated_by;
			$drawingAdjacencyList->insert();
		}
		
		return parent::update($attributes);
	}

}

?>
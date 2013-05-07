<?php

/**
 * This is the model class for table "standard_drawing".
 *
 * The followings are the available columns in table 'standard_drawing':
 * @property integer $id
 * @property integer $store_id
 * @property string $description
 * @property string $alias
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property AssemblyToStandardDrawing[] $assemblyToStandardDrawings
 * @property Staff $staff
 * @property Store $store
 * @property StandardDrawingAdjacencyList[] $standardDrawingAdjacencyLists
 * @property StandardDrawingAdjacencyList[] $standardDrawingAdjacencyLists1
 * @property StandardDrawing $parent
*/
class StandardDrawing extends AdjacencyListActiveRecord
{
	public $parent_id;
	
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
		return array(
			array('store_id, description', 'required'),
			array('parent_id, store_id', 'numerical', 'integerOnly'=>true),
			array('description alias', 'length', 'max'=>255),
//			array('file', 'file', 'types'=>'jpg, gif, png, pdf', 'allowEmpty' => true),
			array('id, store_id, parent_id, description, alias', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'assemblyToStandardDrawings' => array(self::HAS_MANY, 'AssemblyToStandardDrawing', 'standard_drawing_id'),
            'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
            'store' => array(self::BELONGS_TO, 'Store', 'store_id'),
            'standardDrawingAdjacencyLists' => array(self::HAS_MANY, 'StandardDrawingAdjacencyList', 'parent_id'),
            'standardDrawingAdjacencyLists1' => array(self::HAS_MANY, 'StandardDrawingAdjacencyList', 'child_id'),
			'parent' => array(self::BELONGS_TO, 'StandardDrawing', 'parent_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels(array(
			'store_id' => 'Store',
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
			'standardDrawingAdjacencyLists1.parent_id AS parent_id',
			't.alias',
		);

		// where
		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.description', $this->description, true);
		$criteria->compare('t.alias', $this->alias,true);
		$criteria->compare('t.store_id', $this->store_id);
		if(!empty($this->parent_id))
		{
			$criteria->compare('standardDrawingAdjacencyLists1.parent_id',$this->parent_id);
		}

//		// join
//		$criteria->join = '
//			LEFT JOIN standard_drawing_adjacency_list standardDrawingAdjacencyLists1 ON t.id = standardDrawingAdjacencyLists1.parent_id
//		';

		// NB: without this the has_many relations aren't returned and some select columns don't exist
		$criteria->together = true;

		// with
		$criteria->with = array(
			'standardDrawingAdjacencyLists1',
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
				'value'=> 'StandardDrawingAdjacencyList::model()->findByAttributes(array("'.$parentAttrib.'" => $data->'.$primaryKeyName.')) !== null
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

	/**
	 * Retrieves a sort array for use in CActiveDataProvider.
	 * @return array the for data provider that contains the sort condition.
	 */
	public function getSearchSort()
	{	
		return array(
			'parent_id',
		);
	}
	
	public function scopeStore($store_id)
	{
		$criteria=new DbCriteria;
		$criteria->compareNull('store_id', $store_id);

		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
	public function afterFind() {
		if($this->standardDrawingAdjacencyLists1)
		{
			$this->parent_id = $this->standardDrawingAdjacencyLists1[0]->parent_id;
		}
		
		parent::afterFind();
	}
	
	public function insert($attributes = null) {
		
		$return = parent::insert($attributes);

		// if parent_id is not null
		if($this->parent_id !== NULL)
		{
			$standardDrawingAdjacencyList = new StandardDrawingAdjacencyList();
			$standardDrawingAdjacencyList->staff_id = $this->staff_id;
			$standardDrawingAdjacencyList->child_id = $this->id;
			$standardDrawingAdjacencyList->parent_id = $this->parent_id;
			$standardDrawingAdjacencyList->insert();
		}
		
		return $return;
	}
	
	public function update($attributes = null) {

		// if there is currently a parent - if find record in adjacency list
		if($standardDrawingAdjacencyList = StandardDrawingAdjacencyList::model()->findByAttributes(array('child_id' => $this->id)))
		{
			// if removing parent
			if($this->parent_id === NULL)
			{
				// remove
				$this->delete();
			}
			else
			{
				$standardDrawingAdjacencyList->staff_id = $this->staff_id;
				$standardDrawingAdjacencyList->parent_id = $this->parent_id;
				$standardDrawingAdjacencyList->save();
			}
		}
		// otherwise no parent currently - if adding parent
		elseif($this->parent_id)
		{
			$standardDrawingAdjacencyList = new StandardDrawingAdjacencyList();
			$standardDrawingAdjacencyList->child_id = $this->id;
			$standardDrawingAdjacencyList->parent_id = $this->parent_id;
			$standardDrawingAdjacencyList->staff_id = $this->staff_id;
			$standardDrawingAdjacencyList->insert();
		}
		
		return parent::update($attributes);
	}
}

?>
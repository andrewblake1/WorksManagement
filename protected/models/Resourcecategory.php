<?php

/**
 * This is the Nested Set  model class for table "resourcecategory".
 *
 * The followings are the available columns in table 'resourcecategory':
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $dutycategory_id
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 * @property ResourceType[] $resourceTypes
 * @property Dutycategory $dutycategory
 * @property Staff $staff
 */
class Resourcecategory extends ActiveRecord {
	/**
	 * Id of the div in which the tree will berendered.
	 */

	const ADMIN_TREE_CONTAINER_ID = 'resourcecategory_admin_tree';

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'resourcecategory';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE1: you should only define rules for those attributes that
		// will receive user inputs.
		// NOTE2: Remove ALL rules associated with the nested Behavior:
		//rgt,lft,root,level,id.
		return array(
			array('name, staff_id', 'required'),
			array('dutycategory_id', 'numerical', 'integerOnly' => true),
			array('name', 'length', 'max' => 64),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'resourceTypes' => array(self::HAS_MANY, 'ResourceType', 'resourcecategory_id'),
			'dutycategory' => array(self::BELONGS_TO, 'Dutycategory', 'dutycategory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'name' => 'Name',
			'dutycategory_id' => 'Dutycategory',
			'deleted' => 'Deleted',
			'staff_id' => 'Staff',
		);
	}

	public function behaviors() {
		return array(
			'NestedSetBehavior' => array(
				'class' => 'ext.nestedBehavior.NestedSetBehavior',
				'leftAttribute' => 'lft',
				'rightAttribute' => 'rgt',
				'levelAttribute' => 'level',
				'hasManyRoots' => true
			)
		);
	}

	public static function printULTree() {
		$categories = Resourcecategory::model()->findAll(array('order' => 'root,lft'));
		$level = 0;

		foreach ($categories as $n => $category) {

			if ($category->level == $level)
				echo CHtml::closeTag('li') . "\n";
			else if ($category->level > $level)
				echo CHtml::openTag('ul') . "\n";
			else {
				echo CHtml::closeTag('li') . "\n";

				for ($i = $level - $category->level; $i; $i--) {
					echo CHtml::closeTag('ul') . "\n";
					echo CHtml::closeTag('li') . "\n";
				}
			}

			echo CHtml::openTag('li', array('id' => 'node_' . $category->id, 'rel' => $category->name));
			echo CHtml::openTag('a', array('href' => '#'));
			echo CHtml::encode($category->name);
			echo CHtml::closeTag('a');

			$level = $category->level;
		}

		for ($i = $level; $i; $i--) {
			echo CHtml::closeTag('li') . "\n";
			echo CHtml::closeTag('ul') . "\n";
		}
	}

	public static function printULTree_noAnchors() {
		$categories = Resourcecategory::model()->findAll(array('order' => 'lft'));
		$level = 0;

		foreach ($categories as $n => $category) {
			if ($category->level == $level)
				echo CHtml::closeTag('li') . "\n";
			else if ($category->level > $level)
				echo CHtml::openTag('ul') . "\n";
			else {   //if $category->level<$level
				echo CHtml::closeTag('li') . "\n";

				for ($i = $level - $category->level; $i; $i--) {
					echo CHtml::closeTag('ul') . "\n";
					echo CHtml::closeTag('li') . "\n";
				}
			}

			echo CHtml::openTag('li');
			echo CHtml::encode($category->name);
			$level = $category->level;
		}

		for ($i = $level; $i; $i--) {
			echo CHtml::closeTag('li') . "\n";
			echo CHtml::closeTag('ul') . "\n";
		}
	}

}
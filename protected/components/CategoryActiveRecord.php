<?php

/**
 * This is the  abstract Nested Set  model class for category tables
 *
 * The followings are the available columns in category tables
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $name
 * @property integer $deleted
 * @property integer $staff_id
 *
 * The followings are the available model relations:
 */
abstract class CategoryActiveRecord extends ActiveRecord {
	/**
	 * Id of the div in which the tree will berendered.
	 */

	const ADMIN_TREE_CONTAINER_ID = 'category_admin_tree';

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
			array('name', 'length', 'max' => 64),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels($attributeLabels = array()) {
		return parent::attributeLabels(array(
			'id' => 'ID',
			'root' => 'Root',
			'lft' => 'Lft',
			'rgt' => 'Rgt',
			'level' => 'Level',
			'name' => 'Name',
			'deleted' => 'Deleted',
			'staff_id' => 'Staff',
		) + $attributeLabels);
	}

	public function behaviors() {
		return parent::behaviors() + array(
			'NestedSetBehavior' => array(
				'class' => 'ext.nestedBehavior.NestedSetBehavior',
				'leftAttribute' => 'lft',
				'rightAttribute' => 'rgt',
				'levelAttribute' => 'level',
				'hasManyRoots' => true
			)
		);
	}

	public static function printULTree($parent_id = null) {
		
		if($parent_id !== null)
		{
			$model = static::model()->findByPk($parent_id);
			$categories = $model->descendants()->findAll();
			$level = 1;
		}
		else
		{
			$categories = static::model()->findAll(array('order' => 'root,lft'));
			$level = 0;
		}

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
		$categories = static::model()->findAll(array('order' => 'lft'));
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
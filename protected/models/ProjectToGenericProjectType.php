<?php

Yii::import('application.models._base.BaseProjectToGenericProjectType');

class ProjectToGenericProjectType extends BaseProjectToGenericProjectType
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}
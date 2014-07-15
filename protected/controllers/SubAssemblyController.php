<?php

class SubAssemblyController extends Controller
{
	// called within AdminViewWidget
	public function getButtons($model)
	{
		return array(
			'class' => 'WMTbButtonColumn',
			'buttons' => array(
				'delete' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => 'Yii::app()->createUrl("' . $this->modelName . '/delete", array("' . $model->tableSchema->primaryKey . '"=>$data->primaryKey))',
				),
				'update' => array(
					'visible' => 'Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))',
					'url' => "\$this->controller->createUrl('update', array('id'=>\$data->id) +
						array('sub_assembly_ids'=>array_merge(empty(\$_GET['sub_assembly_ids']) ? array() : \$_GET['sub_assembly_ids'], array(\$data->id))
					))",
				),
				'view' => array(
					'visible' => '
						!Yii::app()->user->checkAccess(str_replace("View", "", get_class($data)), array("primaryKey"=>$data->primaryKey))
						&& Yii::app()->user->checkAccess(get_class($data)."Read")',
					'url' => "\$this->controller->createUrl('view', array('id'=>\$data->id) +
						array('sub_assembly_ids'=>array_merge(empty(\$_GET['sub_assembly_ids']) ? array() : \$_GET['sub_assembly_ids'], array(\$data->id))
					))",
				),
			),
		);
	}

	public function setChildTabs($model)
	{
		$models = array();

		foreach($_GET['sub_assembly_ids'] as $subAssemblyId)
		{
			$models[] = SubAssembly::model()->findByPk($subAssemblyId);
		}

		$size = sizeof($models);
		$cntr = 0;
		foreach($models as $model)
		{
			$cntr++;
			if($tabs = $this->getChildTabs($model, $cntr == $size))
			{
				static::$tabs[] = $tabs;
			}
		}

		return static::$tabs;
	}
	public function getChildTabs($model, $last = FALSE)
	{
		$tabs = array();
		
		// need to truncate the array of dependency on per tab level basis
		$subAssemblyIds = array_slice($_GET['sub_assembly_ids'], 0, 1 + array_search($model->id, $_GET['sub_assembly_ids']));
		
		// add tab to  update SubAssembly
		$this->addTab(
			SubAssembly::getNiceName(NULL, $model),
			'SubAssembly',
			'update',
			array('id' => $model->id, 'sub_assembly_ids'=>$subAssemblyIds),
			$tabs,
			TRUE
		);
		
		// add tab to sub assemblies
		$this->addTab(
			SubAssembly::getNiceNamePlural(),
			'SubAssembly',
			'admin',
			array('parent_assembly_id'=>$model->child_assembly_id, 'sub_assembly_ids'=>$subAssemblyIds),
			$tabs
		);

		$this->addTab(
			AssemblyToAssemblyGroup::getNiceNamePlural(),
			'AssemblyToAssemblyGroup',
			'admin',
			array('assembly_id'=>$model->child_assembly_id, 'sub_assembly_ids'=>$subAssemblyIds),
			$tabs
		);

		// add tab to assembly groups
		$this->addTab(
			AssemblyToMaterial::getNiceNamePlural(),
			'AssemblyToMaterial',
			'admin',
			array('assembly_id'=>$model->child_assembly_id, 'sub_assembly_ids'=>$subAssemblyIds),
			$tabs
		);

		// add tab to assembly groups
		$this->addTab(
			AssemblyToMaterialGroup::getNiceNamePlural(),
			'AssemblyToMaterialGroup',
			'admin',
			array('assembly_id'=>$model->child_assembly_id, 'sub_assembly_ids'=>$subAssemblyIds),
			$tabs
		);

		return $tabs;
	}

	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model = NULL, &$tabs = NULL) {
		if($model)
		{
			parent::setTabs(NULL);
			$this->setChildTabs($this->loadModel(static::getUpdateId()));
			$this->setActiveTabs(SubAssembly::getNiceNamePlural(), FALSE, SubAssembly::getNiceNamePlural());
		}
		else
		{
			// if in a sub assembly
			if(isset($_GET['sub_assembly_ids']))
			{
				$subAssembly = SubAssembly::model()->findByPk(current($_GET['sub_assembly_ids']));
				static::setUpdateId($subAssembly->parent_assembly_id, 'Assembly');
				parent::setTabs($model);
				$this->setChildTabs(NULL);
				$this->setActiveTabs(SubAssembly::getNiceNamePlural(), SubAssembly::getNiceNamePlural(), SubAssembly::getNiceNamePlural());
			}
			else
			{
				parent::setTabs($model);
			}
		}

		// set breadcrumbs
		$this->breadcrumbs = self::getBreadCrumbTrail();
	}
	
	protected function currentTabLevel()
	{
		return sizeof(static::$tabs);
	}
	
	protected function restoreAdminSettings(&$modelName, &$container = NULL)
	{
		parent::restoreAdminSettings($modelName, $_SESSION['admin'][$modelName][$this->currentTabLevel()]);
	}	
	
	protected function storeAdminSettings(&$modelName, &$container = NULL)
	{
		$tabLevel = $this->currentTabLevel();
		
		if(!isset($_SESSION['admin'][$modelName][$tabLevel]))
		{
			$_SESSION['admin'][$modelName][$tabLevel] = array();
		}

		parent::storeAdminSettings($modelName, $_SESSION['admin'][$modelName][$tabLevel]);
	}
	
	
	
	
	
}

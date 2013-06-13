<?php

class DrawingController extends AdjacencyListController
{
	private $uploadHandler;

	private function initUploadHandler($id)
	{
		// ensure all correct directories exist
		$uploadDir = Yii::app()->params['privateUploadPath'] . "drawing/$id/";
		$session_id = session_id();
		exec("mkdir " . Yii::app()->params['privateUploadPath'] . "drawing");
		exec("mkdir " . Yii::app()->params['publicUploadPath'] . "drawing");
		exec("mkdir $uploadDir");
		exec("mkdir {$uploadDir}thumbnails/");
		$this->expose($id);

		Yii::import("xupload.UploadHandler");
		$this->uploadHandler = new UploadHandler(array(
			'upload_dir' => $uploadDir,
			'upload_url' => Yii::app()->params['webUploadPath'] . "drawing/$session_id$id/",
			'script_url' => $this->createUrl('upload', array('id'=>$id)),
			'delete_type' => 'POST',
			'image_versions'=>array('thumbnail'=>array(
				'upload_dir' => "$uploadDir/thumbnails/",
				'upload_url' => Yii::app()->params['webUploadPath'] . "drawing/$session_id$id/thumbnails/",
				'max_width' => '80px',
				'max_height' => '80px'
			))
 		));
	}

	public function accessRules()
	{
		$accessRules = parent::accessRules();
			array_unshift($accessRules,
				array('allow',
					'actions' => array('upload'),
					'roles' => array($this->modelName),
				),
				array('allow',
					'actions' => array('getExisting'),
					'roles' => array("{$this->modelName}Read"),
				)
			);

		return $accessRules;
	}

	public function actionGetExisting($id)
	{
		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-Disposition: inline; filename="files.json"');
		header('X-Content-Type-Options: nosniff');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');
		$this->initUploadHandler($id);
	}
	
	public function actionUpload()
	{
		if(isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE')
		{
			$this->initUploadHandler($_GET['id']);
        }
		else
		{
			// if creating
			if(empty($_POST['Drawing']['id']))
			{
				$this->create();
			}
			// otherwise updating
			else
			{
				$this->update($_POST['Drawing']['id']);
			}
        }
	}

	/*
	 * using createRedirect to save the file as only called onece after transaction is committed
	 */
	public function create($modal_id = 'myModal') {
		$this->initUploadHandler($_POST['Drawing']['created']);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * This differs from parent in that the submit actually done inside the validate - submit should now never occur as aftervalidate will jquery
	 * will return false after finished processing - allowing jquery file upload's submit to upload files if required.
	 */
	public function actionCreate($modal_id = 'myModal', &$model = null)
	{
		if($model === null)
		{
			$model=new $this->modelName;
		}
		$models=array();

		if(isset($_POST[$this->modelName]))
		{
			// saving now in ajax validation
			// $validating will be set to true if ajax validating and passed so-far but still need to try, catch db errors before actual submit
			if(!$validating =$this->performAjaxValidation($model))
			{
				throw new CHttpException(400,'You must enable javascript to perform this request.');
			}

			$model->attributes=$_POST[$this->modelName];

			// if an error occurrs in file upload after ajax validation has already created the standard drawing record then we really wan't actionUpdate
			if(isset($_POST[$this->modelName]['created']))
			{
				$this->actionUpdate($_POST[$this->modelName]['created']);
			}

			// start a transaction
			$transaction = Yii::app()->db->beginTransaction();

			// attempt save
			$saved = $model->createSave($models);

			// if not validating and successful
			if($saved)
			{
				// commit
                $transaction->commit();
				$result = array('id'=>$model->id);
				echo $t = function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
				Yii::app()->end();
			}
			// otherwise there has been an error which should be captured in model
			else
			{
				// rollback
                $transaction->rollBack();
				// if coming from ajaxvalidate
				if($validating)
				{
					$result=array();
					if(!is_array($models)) 
					{
						$models=array($model);
					}
					foreach($models as $m)
					{
						foreach($m->getErrors() as $attribute=>$errorS)
						{
							$result[CHtml::activeId($m,$attribute)]=$errorS;
						}
					}
					// return the json encoded data to the client
					echo $t = function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
					Yii::app()->end();
				}

				$model->isNewRecord = TRUE;
			}
		}
		elseif(isset($_GET[$this->modelName]))
		{
			// set any url based paramters
			$model->attributes=$_GET[$this->modelName];
		}

		$this->createRender($model, $models, $modal_id);
	}
	
	protected function createRedirect($model)
	{
		if(Yii::app()->controller->action->id != 'upload')
		{
			parent::createRedirect($model);
		}
	}

	protected function updateRedirect($model)
	{
		if(Yii::app()->controller->action->id != 'upload')
		{
			parent::updateRedirect($model);
		}
	}
	
	private function expose($id)
	{
		// create a symlink in below doc root to expose to web
// TODO: currently if user goes back into drawing then could get up by previous at command removing at just the wrong time.		
		$session_id = session_id();
		// local source
		$source = Yii::app()->params['privateUploadPath'] . "drawing/$id/"; 
		// local target
		$target = Yii::app()->params['publicUploadPath'] . "drawing/$session_id$id";
		// create the symlink
		exec("ln -s -f $source $target");
		// set symlink expiry
		$expire = date("H:i" , time() + 120);
		exec("echo 'rm $target' | at $expire");
	}
	
	public function actionUpdate($id, $model = null) {
		$model = $this->loadModel($id, $model);
		$this->expose($id);
		
		parent::actionUpdate($id, $model);
	}

	public function update($id)
	{
		$model=$this->loadModel($id);


		// $validating will be set to true if ajax validating and passed so-far but still need to try, catch db errors before actual submit
		$validating =$this->performAjaxValidation($model);

		$model->attributes=$_POST[$this->modelName];
			
		// start a transaction
		$transaction = Yii::app()->db->beginTransaction();
			
		// attempt save
		$saved = $model->updateSave($models);

		// if not validating and successful
		if(!$validating && $saved)
		{
			$this->initUploadHandler($id);
//			$this->uploadHandler->post();
			// commit
			$transaction->commit();
			$this->updateRedirect($model);
		}
		// otherwise there has been an error which should be captured in model
		else
		{
			// rollback
			$transaction->rollBack();
			// if coming from ajaxvalidate
			if($validating)
			{
				$result=array();
				if(!is_array($models)) 
				{
					$models=array($model);
				}
				foreach($models as $m)
				{
					foreach($m->getErrors() as $attribute=>$errors)
					{
						$result[$m->getHtmlId($attribute)]=$errors;
					}
				}
				// return the json encoded data to the client
				echo function_exists('json_encode') ? json_encode($result) : CJSON::encode($result);
				Yii::app()->end();
			}

			$model->isNewRecord = TRUE;
		}

		// add primary key into session so it can be retrieved for future use in breadcrumbs
		$_SESSION[$this->modelName] = array(
			'name'=>$model->tableSchema->primaryKey,
			'value'=>$id,
		);
	}

	public function actionDelete($id) {
// TODO: potentially not maintaining relational integrity here
		// remove any file that may have been uploaded
		$source = Yii::app()->params['privateUploadPath'] . "drawing/$id";
		// remove it if exists
		exec("rm -rf $source");

		return parent::actionDelete($id);
	}

	public function getChildTabs($model, $last = FALSE)
	{
		$modelName = $this->modelName;
		$tabs = array();
		
		parent::setTabs($model, $tabs);
		
		// dont add empty tabs if no write access
		if(static::checkAccess(self::accessWrite, $modelName) || DrawingAdjacencyList::model()->countByAttributes(array('parent_id' => $model->id)))
		{
			// add tab to drawings
			$this->addTab(Drawing::getNiceNamePlural(), $this->createUrl('Drawing/admin', array(
				'standard_id' => $model->standard_id,
				'parent_id' => $model->id)
			), $tabs[0]);	
		}
	
		return $tabs[0];
	}
	
	// override the tabs when viewing assemblies for a particular task
	public function setTabs($model) {

		// control extra rows of tabs if action is 
		if($model)
		{
			if(isset($_GET['task_to_assembly_id']))
			{
				$taskToAssemblyController= new TaskToAssemblyController(NULL);
				$taskToAssembly = TaskToAssembly::model()->findByPk($_GET['task_to_assembly_id']);
				$taskToAssembly->assertFromParent();
				$taskToAssemblyController->setTabs(NULL);
				static::$tabs = $taskToAssemblyController->tabs;
				static::$tabs[sizeof(static::$tabs) - 1][3]['active'] = TRUE;

				$tabs=array();
				$this->addTab(Drawing::getNiceName($_GET['id']), Yii::app()->request->requestUri, $tabs, TRUE);
				static::$tabs = array_merge(static::$tabs, array($tabs));

				// elimate irrelevant tabs
				$this->breadcrumbs = TaskToAssemblyController::getBreadCrumbTrail();
			}
			else
			{
				parent::setTabs(NULL);
				
				$this->setChildTabs($this->loadModel(static::getUpdateId()));
				$this->setActiveTabs(NULL, NULL, Drawing::getNiceNamePlural());
				$this->breadcrumbs = static::getBreadCrumbTrail();
			}
		}
		else
		{
			// be careful of any get params being appended - i.e. lower down sets parent id which don't want at
			// top level
			if(!empty($_GET['parent_id']))
			{
				$parent_id = $_GET['parent_id'];
				unset($_GET['parent_id']);
				parent::setTabs($model);
				$_GET['parent_id'] = $parent_id;
			}
			else {
				parent::setTabs($model);
			}

			if(isset($_GET['parent_id']))
			{
				$this->setChildTabs($this->loadModel($_GET['parent_id']));
				$this->setActiveTabs(NULL, Drawing::getNiceNamePlural(), Drawing::getNiceNamePlural());
			}

			$this->breadcrumbs = static::getBreadCrumbTrail();
		}
		
//		$this->trimTabs(array(), array(Assembly::getNiceNamePlural()), array(Drawing::getNiceNamePlural()));
	}
	
	public function getRelation($model, $attribute)
	{
		if($attribute == 'parent_id')
		{
			return $model->parent;
		}
		
		return NULL;
	}


}

?>
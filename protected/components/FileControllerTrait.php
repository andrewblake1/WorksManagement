<?php

trait FileControllerTrait {

	private $uploadHandler;

	private function initUploadHandler($id)
	{
		$modelName = $this->modelName;
		$dirName = lcfirst($modelName);
		// ensure all correct directories exist
		$uploadDir = Yii::app()->params['privateUploadPath'] . "$dirName/$id/";
		$session_id = session_id();
		exec("mkdir " . Yii::app()->params['privateUploadPath'] . $dirName);
		exec("mkdir " . Yii::app()->params['publicUploadPath'] . $dirName);
		exec("mkdir $uploadDir");

		Yii::import("xupload.UploadHandler");
		$this->uploadHandler = new UploadHandler(array(
			'upload_dir' => $uploadDir,
			'upload_url' => $modelName::model()->findByPk($id)->expose(),
			'script_url' => $this->createUrl('upload', array('id'=>$id)),
			'delete_type' => 'POST',
			'image_versions'=>array('thumbnail'=>array(
				'upload_url' => Yii::app()->params['webUploadPath'] . "$modelName/$session_id$id/thumbnails/",
				'max_width' => '80px',
				'max_height' => '80px',
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
			if(empty($_POST[$this->modelName]['id']))
			{
				$this->create();
			}
			// otherwise updating
			else
			{
				$this->update($_POST[$this->modelName]['id']);
			}
        }
	}

	/*
	 * using createRedirect to save the file as only called onece after transaction is committed
	 */
	public function create($modal_id = 'myModal') {
		$this->initUploadHandler($_POST[$this->modelName]['created']);
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

			// if an error occurrs in file upload after ajax validation has already created the model record then we really wan't actionUpdate
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
	
	protected function createRedirect($model, $params = array())
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
	
	public function actionUpdate($id, $model = null) {
		$model = $this->loadModel($id, $model);
		$model->expose();
		
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
		$source = Yii::app()->params['privateUploadPath'] . "{$this->modelName}/$id";
		// remove it if exists
		exec("rm -rf $source");

		return parent::actionDelete($id);
	}

}

?>
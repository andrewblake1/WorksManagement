<?php

class StandardDrawingController extends Controller
{
	private $uploadHandler;

	private function initUploadHandler($id)
	{
		// ensure all correct directories exist
		$uploadDir = Yii::app()->params['privateUploadPath'] . "standard_drawing/$id/";
		$sessionId = session_id();
		exec("mkdir " . Yii::app()->params['privateUploadPath'] . "standard_drawing");
		exec("mkdir " . Yii::app()->params['publicUploadPath'] . "standard_drawing");
		exec("mkdir $uploadDir");
		exec("mkdir {$uploadDir}thumbnails/");
		$this->expose($id);

		Yii::import("xupload.UploadHandler");
		$this->uploadHandler = new UploadHandler(array(
			'upload_dir' => $uploadDir,
			'upload_url' => Yii::app()->params['webUploadPath'] . "standard_drawing/$sessionId$id/",
			'script_url' => $this->createUrl('upload', array('id'=>$id)),
			'delete_type' => 'POST',
			'image_versions'=>array('thumbnail'=>array(
				'upload_dir' => "$uploadDir/thumbnails/",
				'upload_url' => Yii::app()->params['webUploadPath'] . "standard_drawing/$sessionId$id/thumbnails/",
				'max_width' => '80px',
				'max_height' => '80px'
			))
 		));
	}

	public function accessRules()
	{
		$accessRules = parent::accessRules();
		array_unshift($accessRules, array('allow',
			'actions' => array('upload', 'getExisting'),
			'roles' => array($this->modelName),
		));

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
//		$this->uploadHandler->get();
	}
	
	public function actionUpload()
	{
		if(isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE')
		{
			$this->initUploadHandler($_GET['id']);
//            $this->uploadHandler->delete();
        }
		else
		{
			// if creating
			if(empty($_POST['StandardDrawing']['id']))
			{
				$this->create();
			}
			// otherwise updating
			else
			{
				$this->update($_POST['StandardDrawing']['id']);
			}
        }
	}

	/*
	 * using createRedirect to save the file as only called onece after transaction is committed
	 */

	public function create($modalId = 'myModal') {
		ob_start();
		parent::actionCreate($modalId, $model = new $this->modelName);
		// if errors
		if($model->hasErrors())
		{
			ob_end_flush();
		}
		else
		{
			ob_end_clean();
			$this->initUploadHandler($model->id);
//			$this->uploadHandler->post();
		}
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
		$sessionId = session_id();
		// local source
		$source = Yii::app()->params['privateUploadPath'] . "standard_drawing/$id/"; 
		// local target
		$target = Yii::app()->params['publicUploadPath'] . "standard_drawing/$sessionId$id";
		// create the symlink
		exec("ln -s -f $source $target");
		// set symlink expiry
		$expire = date("H:i" , time() + 120);
		exec("echo 'rm $target' | at $expire");
	}
	
	public function actionUpdate($id, $model = null) {
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
		$saved = $this->updateSave($model, $models);

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
						$result[$this->actionGetHtmlId($m,$attribute)]=$errors;
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
		$source = Yii::app()->params['privateUploadPath'] . "standard_drawing/$id";
		// remove it if exists
		exec("rm -rf $source");

		return parent::actionDelete($id);
	}

}

?>
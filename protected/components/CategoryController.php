<?php

class CategoryController extends Controller
{
	/**
	 * @var string the name of the admin view
	 */
	protected $_adminView = '/categoryAdmin';

	protected function exportButton()
	{
	}

	//UTILITY FUNCTIONS
	public static  function registerCssAndJs($folder, $jsfile, $cssfile)
	{
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerScriptFile($publishedFolder . $jsfile, CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerCssFile($publishedFolder . $cssfile);
	}

	public static function registerCss($folder, $cssfile)
	{
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerCssFile($publishedFolder .'/'. $cssfile);
		return $publishedFolder .'/'. $cssfile;
	}

	public static function registerJs($folder, $jsfile)
	{
		$sourceFolder = YiiBase::getPathOfAlias($folder);
		$publishedFolder = Yii::app()->assetManager->publish($sourceFolder);
		Yii::app()->clientScript->registerScriptFile($publishedFolder .'/'.  $jsfile);
		return $publishedFolder .'/'. $jsfile;
	}

	/**
	*This constructor added to fix a styling issue with ajax where the cascading order of style sheets being effected as the
	* registered style sheets get re brought back in duing ajax request
	* @param type $id
	* @param type $module 
	*/
	public function __construct($id, $module = null)
	{
		if(!empty($_POST))
		{
			$cs=Yii::app()->clientScript;
			$temp = $cs->scriptMap;
			$cs->reset();
			$cs->scriptMap = $temp;
		}

		parent::__construct($id, $module);
	}

	public function init()
	{
		$this->registerAssets();
		parent::init();
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('admin'),
				'roles'=>array($this->modelName.'Read'),
			),
			array('allow',
				'roles'=>array($this->modelName),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


	private function registerAssets()
	{
		Yii::app()->clientScript->registerCoreScript('jquery');
		$this->registerJs('webroot.js_plugins.jstree','/jquery.jstree.js');
		$this->registerCssAndJs('webroot.js_plugins.fancybox',
			'/jquery.fancybox-1.3.4.js',
			'/jquery.fancybox-1.3.4.css');
// NB: this conlficting with JuiAutocomplete
/*		$this->registerCssAndJs('webroot.js_plugins.jqui1812',
			'/js/jquery-ui-1.8.12.custom.min.js',
			'/css/dark-hive/jquery-ui-1.8.12.custom.css');*/
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_plugins/json2/json2.js');
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/client_val_form.css','screen');
	}

	public function actionFetchTree($parent_id = null)
	{
		$modelName = $this->modelName();
		$modelName::printULTree($parent_id);
	}

	public function actionRename()
	{
		$new_name=$_POST['new_name'];
		$id=$_POST['id'];
		$renamed_cat=$this->loadModel($id);
		$renamed_cat->name= $new_name;
		if ($renamed_cat->saveNode())
		{
			echo json_encode (array('success'=>true));
			exit;
		}
		else
		{
			echo json_encode (array('success'=>false));
			exit;
		}
	}

	public function actionRemove()
	{
		$id=$_POST['id'];
		$deleted_cat=$this->loadModel($id);

		try
		{
			$deleted = $deleted_cat->deleteNode();
		}
		catch (Exception $e)
		{
			// most likely tying to delete parent of child - could check fo the error here or override the delete method
			// in nested set behaviour to use our db_callback function to smooth the error and report back but for now until otherwise
			// determined assuming trying to delete parent and report that
			$deleted = false; 
		}

		if($deleted)
		{
			echo json_encode (array('success'=>true));
			exit;
		}
		else
		{
			echo json_encode (array('success'=>false));
			exit;
		}
	}
	
	public function actionReturnForm()
	{
		//don't reload these scripts or they will mess up the page
		//yiiactiveform.js still needs to be loaded that's why we don't use
		// Yii::app()->clientScript->scriptMap['*.js'] = false;
		$cs=Yii::app()->clientScript;
		$cs->scriptMap=array(
			'jquery.min.js'=>false,
			'jquery.js'=>false,
			'jquery.fancybox-1.3.4.js'=>false,
			'jquery.jstree.js'=>false,
			'jquery-ui-1.8.12.custom.min.js'=>false,
			'json2.js'=>false,
		);

		$modelName = empty($_POST['model_name']) ? $this->modelName : $_POST['model_name'];
		
		//Figure out if we are updating a Model or creating a new one.
		if(isset($_POST['update_id']))
		{
//TODO: may have validation problem here i.e. not sure if fields will be bound correctly so may have to place two modals in the form, one for create
// and one for update - not sure due to binding problem previously experienced after ajax
//			$model = $this->loadModel($_POST['update_id']);
			$model = $modelName::model()->findByPk($_POST['update_id']);
			$returnUrl = $this->createUrl("$modelName/update", array('id'=>$_POST['update_id']));
		}
		else
		{
			$model = new $modelName;
			// massive assignment
			$model->attributes = $_POST;
		}

		// set the url for the form so that doesn't try to come back here when update is clicked
		$this->renderPartial('//'.lcfirst($modelName).'/_form',
			array(
				'model'=>$model,
				'parent_id'=>!empty($_POST['parent_id'])
					? $_POST['parent_id']
					: '',
				'action'=>$returnUrl,
			),
			false,
			true
		);
	}

	public function actionReturnView()
	{
		//don't reload these scripts or they will mess up the page
		//yiiactiveform.js still needs to be loaded that's why we don't use
		// Yii::app()->clientScript->scriptMap['*.js'] = false;
		$cs=Yii::app()->clientScript;
		$cs->scriptMap=array(
			'jquery.min.js'=>false,
			'jquery.js'=>false,
			'jquery.fancybox-1.3.4.js'=>false,
			'jquery.jstree.js'=>false,
			'jquery-ui-1.8.12.custom.min.js'=>false,
			'json2.js'=>false,
		);

		$model=$this->loadModel($_POST['id']);

		$this->renderPartial('view', array('model'=>$model),false,true);
	}
	/*
	 * to be overidden if using mulitple models
	 */
	protected function createSave($model, &$models=array())
	{
		// if new root
		if(empty($_POST['parent_id']))
		{
			// atempt save
			$saved = $model->saveNode(true);
		}
		// otherwise appending to a node
		else
		{
			$parent=$this->loadModel($_POST['parent_id']);
			$saved = $model->appendTo($parent);
		}
		// put the model into the models array used for showing all errors
		$models[] = $model;
		
		return $saved;
	}
	
// Todo: override updateRedirect and createRedirect to ajax refresh of the the tree
	/*
	 * to be overidden if using mulitple models
	 */
	protected function updateSave($model, &$models=array())
	{
		// atempt save
		$saved = $model->saveNode(false);
		// put the model into the models array used for showing all errors
		$models[] = $model;
		
		return $saved;
	}

	public function actionMoveCopy()
	{
		$moved_node_id=$_POST['moved_node'];
		$new_parent_id=$_POST['new_parent'];
		$new_parent_root_id=$_POST['new_parent_root'];
		$previous_node_id=$_POST['previous_node'];
		$next_node_id=$_POST['next_node'];
		$copy=$_POST['copy'];

		//the following is additional info about the operation provided by
		// the jstree.It's there if you need it.See documentation for jstree.

		//  $old_parent_id=$_POST['old_parent'];
		//$pos=$_POST['pos'];
		//  $copied_node_id=$_POST['copied_node'];
		//  $replaced_node_id=$_POST['replaced_node'];

		if($copy != 'false')
		{
			throw Exception();
		}
		
		//the  moved,copied  node
		$moved_node=$this->loadModel($moved_node_id);

		//if we are not moving as a new root...
		if ($new_parent_root_id!='root')
		{
			//the new parent node
			$new_parent=$this->loadModel($new_parent_id);
			//the previous sibling node (after the move).
			if($previous_node_id!='false')
			{
				$previous_node=$this->loadModel($previous_node_id);
			}

			//if we move
			if ($copy == 'false')
			{
				//if the moved node is only child of new parent node
				if ($previous_node_id=='false'&&  $next_node_id=='false')
				{
					if ($moved_node->moveAsFirst($new_parent))
					{
						$moved_node->afterSave();
						echo json_encode(array('success'=>true));
						exit;
					}
				}
				//if we moved it in the first position
				else if($previous_node_id=='false' &&  $next_node_id !='false')
				{
					if($moved_node->moveAsFirst($new_parent))
					{
						$moved_node->afterSave();
						echo json_encode(array('success'=>true));
						exit;
					}
				}
				//if we moved it in the last position
				else if($previous_node_id !='false' &&  $next_node_id == 'false')
				{
					if($moved_node->moveAsLast($new_parent))
					{
						$moved_node->afterSave();
						echo json_encode(array('success'=>true));
						exit;
					}
				}
				//if the moved node is somewhere in the middle
				else if($previous_node_id !='false' &&  $next_node_id != 'false')
				{
					if($moved_node->moveAfter($previous_node))
					{
						$moved_node->afterSave();
						echo json_encode(array('success'=>true));
						exit;
					}
				}

			}

		}
		//if the new parent is not root end
		//else,move it as a new Root
		else
		{
			//if moved/copied node is not Root
			if(!$moved_node->isRoot())
			{
				if($moved_node->moveAsRoot())
				{
					echo json_encode(array('success'=>true ));
				}
				else
				{
					echo json_encode(array('success'=>false ));
				}
			}
			//else if moved/copied node is Root
			else
			{
				echo json_encode(array('success'=>false,'message'=>'Node is already a Root.Roots are ordered by id.' ));
			}
		}

	}

}

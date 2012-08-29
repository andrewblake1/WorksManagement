<?php

class ResourcecategoryController extends Controller
{
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

	public function accessRules()
	{
		return array(
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
		$this->registerCssAndJs('webroot.js_plugins.jqui1812',
			'/js/jquery-ui-1.8.12.custom.min.js',
			'/css/dark-hive/jquery-ui-1.8.12.custom.css');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js_plugins/json2/json2.js');
		Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/client_val_form.css','screen');
	}

	public function actionIndex()
	{
		//create an array open_nodes with the ids of the nodes that we want to be initially open
		//when the tree is loaded.Modify this to suit your needs.Here,we open all nodes on load.
		$categories= Resourcecategory::model()->findAll(array('order'=>'lft'));
		$identifiers=array();

		foreach($categories as $n=>$category)
		{
			$identifiers[]="'".'node_'.$category->id."'";
		}
		$open_nodes=implode(',', $identifiers);

		$baseUrl=Yii::app()->baseUrl;

		$dataProvider=new CActiveDataProvider('Resourcecategory');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'baseUrl'=> $baseUrl,
			'open_nodes'=> $open_nodes
		));
	}

	/**
	* Returns the data model based on the primary key given in the GET variable.
	* If the data model is not found, an HTTP exception will be raised.
	* @param integer the ID of the model to be loaded
	*/
	public function loadModel($id)
	{
		$model=Resourcecategory::model()->findByPk($id);
		if($model===null)
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}

		return $model;
	}

	public function actionFetchTree()
	{
		Resourcecategory::printULTree();
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

		if($deleted_cat->deleteNode())
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

		//Figure out if we are updating a Model or creating a new one.
		$model = isset($_POST['update_id']) ? $this->loadModel($_POST['update_id']) : new Resourcecategory;		

		$this->renderPartial('_form',
			array(
				'model'=>$model,
				'parent_id'=>!empty($_POST['parent_id'])?$_POST['parent_id']:''
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

	public function actionCreateRoot()
	{
		if(isset($_POST['Resourcecategory']))
		{
			$new_root=new Resourcecategory;
			$new_root->attributes=$_POST['Resourcecategory'];
			// Ab hacking changed this to force validation so that before validate adds staff_id
			echo $new_root->saveNode(true)
				? json_encode(array('success'=>true, 'id'=>$new_root->primaryKey))
				: json_encode(array('success'=>false, 'message'=>'Error.Root Resourcecategory was not created.'));
			exit;
		}
	}

	public function actionCreate()
	{
		if(isset($_POST['Resourcecategory']))
		{
			$model=new Resourcecategory;
			//set the submitted values
			$model->attributes=$_POST['Resourcecategory'];
			$parent=$this->loadModel($_POST['parent_id']);
			//return the JSON result to provide feedback.
			echo $model->appendTo($parent)
				? json_encode(array('success'=>true, 'id'=>$model->primaryKey))
				: json_encode(array('success'=>false, 'message'=>'Error.Resourcecategory was not created.'));
			exit;
		}
	}


	public function actionUpdate()
	{
		if(isset($_POST['Resourcecategory']))
		{
			$model=$this->loadModel($_POST['update_id']);
			$model->attributes=$_POST['Resourcecategory'];

			echo $model->saveNode(false)
				? json_encode(array('success'=>true))
				: json_encode(array('success'=>false));
		}
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
						echo json_encode(array('success'=>true));
						exit;
					}
				}
				//if we moved it in the first position
				else if($previous_node_id=='false' &&  $next_node_id !='false')
				{
					if($moved_node->moveAsFirst($new_parent))
					{
						echo json_encode(array('success'=>true));
						exit;
					}
				}
				//if we moved it in the last position
				else if($previous_node_id !='false' &&  $next_node_id == 'false')
				{
					if($moved_node->moveAsLast($new_parent))
					{
						echo json_encode(array('success'=>true));
						exit;
					}
				}
				//if the moved node is somewhere in the middle
				else if($previous_node_id !='false' &&  $next_node_id != 'false')
				{
					if($moved_node->moveAfter($previous_node))
					{
						echo json_encode(array('success'=>true));
						exit;
					}
				}

			}
			//else it is a copy
			else
			{
				//create the copied Resourcecategory model
				$copied_node=new Resourcecategory;
				//copy the attributes (only safe attributes will be copied).
				$copied_node->attributes=$moved_node->attributes;
				//remove the primary key
				$copied_node->id=null;


				if($copied_node->appendTo($new_parent))
				{
					echo json_encode(array('success'=>true, 'id'=>$copied_node->primaryKey));
					exit;
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

/*	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='resourcecategory-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}*/
}

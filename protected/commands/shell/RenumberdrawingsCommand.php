<?php
class RenumberdrawingsCommand extends CConsoleCommand
{
   
    private $_authManager;
 
    public function getHelp()
	{
		return <<<EOD
USAGE
	renumberdrawings

DESCRIPTION
	This command renumbers drawings as per id_new column that you should add to tbl_drawing table along with id_old. This takes care of renaming the drawing
	folders.
	
	It is advised to make a full system backup first at openhost.co.nz
	- under subscriptions

EOD;
	}

	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		//ensure that an authManager is defined as this is mandatory for creating an auth heirarchy
		if(($this->_authManager=Yii::app()->authManager)===null)
		{
		    echo "Error: an authorization manager, named 'authManager' must be con-figured to use this command.\n";
			echo "If you already added 'authManager' component in application con-figuration,\n";
			echo "please quit and re-enter the yiic shell.\n";

			return;
		}  
		
		//provide the oportunity for the use to abort the request
		echo "Would you like to continue? [Yes|No] ";
	   
	    //check the input from the user and continue if they indicated yes to the above question
	    if(!strncasecmp(trim(fgets(STDIN)),'y',1)) 
		{
		    //first we need to remove all operations, roles, child relationship and as-signments
			$this->_authManager->clearAll();

			// NB: these must be run first or there will be an integrity constraing violation against no updated_by
			// NB: these are just an initial user and should be changed once app is installed
			try
			{
				Yii::app()->db->createCommand("
					START TRANSACTION;
					UPDATE `tbl_drawing` SET id_old = id;
					UPDATE `tbl_drawing` SET id = id_new;
					COMMIT;
				")->execute();
			}
			catch (CDbException $e)
			{
				echo $e->getMessage();
				return;
			}

			// rename the directories
			$path = Yii::app()->params['privateUploadPath'];
			foreach(Drawing::model()->findAll() as $drawing)
			{
				exec("mv {$path}drawing/{$drawing->id_old} {$path}drawing/{$drawing->id_new}");
			}
			

			//provide a message indicating success
			echo "Complete. Please check all ok and if not restore the backup you hopeufully made at Openhost!";
        } 
    }
}

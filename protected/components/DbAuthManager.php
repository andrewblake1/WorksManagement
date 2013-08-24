<?php
class DbAuthManager extends CDbAuthManager
{

	public function createAuthItem($name,$type,$description='',$bizRule=null,$data=null)
	{
		$this->db->createCommand()
			->insertIgnore($this->itemTable, array(
				'name'=>$name,
				'type'=>$type,
				'description'=>$description,
				'bizrule'=>$bizRule,
				'data'=>serialize($data)
			));
		
		return new CAuthItem($this,$name,$type,$description,$bizRule,$data);
	}

}
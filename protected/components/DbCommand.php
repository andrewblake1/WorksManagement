<?php
class DbCommand extends CDbCommand
{
	public function insertIgnore($table, $columns)
	{
		$params=array();
		$names=array();
		$placeholders=array();
		foreach($columns as $name=>$value)
		{
			$names[]=$this->connection->quoteColumnName($name);
			if($value instanceof CDbExpression)
			{
				$placeholders[] = $value->expression;
				foreach($value->params as $n => $v)
					$params[$n] = $v;
			}
			else
			{
				$placeholders[] = ':' . $name;
				$params[':' . $name] = $value;
			}
		}
		$sql='INSERT IGNORE INTO ' . $this->connection->quoteTableName($table)
			. ' (' . implode(', ',$names) . ') VALUES ('
			. implode(', ', $placeholders) . ')';
		return $this->setText($sql)->execute($params);
	}

}
?>

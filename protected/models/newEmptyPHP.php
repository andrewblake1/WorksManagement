DELIMITER $$

CREATE PROCEDURE `pro_get_duties_from_planning` (IN in_id INT)
/*
Create a temporary table containing relevant duties and remove irrelevant branches
@param in_id - id of parent item in wbs
*/
BEGIN

    /* variable declarations section */
    DECLARE cur_duty_step_dependency_id INT;

    DECLARE no_more_rows INT;  

    /* cursor declaration section */

	/* get list of end children i.e. have no children */
    DECLARE cursor_end_children CURSOR FOR 
		SELECT DISTINCT
			t.id
		FROM tbl_duty_step_dependency t
		LEFT JOIN tbl_duty_step_dependency t2
			ON t.parent_duty_step_id = t2.child_duty_step_id
		WHERE t2.id IS NULL;

    /* get a list of dead branch starting points */
    DECLARE cursor_dead_branch CURSOR FOR 
        SELECT
			t.duty_step_dependency_id
        FROM tmp_tmp_duty t
		JOIN tbl_task_to_custom_field_to_task_template ON t.id = tbl_task_to_custom_field_to_task_template.task_id
        JOIN tbl_custom_field_to_task_template ON tbl_task_to_custom_field_to_task_template.custom_field_to_task_template_id = tbl_custom_field_to_task_template.id
        JOIN tbl_custom_field ON tbl_custom_field_to_task_template.custom_field_id = tbl_custom_field.id
		WHERE t.compare IS NOT NULL AND t.custom_value NOT REGEXP t.compare;

    /* handler declaration section */
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows = TRUE;

    /* processing section */

    /* obtain a list of duties from all tasks from the in params - because planning table is nested set we can retrieve these in a single query */
    /* create a temporary table to hold the result set */
    DROP TEMPORARY TABLE IF EXISTS tmp_tmp_duty;
    CREATE TEMPORARY TABLE tmp_tmp_duty
		SELECT
			t.*,
			dutyStepBranch.compare,
			dutyDataToCustomFieldToDutyStep.custom_value,
			dutyStepDependency.id AS duty_step_dependency_id,
			NULL AS flag
		FROM v_duty t
			JOIN tbl_planning planning ON t.task_id = planning.id
			LEFT JOIN tbl_duty_step_dependency dutyStepDependency ON t.duty_step_id = dutyStepDependency.parent_duty_step_id
			LEFT JOIN v_duty dutyChild
				ON dutyStepDependency.child_duty_step_id = dutyChild.duty_step_id 
				AND t.task_id = dutyChild.task_id
			LEFT JOIN tbl_duty_data_to_custom_field_to_duty_step dutyDataToCustomFieldToDutyStep ON dutyChild.duty_data_id = dutyDataToCustomFieldToDutyStep.duty_data_id
			LEFT JOIN tbl_duty_step_branch dutyStepBranch
				ON dutyDataToCustomFieldToDutyStep.custom_field_to_duty_step_id = dutyStepBranch.custom_field_to_duty_step_id
				AND dutyStepDependency.id = dutyStepBranch.duty_step_dependency_id
		WHERE planning.`level` = 4
			AND planning.lft >= (SELECT tbl_planning.lft FROM tbl_planning WHERE tbl_planning.id = in_id)
			AND planning.rgt<= (SELECT tbl_planning.rgt FROM tbl_planning WHERE tbl_planning.id = in_id)
			AND planning.root =  (SELECT tbl_planning.root FROM tbl_planning WHERE tbl_planning.id = in_id);

    /* loop thru dead branches */
    OPEN cursor_end_children;
    loop_end_children: LOOP

        FETCH cursor_end_children INTO
            cur_duty_step_dependency_id;

        IF no_more_rows THEN
            SET no_more_rows = FALSE;
            CLOSE cursor_dead_branch;
            LEAVE loop_dead_branch;
        END IF;

         /* flag traceable unblocked branches */
		CALL pro_flag_duty_branches(cur_duty_step_dependency_id);

    END LOOP; /* end loop thru custom values*/

	/* remove non flagged branches */
	DELETE FROM tmp_tmp_duty WHERE flag IS NULL;
	
	/* get distinct list */
    DROP TEMPORARY TABLE IF EXISTS tmp_duty;
    CREATE TEMPORARY TABLE tmp_duty
		SELECT DISTINCT * FROM tmp_tmp_duty;

END
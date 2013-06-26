<script  type="text/javascript">
	$(function ()
	{
		$("#<?php echo $modelName::ADMIN_TREE_CONTAINER_ID; ?>").delegate("a","click", function(e) {
			// get the id of the clicked node
			id = $(this).parent().attr("id").split("_")[1];
			// go to the admin screen - filtering by this parent id
			window.location = encodeURI("<?php echo "$baseUrl/CustomFieldToDutyStep/admin?custom_field_duty_step_category_id="; ?>" + id);
		});

	});

</script>
SELECT
	CONCAT_WS('', DATE_FORMAT(tbl_day.scheduled, '%D %M %y'), ' at ', TIME_FORMAT(ADDTIME('8:00:00', tbl_project.travel_time_1_way), '%k:%i %p'), ' (', TIME_FORMAT(tbl_project.travel_time_1_way, '%k:%i'), ' hours travel', ')')
FROM tbl_crew
	JOIN tbl_day ON tbl_crew.day_id = tbl_day.id
	JOIN tbl_project ON tbl_day.project_id = tbl_project.id
WHERE tbl_crew.id = :pk
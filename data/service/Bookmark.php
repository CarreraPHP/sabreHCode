<?PHP

namespace sabreHcode\data\service {

	class Bookmark {

		private $_oDbInstance;

		public function __construct($dbInstance) {

			$this->_oDbInstance = $dbInstance;
		}

		public function mark($course_id, $unmark = false) {
			$course_id = str_replace("cou", "", $course_id);
			$user = (object)$_SESSION['userDetails'][0];
			
			$dSql = "DELETE FROM pt_bookmark WHERE course_id = '". $course_id . "' AND created_by = '" . $user->id. "'";
			$bResult = $this->_oDbInstance->query($dSql);
			if($unmark) return (boolean) $bResult;
			
			$sSql = "INSERT INTO pt_bookmark (course_id, deleted, created_by, updated_by, created_date, updated_date) VALUES ('" . $course_id . "', 'F', '" . $user->id . "','" . $user->id . "', NOW(), NOW())";
			$oResult = $this->_oDbInstance->query($sSql);
			
			return (boolean) $oResult;
		}
		
		public function bookmarkList() {
			$aRecordCollection = array();
			$user = (object)$_SESSION['userDetails'][0];
				//WHERE (`category_id` = $parent OR `path` LIKE '%/1/%') 
				$sSql = "SELECT 
										CONCAT('cou', `pt_course`.id) AS id,
										name,
										description,
										SUBSTRING_INDEX(`path`, '/1/', - 1) AS `path`,
										TRUE AS `leaf`,
										`category_id` AS `parent_id`
								FROM
								`sabre`.`pt_bookmark` 
								LEFT JOIN `sabre`.`pt_course` ON `pt_course`.`id` = `pt_bookmark`.`course_id` 
								WHERE
										`pt_bookmark`.`deleted` = 'F' AND `pt_course`.`deleted` = 'F'
								ORDER BY `parent_id` ASC , `id` ASC";
				$oResult = $this -> _oDbInstance -> query($sSql);
				
				if($oResult -> num_rows > 0){
					while($aRecord = $oResult -> fetch_assoc()){
						$childPermissionArray = array('create' => false, 'update' => false, 'read' => false, 'delete' => false, 'permission' => false);
						$courseId = preg_replace('/cou/', '', $aRecord['id']);
						$sql = "SELECT `id`, `user_id`, `user_role_id`, `module_id`, `module_sub_id`, GROUP_CONCAT(`module_access_level`) As module_access_level FROM `sabre`.`pt_user_module_access` WHERE `user_id` = '$user->id' AND `module_id` = '1' AND `module_sub_id` IN (" . $courseId . ") GROUP BY `module_id`, `module_sub_id` ORDER BY `module_sub_id` ASC";
						$Result = $this -> _oDbInstance -> query($sql);
						if ($Result -> num_rows > 0) {
							while ($Record = $Result -> fetch_assoc()) {
								$mod_acc_lev = explode(",", $Record['module_access_level']);
								if (in_array('C', $mod_acc_lev)) {
									$childPermissionArray['create'] = true;
								}
								if (in_array('R', $mod_acc_lev)) {
									$childPermissionArray['read'] = true;
								}
								if (in_array('U', $mod_acc_lev)) {
									$childPermissionArray['update'] = true;
								}
								if (in_array('D', $mod_acc_lev)) {
									$childPermissionArray['delete'] = true;
								}
								if (in_array('P', $mod_acc_lev)) {
									$childPermissionArray['permission'] = true;
								}
							}
						}
						$aRecord['permission'] = $childPermissionArray;						
						$aRecordCollection[] = $aRecord;
					}
				}
				return $aRecordCollection;
		}
	}

}
?>
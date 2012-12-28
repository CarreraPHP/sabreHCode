<?PHP

namespace sabreHcode\data\service {

    class Bookmark {

        private $_oDbInstance;

        public function __construct($dbInstance) {

            $this->_oDbInstance = $dbInstance;
        }

        public function mark($course_id, $unmark = false) {
            $course_id = str_replace("cou", "", $course_id);
            $userId = $_SESSION['userDetails'][0]['id'];
            $sSql = "SELECT `id` FROM `sabre`.`pt_bookmark` WHERE `course_id` = $course_id AND `created_by` = $userId";
            $aResult = $this->_oDbInstance->query($sSql);
            if ($aResult->num_rows > 0) {
                if ($unmark == true) {
                    while ($record = $aResult->fetch_assoc()) {
                        $update = "UPDATE `sabre`.`pt_bookmark` SET `deleted` = 'T' WHERE `id` = '" . $record['id'] . "' AND `created_by` = $userId";
                        $result = $this->_oDbInstance->query($update);
                        if ($result) {
                            return array('success' => true, 'message' => 'course deleted from bookmark');
                        }
                    }
                } else {
                    return array('success' => true, 'message' => 'course already exists in bookmark');
                }
            } else {
                $sSql = "INSERT INTO pt_bookmark (course_id, deleted, created_by, updated_by, created_date, updated_date) VALUES ('" . $course_id . "', 'F', '$userId','$userId', NOW(), NOW())";
                $oResult = $this->_oDbInstance->query($sSql);
                if ($oResult) {
                    return array('success' => true, 'message' => 'course added from bookmark');
                }
            }
        }

        public function bookmarkList() {
            $aRecordCollection = array();
            $userId = $_SESSION['userDetails'][0]['id'];
            //WHERE (`category_id` = $parent OR `path` LIKE '%/1/%') 
            $sSql = "SELECT CONCAT('cou', `pt_course`.id) AS id, name, description, SUBSTRING_INDEX(`path`, '/1/', - 1) AS `path`, TRUE AS `leaf`, `category_id` AS `parent_id` FROM `sabre`.`pt_bookmark` LEFT JOIN `sabre`.`pt_course` ON `pt_course`.`id` = `pt_bookmark`.`course_id` WHERE `pt_bookmark`.`deleted` = 'F' AND `pt_course`.`deleted` = 'F' AND `pt_bookmark`.`created_by` = $userId ORDER BY `parent_id` ASC , `id` ASC";
            $oResult = $this->_oDbInstance->query($sSql);

            if ($oResult->num_rows > 0) {
                while ($aRecord = $oResult->fetch_assoc()) {
                    if ($userId != 1) {
                        $childPermissionArray = array('create' => false, 'update' => false, 'read' => false, 'delete' => false, 'permission' => false);
                        $courseId = preg_replace('/cou/', '', $aRecord['id']);
                        $sql = "SELECT `id`, `user_id`, `user_role_id`, `module_id`, `module_sub_id`, GROUP_CONCAT(`module_access_level`) As module_access_level FROM `sabre`.`pt_user_module_access` WHERE `user_id` = '$userId' AND `module_id` = '1' AND `module_sub_id` IN (" . $courseId . ") GROUP BY `module_id`, `module_sub_id` ORDER BY `module_sub_id` ASC";
                        $Result = $this->_oDbInstance->query($sql);
                        if ($Result->num_rows > 0) {
                            while ($Record = $Result->fetch_assoc()) {
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
                    }else{
                       $childPermissionArray = array('create' => false, 'update' => false, 'read' => true, 'delete' => false, 'permission' => false); 
                       $aRecord['permission'] = $childPermissionArray;
                        $aRecordCollection[] = $aRecord;
                    }
                }
            }
            return $aRecordCollection;
        }

    }

}
?>
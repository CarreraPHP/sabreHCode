<?PHP

namespace sabreHcode\data\service {

    class Quiz {

        private $_oDbInstance;

        public function __construct($dbInstance) {

            $this->_oDbInstance = $dbInstance;
        }

        public function getAllSingleQuiz($course_id, $topic_id, $content_id) {
            $course_id = preg_replace('/cou/', '', $course_id);
            if (isset($_SESSION['userDetails'])) {
                $user_id = $_SESSION['userDetails'][0]['id'];
            }
            $totalArr = array();
            $sql = "SELECT qm1.`ques_id`, qm1.`ans_id`,	qm1.`quiz_id`, qm1.`result`, qm1.`course_id`, qm1.`topic_id`, qm1.`content_id`, a1.`name` AS ans_name, 	q1.`name` AS ques_name ";
            $sql .= "FROM `pt_quiz_meta` qm1 ";
            $sql .= "LEFT JOIN `pt_quiz_content` q1 ON qm1.`ques_id` = q1.`id` AND q1.`content_type` = 'Q' AND q1.`quiz_type` = qm1.`quiz_id` ";
            $sql .= "LEFT JOIN `pt_quiz_content` a1 ON qm1.`ans_id` = a1.`id` AND a1.`content_type` = 'A' AND a1.`quiz_type` = qm1.`quiz_id` ";
            $sql .= "WHERE qm1.`course_id` = '$course_id' AND qm1.`topic_id` = '$topic_id' AND qm1.`content_id` = '$content_id' ";
            $sql .= "ORDER BY qm1.`ques_id` ASC";
            $result = $this->_oDbInstance->query($sql);
            if ($result->num_rows > 0) {
                while ($record = $result->fetch_assoc()) {
                    $totalArr[$record['ques_id']]['ques_id'] = $record['ques_id'];
                    $totalArr[$record['ques_id']]['ans_id'] = $record['ans_id'];
                    $totalArr[$record['ques_id']]['quiz_id'] = $record['quiz_id'];
                    $totalArr[$record['ques_id']]['course_id'] = $record['course_id'];
                    $totalArr[$record['ques_id']]['topic_id'] = $record['topic_id'];
                    $totalArr[$record['ques_id']]['content_id'] = $record['content_id'];
                    $totalArr[$record['ques_id']]['name'] = $record['ques_name'];
                    if ($record['result'] == 'T') {
                        $totalArr[$record['ques_id']]['correct'][] = $record;
                    }
                    $totalArr[$record['ques_id']]['ans'][] = $record;
                }
                $sSql = "SELECT t1.parent_ques_id AS p_id, t1.ques_id, t1.ans_id, result_id, t2.name AS p_name, t3.name AS ques_name, t4.name AS ans_name, t5.name AS cor_ans, t1.course_id, t1.topic_id, t1.content_id, t1.quiz_id ";
                $sSql .= "FROM matching_quiz_meta AS t1 ";
                $sSql .= "LEFT JOIN pt_quiz_content AS t2 ON t1.parent_ques_id = t2.id ";
                $sSql .= "LEFT JOIN pt_quiz_content AS t3 ON t1.ques_id = t3.id ";
                $sSql .= "LEFT JOIN pt_quiz_content AS t4 ON t1.ans_id = t4.id ";
                $sSql .= "LEFT JOIN pt_quiz_content AS t5 ON t1.result_id = t5.id ";
                $sSql .= "WHERE t1.`course_id` = $course_id AND t1.`topic_id` = $topic_id AND t1.`content_id` = $content_id";
                $Result = $this->_oDbInstance->query($sSql);
                while ($Record = $Result->fetch_assoc()) {
                    $totalArr[$Record['p_id']]['p_id'] = $Record['p_id'];
                    $totalArr[$Record['p_id']]['ques_id'] = $Record['ques_id'];
                    $totalArr[$Record['p_id']]['ans_id'] = $Record['ans_id'];
                    $totalArr[$Record['p_id']]['result_id'] = $Record['result_id'];
                    $totalArr[$Record['p_id']]['p_name'] = $Record['p_name'];
                    $totalArr[$Record['p_id']]['course_id'] = $Record['course_id'];
                    $totalArr[$Record['p_id']]['topic_id'] = $Record['topic_id'];
                    $totalArr[$Record['p_id']]['content_id'] = $Record['content_id'];
                    $totalArr[$Record['p_id']]['quiz_id'] = $Record['quiz_id'];
                    $totalArr[$Record['p_id']]['ques_name'][] = $Record;
                    $totalArr[$Record['p_id']]['ans_name'][] = $Record;
                    $totalArr[$Record['p_id']]['cor_ans'][] = $Record;
                }
                return array("data" => array_values($totalArr), "total" => count($totalArr));
            } else {
                return $totalArr[] = array('name' => "No Records Found");
            }
        }

        public function getMatchQuizQues($course_id, $topic_id, $content_id, $ques_id) {
            $totalArr = array();
            $course_id = preg_replace('/cou/', '', $course_id);
            $sql = "SELECT t1.ques_id, t2.name AS ques_name, t1.ans_id, t3.name AS ans_name FROM matching_quiz_meta AS t1 ";
            $sql .= "LEFT JOIN pt_quiz_content AS t2 ON t1.ques_id = t2.id ";
            $sql .= "LEFT JOIN pt_quiz_content AS t3 ON t1.ans_id = t3.id ";
            $sql .= "WHERE t1.`course_id` = $course_id AND t1.`topic_id` = $topic_id AND t1.`content_id` = $content_id AND t1.parent_ques_id = $ques_id";
            $Result = $this->_oDbInstance->query($sql);
            if ($Result->num_rows > 0) {
                while ($Record = $Result->fetch_assoc()) {
                    $totalArr[$Record['ques_id']] = $Record;
                }
                return array("data" => array_values($totalArr), "total" => count($totalArr));
            } else {
                return $totalArr[] = array('name' => "No Records Found");
            }
        }

        public function getSingleQuizResult($course_id, $topic_id, $content_id) {
            $role_id = '';
            $database = 'sabre';
            $resultArr = array();
            $corAnsArr = array();
            $selAnsArr = array();
            $quesArr = array();
            $course_id = preg_replace('/cou/', '', $course_id);
            if (isset($_SESSION['userDetails'])) {
                $user_id = $_SESSION['userDetails'][0]['id'];
                $role_id = $_SESSION['userDetails'][0]['role_id'];
            }
            if ($role_id != '' && $role_id == 1) {
                $table = 'pt_admin_quiz';
            } else {
                $table = 'pt_quiz_result';
            }
            $sql = "SELECT q1.id AS ques_id, q1.name AS ques_name, q1.quiz_type, a1.id AS cor_id, a1.name AS cor_ans, a2.id AS sel_id, a2.name AS sel_ans, q1.course_id, q1.topic_id, q1.content_id, m1.result, qp1.result AS final ";
            $sql .="FROM pt_quiz_content q1 ";
            $sql .= "LEFT JOIN pt_quiz_meta m1 ON m1.ques_id = q1.id AND m1.result = 'T' AND q1.course_id = m1.course_id AND q1.topic_id = m1.topic_id AND q1.content_id = m1.content_id ";
            $sql .= "LEFT JOIN pt_quiz_content a1 ON m1.ans_id = a1.id ";
            $sql .= "LEFT JOIN $table qp1 ON qp1.que_id = q1.id AND q1.course_id = qp1.course_id AND q1.topic_id = qp1.topic_id AND q1.content_id = qp1.content_id ";
            $sql .= "LEFT JOIN pt_quiz_content a2 ON qp1.selected_ans_id = a2.id ";
            $sql .= "WHERE q1.content_type = 'Q' AND q1.course_id = $course_id AND q1.topic_id = $topic_id AND q1.content_id = $content_id"; //GROUP BY cor_ans

            $result = $this->_oDbInstance->query($sql);
            if ($result->num_rows > 0) {
                while ($record = $result->fetch_assoc()) {
                    $corAns = array();
                    $selAns = array();
                    $totalArr[$record['ques_id']]['ques_id'] = $record['ques_id'];
                    $totalArr[$record['ques_id']]['quiz_type'] = $record['quiz_type'];
                    $totalArr[$record['ques_id']]['ques_name'] = $record['ques_name'];
                    $totalArr[$record['ques_id']]['cor_id'] = $record['cor_id'];

                    $totalArr[$record['ques_id']]['sel_id'] = $record['sel_id'];
                    $totalArr[$record['ques_id']]['course_id'] = $record['course_id'];
                    $totalArr[$record['ques_id']]['topic_id'] = $record['topic_id'];
                    $totalArr[$record['ques_id']]['content_id'] = $record['content_id'];
                    $corAnsArr[] = $record['cor_id'];
                    $selAnsArr[] = $record['sel_id'];
                    $quesArr[] = $record['ques_id'];
                    $corAns = array(
                        "cor_ans" => $record['cor_ans'],
                        "cor_id" => $record['cor_id'],
                        "quiz_type" => $record['quiz_type'],
                        "course_id" => $record['course_id'],
                        "topic_id" => $record['topic_id'],
                        "content_id" => $record['content_id'],
                        "ques_id" => $record['ques_id']
                    );
                    $selAns = array(
                        "sel_ans" => $record['sel_ans'],
                        "sel_id" => $record['sel_id'],
                        "quiz_type" => $record['quiz_type'],
                        "course_id" => $record['course_id'],
                        "topic_id" => $record['topic_id'],
                        "content_id" => $record['content_id'],
                        "ques_id" => $record['ques_id']
                    );

                    $totalArr[$record['ques_id']]['cor_ans'][$record['cor_id']] = $corAns;
                    $totalArr[$record['ques_id']]['sel_ans'][$record['sel_id']] = $selAns;
                }

                $corAnsArr = array_values(array_unique($corAnsArr));
                $selAnsArr = array_values(array_unique($selAnsArr));
                $quesArr = array_values(array_unique($quesArr));

                for ($i = 0; $i < count($quesArr); $i++) {
                    $selCnt = count($totalArr[$quesArr[$i]]['sel_ans']);
                    $corCut = count($totalArr[$quesArr[$i]]['cor_ans']);
                    if ($corCut == $selCnt) {
                        for ($j = 0; $j < count($corAnsArr); $j++) {
                            for ($k = 0; $k < count($selAnsArr); $k++) {
                                if ($corAnsArr[$j] == $selAnsArr[$k]) {
                                    $totalArr[$quesArr[$i]]['icon'] = "resources/images/icons/accept.png";
                                } else {
                                    $totalArr[$quesArr[$i]]['icon'] = "resources/images/icons/cancel.png";
                                }
                            }
                        }
                    } else {
                        $totalArr[$quesArr[$i]]['icon'] = "resources/images/icons/cancel.png";
                    }
                }
                $totalArr = array_values($totalArr);
                $totalArrCount = count($totalArr);
                for ($index = 0; $index < $totalArrCount; $index++) {
                    $totalArr[$index]['cor_ans'] = array_values($totalArr[$index]['cor_ans']);
                    $totalArr[$index]['sel_ans'] = array_values($totalArr[$index]['sel_ans']);
                }
                return array("success" => true, "data" => array_values($totalArr));
            } else {
                return $aRecordCollection[] = array('name' => "No Records Found");
            }
        }

        public function getQuizResultChart($course_id, $topic_id, $content_id) {
            if (isset($_SESSION['userDetails'])) {
                $user_id = $_SESSION['userDetails'][0]['id'];
                $role_id = $_SESSION['userDetails'][0]['role_id'];
            }
            if ($role_id != '' && $role_id == 1) {
                $table = 'pt_admin_quiz';
                $field = 'percentage';
            } else {
                $table = 'pt_quiz_result';
                $field = 'percentage %';
            }
            $database = 'sabre';
            $course_id = preg_replace('/cou/', '', $course_id);
            $percentage = array();
            $count = '';
            $ansCount = array();
            $total = 100;
            $finalArray = array();
            $totArr = array();
            $sql = "SELECT t1.`que_id`, t1.`selected_ans_id`, t1.`$field`, t2.`ans_id`, t2.`quiz_id` FROM `$table` t1 LEFT JOIN pt_quiz_meta t2 ON t1.que_id = t2.ques_id WHERE t2.result = 'T' AND t1.`user_id` = '$user_id' AND t1.`course_id` = '$course_id' AND t1.`topic_id` = '$topic_id' AND t1.`content_id` = '$content_id'";
            $oResult = $this->_oDbInstance->query($sql);
            if ($oResult->num_rows > 0) {
                $count = $oResult->num_rows;

                while ($oRecord = $oResult->fetch_assoc()) {
                    $totArr[$oRecord['que_id']]['selected_ans_id'] = $oRecord['selected_ans_id'];
                    $totArr[$oRecord['que_id']][$field] = $oRecord[$field];
                    $totArr[$oRecord['que_id']]['que_id'] = $oRecord['que_id'];
                    $totArr[$oRecord['que_id']]['ans_id'] = $oRecord['ans_id'];
                    $totArr[$oRecord['que_id']]['quiz_id'] = $oRecord['quiz_id'];
                    $corAnsArr[] = $oRecord['ans_id'];
                    $selAnsArr[] = $oRecord['selected_ans_id'];
                    $quesArr[] = $oRecord['que_id'];
                    $corAns = array(
                        "cor_id" => $oRecord['ans_id'],
                        "ques_id" => $oRecord['que_id'],
                        "percentage" => $oRecord[$field]
                    );
                    $selAns = array(
                        "sel_id" => $oRecord['selected_ans_id'],
                        "ques_id" => $oRecord['que_id'],
                        "percentage" => $oRecord[$field]
                    );
                    $totArr[$oRecord['que_id']]['cor_ans'][$oRecord['ans_id']] = $corAns;
                    $totArr[$oRecord['que_id']]['sel_ans'][$oRecord['selected_ans_id']] = $selAns;
                }
                $corAnsArr = array_values(array_unique($corAnsArr));
                $selAnsArr = array_values(array_unique($selAnsArr));
                $quesArr = array_values(array_unique($quesArr));

                for ($i = 0; $i < count($quesArr); $i++) {
                    $selCnt = count($totArr[$quesArr[$i]]['sel_ans']);
                    $corCut = count($totArr[$quesArr[$i]]['cor_ans']);
                    if ($corCut == $selCnt) {
                        for ($j = 0; $j < count($corAnsArr); $j++) {
                            for ($k = 0; $k < count($selAnsArr); $k++) {
                                if (trim($corAnsArr[$j]) == trim($selAnsArr[$k])) {
                                    $totArr[$quesArr[$i]]['percentage'] = "100";
                                } else {
                                    $totArr[$quesArr[$i]]['percentage'] = "0";
                                }
                            }
                        }
                    } else {
                        $totArr[$quesArr[$i]]['percentage'] = "0";
                    }
                }
                $totArr = array_values($totArr);
                $totArrCount = count($totArr);

                for ($i = 0; $i < $totArrCount; $i++) {
                    $percentage[] = $totArr[$i]['percentage'];
                    if ($totArr[$i]['percentage'] == 100) {
                        $ansCount[] = $oRecord[$field];
                    }
                }
                $avg = array_sum($percentage) / $totArrCount;
                $i = 0;
                $outOf = count($ansCount);
                if ($avg != 0) {
                    $finalArray[$i]['name'] = 'Pass ' . round($avg) . '%';
                    $finalArray[$i]['result'] = round($avg);
                    $finalArray[$i]['count'] = $totArrCount;
                    $finalArray[$i]['correct'] = $outOf;
                    $finalArray[$i + 1]['name'] = 'Fail ' . round($total - $avg) . '%';
                    $finalArray[$i + 1]['result'] = round($total - $avg);
                    $finalArray[$i + 1]['count'] = $totArrCount;
                    $finalArray[$i + 1]['correct'] = $outOf;
                } else {
                    $finalArray[$i]['name'] = 'Fail ' . round($total - $avg) . '%';
                    $finalArray[$i]['result'] = $total;
                    $finalArray[$i]['count'] = $totArrCount;
                    $finalArray[$i]['correct'] = $outOf;
                }
            }
            return array("data" => $finalArray);
        }

        public function setQuizAttempt($user_id, $course_id, $topic_id, $content_id) {
            $course_id = preg_replace('/cou/', '', $course_id);
            $aRecordCollection = array();
            $sql = "SELECT * FROM `sabre`.`pt_quiz_attempt` WHERE user_id = '$user_id' AND `course_id` = '$course_id' AND `topic_id` = '$topic_id' AND `content_id` = '$content_id'";
            $Result = $this->_oDbInstance->query($sql);
            if ($Result->num_rows == 0) {
                $attempt = 1;
                $insert = "INSERT INTO `sabre`.`pt_quiz_attempt` (`user_id`,`attempt`,`course_id`, `topic_id`, `content_id`, `created_by`,`created_date`,`updated_by`,`update_date`)VALUES('$user_id',$attempt,'$course_id','$topic_id','$content_id',1',NOW(),'1',NOW())";
                $Result = $this->_oDbInstance->query($insert);
                if ($Result) {
                    $sSql = "SELECT `id`, `user_id`, `attempt` FROM `sabre`.`pt_quiz_attempt` WHERE user_id = '$user_id'";
                    $Result = $this->_oDbInstance->query($sSql);
                    while ($Record = $Result->fetch_assoc()) {
                        $aRecordCollection[] = $Record;
                    }
                }
                return json_encode(array('success' => true, 'message' => 'Record inserted successfully', 'data' => $aRecordCollection));
            } else {
                while ($Record = $Result->fetch_assoc()) {
                    $attempt = $Record['attempt'] + 1;
                    $id = $Record['id'];
                    $update = "UPDATE `sabre`.`pt_quiz_attempt` SET `attempt` = $attempt WHERE user_id = '$user_id' AND `id` = $id";
                    $Result = $this->_oDbInstance->query($update);
                    if ($Result) {
                        $sSql = "SELECT `id`, `user_id`, `attempt` FROM `sabre`.`pt_quiz_attempt` WHERE user_id = '$user_id'";
                        $Result = $this->_oDbInstance->query($sSql);
                        while ($Record = $Result->fetch_assoc()) {
                            $aRecordCollection[] = $Record;
                        }
                    }
                }
                return json_encode(array('success' => true, 'message' => 'Record updated successfully', 'data' => $aRecordCollection));
            }
        }

        public function SaveQuiz($data) {
            $role_id = '';
            $course_id = preg_replace('/cou/', '', $data['course_id']);
            $database = 'sabre';
            if (isset($_SESSION['userDetails'])) {
                $user_id = $_SESSION['userDetails'][0]['id'];
                $role_id = $_SESSION['userDetails'][0]['role_id'];
            }
            if ($role_id != '' && $role_id == 1) {
                $table = 'pt_admin_quiz';
                $field = 'percentage';
            } else {
                $table = 'pt_quiz_result';
                $field = 'percentage %';
            }
            if ($data['quiz_id'] == "4") {
                $sql = "SELECT `result` FROM `sabre`.`pt_quiz_meta` WHERE `ans_id` = '" . $data['Answer'] . "'";
                $Results = $this->_oDbInstance->query($sql);
                while ($Record = $Results->fetch_assoc()) {
                    if ($Record['result'] == 'T') {
                        $percentage = "100%";
                    } else {
                        $percentage = "0%";
                    }
                    $select = "SELECT * FROM `" . $database . "`" . ".`$table` WHERE `user_id` = '$user_id' AND que_id = '" . $data['que_id'] . "'";
                    $oResult = $this->_oDbInstance->query($select);
                    if ($oResult->num_rows == 0) {
                        $insert = "INSERT INTO `" . $database . "`" . ".`$table`";
                        $insert .= "(`user_id`,`que_id`,`selected_ans_id`,`result`, `$field`, `course_id`, `topic_id`, `content_id`, ";
                        $insert .= "`created_by`,`created_date`,`updated_by`,`updated_date`)";
                        $insert .= "VALUES('$user_id','" . $data['que_id'] . "','" . $data['Answer'] . "','" . $Record['result'] . "','$percentage', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '$user_id',NOW(),'$user_id',NOW())";
                        $Result = $this->_oDbInstance->query($insert);
                    } else {
                        while ($oRecord = $oResult->fetch_assoc()) {
                            $update = "UPDATE `" . $database . "`" . ".`$table` SET `selected_ans_id` = '" . $data['Answer'] . "', `result` = '" . $Record['result'] . "', `$field` = '$percentage', `updated_date` = NOW() WHERE user_id = '$user_id' AND `id` = '" . $oRecord['id'] . "'";
                            $Result = $this->_oDbInstance->query($update);
                        }
                    }
                }
            } elseif ($data['quiz_id'] == "3") {
                $ans = array();
                if (isset($data['Answer1'])) {
                    $ans[] = $data['Answer1'];
                }
                if (isset($data['Answer2'])) {
                    $ans[] = $data['Answer2'];
                }
                if (isset($data['Answer3'])) {
                    $ans[] = $data['Answer3'];
                }
                if (isset($data['Answer4'])) {
                    $ans[] = $data['Answer4'];
                }
                $sql = "SELECT `id` FROM `" . $database . "`" . ".`$table` WHERE `que_id` = " . $data['que_id'];
                $Results = $this->_oDbInstance->query($sql);
                if ($Results->num_rows > 0) {
                    while ($Records = $Results->fetch_assoc()) {
                        $mulArr[] = $Records['id'];
                    }
                    $delId = implode(",", $mulArr);
                    $del = "DELETE FROM `" . $database . "`" . ".`$table` WHERE `id` IN($delId)";
                    $Results = $this->_oDbInstance->query($del);
                }
                for ($i = 0; $i < count($ans); $i++) {
                    $sql = "SELECT `result` FROM `sabre`.`pt_quiz_meta` WHERE `ans_id` = '" . $ans[$i] . "'";
                    $Results = $this->_oDbInstance->query($sql);
                    while ($Records = $Results->fetch_assoc()) {
                        if ($Records['result'] == 'T') {
                            $percentage = "100%";
                        } else {
                            $percentage = "0%";
                        }
                        $insert = "INSERT INTO `" . $database . "`" . ".`$table`";
                        $insert .= "(`user_id`,`que_id`,`selected_ans_id`,`result`, `$field`, `course_id`, `topic_id`, `content_id`, ";
                        $insert .= "`created_by`,`created_date`,`updated_by`,`updated_date`)";
                        $insert .= "VALUES('$user_id','" . $data['que_id'] . "','" . $ans[$i] . "','" . $Records['result'] . "','$percentage', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '$user_id',NOW(),'$user_id',NOW())";
                        $Result = $this->_oDbInstance->query($insert);
                    }
                }
            }
            return array('success' => true);
        }

        public function AddQuiz($data) {
            $course_id = preg_replace('/cou/', '', $data['course_id']);
            $data['question'] = addslashes($data['question']);
            $ansArr = array();
            $ansArr['answer'][] = addslashes($data['answer1']);
            $ansArr['answer'][] = addslashes($data['answer2']);
            $ansArr['answer'][] = addslashes($data['answer3']);
            $ansArr['answer'][] = addslashes($data['answer4']);
            if ($data['quiz_type'] == 4) {
                if ($data['id'] == 'new') {
                    if (isset($data['question'])) {
                        $select = "SELECT * FROM `sabre`.`pt_quiz_content` WHERE `name` = '" . $data['question'] . "'";
                        $oResult = $this->_oDbInstance->query($select);
                        if ($oResult->num_rows == 0) {
                            $insert = "INSERT INTO `sabre`.`pt_quiz_content` (`name`,`content_type`,`quiz_type`,";
                            $insert .= "`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                            $insert .= "VALUES('" . $data['question'] . "', 'Q', '" . $data['quiz_type'] . "', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '1', NOW(), '1', NOW())";
                            $Result = $this->_oDbInstance->query($insert);
                            if ($Result) {
                                $sql = "SELECT `id` FROM `sabre`.`pt_quiz_content` ORDER BY `id` DESC LIMIT 0, 1";
                                $oResult = $this->_oDbInstance->query($sql);
                                while ($oRecord = $oResult->fetch_assoc()) {
                                    $ansCount = count($ansArr['answer']);
                                    for ($i = 0; $i < $ansCount; $i++) {
                                        $insert = "INSERT INTO `sabre`.`pt_quiz_content` (`name`,`content_type`,`quiz_type`,";
                                        $insert .= "`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                                        $insert .= "VALUES('" . $ansArr['answer'][$i] . "', 'A', '" . $data['quiz_type'] . "', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '1', NOW(), '1', NOW())";
                                        $Result = $this->_oDbInstance->query($insert);
                                        if ($Result) {
                                            $sql = "SELECT `id` FROM `sabre`.`pt_quiz_content` ORDER BY `id` DESC LIMIT 0, 1";
                                            $result = $this->_oDbInstance->query($sql);
                                            while ($record = $result->fetch_assoc()) {
                                                if ($data['CorrectAns'] == $i + 1) {
                                                    $results = 'T';
                                                } else {
                                                    $results = 'F';
                                                }
                                                $ins = "INSERT INTO `sabre`.`pt_quiz_meta`(`ques_id`,`ans_id`,`quiz_id`,`course_id`,`topic_id`,`content_id`,`result`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                                                $ins .= " VALUES('" . $oRecord['id'] . "', '" . $record['id'] . "', '" . $data['quiz_type'] . "', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '$results', '1', NOW(), '1', NOW())";
                                                $Res = $this->_oDbInstance->query($ins);
                                            }
                                        }
                                    }
                                }
                            }
                            return array("success" => true);
                        } else {
                            return array("success" => false);
                        }
                    }
                } else {
                    $ansIdArr = array();
                    $metaIdArr = array();

                    $update = "UPDATE `sabre`.`pt_quiz_content` SET `name` = '" . $data['question'] . "', `updated_date` = NOW() WHERE `id` =" . $data['id'];
                    $Res = $this->_oDbInstance->query($update);
                    if ($Res) {
                        $sql = "SELECT `id`, `ans_id` FROM `sabre`.`pt_quiz_meta` WHERE `ques_id` ='" . $data['id'] . "'";
                        $result = $this->_oDbInstance->query($sql);
                        while ($record = $result->fetch_assoc()) {
                            $ansIdArr[] = $record['ans_id'];
                            $metaIdArr[] = $record['id'];
                        }
                        $ansIdCount = count($ansIdArr);
                        $metaIdCount = count($metaIdArr);
                        for ($j = 0; $j < $metaIdCount; $j++) {
                            $updateResult = "UPDATE `sabre`.`pt_quiz_meta` SET `result` = 'F' WHERE `id` = '" . $metaIdArr[$j] . "'";
                            $results = $this->_oDbInstance->query($updateResult);
                        }

                        for ($i = 0; $i < $ansIdCount; $i++) {
                            $update = "UPDATE `sabre`.`pt_quiz_content` SET `name` = '" . $ansArr['answer'][$i] . "', `updated_date` = NOW() WHERE `id` =" . $ansIdArr[$i];
                            $res = $this->_oDbInstance->query($update);
                            if ($res) {
                                if ($data['CorrectAns'] == $i + 1) {
                                    $select = "SELECT `id` FROM `sabre`.`pt_quiz_content` WHERE `name` ='" . $ansArr['answer'][$i] . "'";
                                    $oResult = $this->_oDbInstance->query($select);
                                    while ($oRecord = $oResult->fetch_assoc()) {
                                        $sql = "SELECT `id`, `ans_id` FROM `sabre`.`pt_quiz_meta` WHERE `ans_id` = '" . $oRecord['id'] . "'";
                                        $result = $this->_oDbInstance->query($sql);
                                        while ($record = $result->fetch_assoc()) {
                                            $update = "UPDATE `sabre`.`pt_quiz_meta` SET `result` = 'T', `updated_date` = NOW() WHERE `id` = '" . $record['id'] . "'";
                                            $res = $this->_oDbInstance->query($update);
                                            if ($res) {
                                                return array("success" => true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        return array("success" => false);
                    }
                }
            } else if ($data['quiz_type'] == 3) {
                $corAns = array();
                if (isset($data['CorrectAns1'])) {
                    $corAns[$data['CorrectAns1']] = $data['CorrectAns1'];
                }
                if (isset($data['CorrectAns2'])) {
                    $corAns[$data['CorrectAns2']] = $data['CorrectAns2'];
                }
                if (isset($data['CorrectAns3'])) {
                    $corAns[$data['CorrectAns3']] = $data['CorrectAns3'];
                }
                if (isset($data['CorrectAns4'])) {
                    $corAns[$data['CorrectAns4']] = $data['CorrectAns4'];
                }
                $ansArr = array();
                $ansArr['answer'][] = addslashes($data['answer1']);
                $ansArr['answer'][] = addslashes($data['answer2']);
                $ansArr['answer'][] = addslashes($data['answer3']);
                $ansArr['answer'][] = addslashes($data['answer4']);
                if ($data['id'] == 'new') {
                    if (isset($data['question'])) {
                        $select = "SELECT * FROM `sabre`.`pt_quiz_content` WHERE `name` = '" . $data['question'] . "'";
                        $oResult = $this->_oDbInstance->query($select);
                        if ($oResult->num_rows == 0) {
                            $insert = "INSERT INTO `sabre`.`pt_quiz_content` (`name`,`content_type`,`quiz_type`,";
                            $insert .= "`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                            $insert .= "VALUES('" . $data['question'] . "', 'Q', '" . $data['quiz_type'] . "', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '1', NOW(), '1', NOW())";
                            $Result = $this->_oDbInstance->query($insert);
                            if ($Result) {
                                $sql = "SELECT `id` FROM `sabre`.`pt_quiz_content` ORDER BY `id` DESC LIMIT 0, 1";
                                $oResult = $this->_oDbInstance->query($sql);
                                while ($oRecord = $oResult->fetch_assoc()) {
                                    $ansCount = count($ansArr['answer']);
                                    for ($i = 0; $i < $ansCount; $i++) {
                                        $insert = "INSERT INTO `sabre`.`pt_quiz_content` (`name`,`content_type`,`quiz_type`,";
                                        $insert .= "`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                                        $insert .= "VALUES('" . $ansArr['answer'][$i] . "', 'A', '" . $data['quiz_type'] . "', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '1', NOW(), '1', NOW())";
                                        $Result = $this->_oDbInstance->query($insert);
                                        if ($Result) {
                                            $sql = "SELECT `id` FROM `sabre`.`pt_quiz_content` ORDER BY `id` DESC LIMIT 0, 1";
                                            $result = $this->_oDbInstance->query($sql);
                                            while ($record = $result->fetch_assoc()) {
                                                $ans_id[] = $record;
                                            }
                                        }
                                    }
                                    if (count($corAns) < 0) {
                                        if ($data['CorrectAns'] == $i + 1) {
                                            $results = 'T';
                                        } else {
                                            $results = 'F';
                                        }
                                        echo $ins = "INSERT INTO `sabre`.`pt_quiz_meta`(`ques_id`,`ans_id`,`quiz_id`,`course_id`,`topic_id`,`content_id`,`result`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                                        $ins .= " VALUES('" . $oRecord['id'] . "', '" . $record['id'] . "', '" . $data['quiz_type'] . "', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '$results', '1', NOW(), '1', NOW())";
                                        $Res = $this->_oDbInstance->query($ins);
                                    } else {
                                        $results = array();
                                        $ansCount = count($ansArr['answer']);
                                        for ($z = 0; $z < $ansCount; $z++) {
                                            if (isset($corAns[$z + 1])) {
                                                $results[] = 'T';
                                            } else {
                                                $results[] = 'F';
                                            }
                                        }
                                        $ins = '';
                                        for ($y = 0; $y < count($results); $y++) {
                                            $ins = "INSERT INTO `sabre`.`pt_quiz_meta`(`ques_id`,`ans_id`,`quiz_id`,`course_id`,`topic_id`,`content_id`,`result`,`created_by`,`created_date`,`updated_by`,`updated_date`) VALUES('" . $oRecord['id'] . "', '" . $ans_id[$y]['id'] . "', '" . $data['quiz_type'] . "', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '$results[$y]', '1', NOW(), '1', NOW())";
                                            $Res = $this->_oDbInstance->query($ins);
                                        }
                                    }
                                }
                            }
                            return array("success" => true);
                        } else {
                            return array("success" => false);
                        }
                    }
                } else {

                    $sql = "SELECT `id`, `ans_id` FROM `sabre`.`pt_quiz_meta` WHERE ques_id =" . $data['id'];
                    $oResult = $this->_oDbInstance->query($sql);
                    if ($oResult->num_rows > 0) {
                        while ($oRecord = $oResult->fetch_assoc()) {
                            $answerArr[] = $oRecord;
                        }
                        for ($i = 0; $i < count($answerArr); $i++) {
                            $update = "UPDATE `sabre`.`pt_quiz_meta` SET `result` = 'F' WHERE id =" . $answerArr[$i]['id'];
                            $this->_oDbInstance->query($update);
                            if (isset($corAns[$i + 1])) {
                                $results[] = 'T';
                            } else {
                                $results[] = 'F';
                            }
                        }
                        for ($j = 0; $j < count($results); $j++) {
                            $update = "UPDATE `sabre`.`pt_quiz_meta` SET `result` ='" . $results[$j] . "'  WHERE id =" . $answerArr[$j]['id'];
                            $this->_oDbInstance->query($update);
                        }
                        return array("success" => true);
                    } else {
                        return array("success" => false);
                    }
                }
            }
        }

        public function getQuizType() {
            $quizType = array();
            $sql = "SELECT `id`, `name` FROM `sabre`.`pt_quiz_type` WHERE `deleted` = 'F'";
            $oResult = $this->_oDbInstance->query($sql);
            if ($oResult->num_rows > 0) {
                while ($oRecord = $oResult->fetch_assoc()) {
                    $quizType[] = $oRecord;
                }
            }
            return array("data" => $quizType);
        }

    }

}
?>

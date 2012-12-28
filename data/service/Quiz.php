<?PHP

namespace sabreHcode\data\service {

    class Quiz {

        private $_oDbInstance;

        public function __construct($dbInstance) {

            $this->_oDbInstance = $dbInstance;
        }

        public function getAllSingleQuiz($course_id, $topic_id, $content_id) {
            $course_id = preg_replace('/cou/', '', $course_id);

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
                    $totalArr[$Record['p_id']]['name'] = $Record['p_name'];
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
            // $database = 'sabre';
            //$resultArr = array();
            $corAnsArr = array();
            $selAnsArr = array();
            $quesArr = array();
            $finalArr = array();
            $course_id = preg_replace('/cou/', '', $course_id);
            if (isset($_SESSION['userDetails'])) {
                // $user_id = $_SESSION['userDetails'][0]['id'];
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
            $sql .= "WHERE q1.content_type = 'Q' AND q1.course_id = $course_id AND q1.topic_id = $topic_id AND q1.content_id = $content_id AND q1.quiz_type != 2 ORDER BY `sel_id` ASC"; //GROUP BY cor_ans

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
                $totalArr = array_values($totalArr);
                for ($i = 0; $i < count($totalArr); $i++) {
                    if (count($totalArr[$i]['cor_ans']) == 1 && count($totalArr[$i]['sel_ans']) == 1) {
                        $corAnswer = array_values($totalArr[$i]['cor_ans']);
                        $selAnswer = array_values($totalArr[$i]['sel_ans']);
                        if ($corAnswer[0]['cor_id'] == $selAnswer[0]['sel_id']) {
                            $totalArr[$i]['icon'] = "resources/images/icons/accept.png";
                        } else {
                            $totalArr[$i]['icon'] = "resources/images/icons/cancel.png";
                        }
                    } else {
                        if (count($totalArr[$i]['cor_ans']) == count($totalArr[$i]['sel_ans'])) {
                            $corAnswer = array_values($totalArr[$i]['cor_ans']);
                            $selAnswer = array_values($totalArr[$i]['sel_ans']);
                            for ($j = 0; $j < count($corAnswer); $j++) {
                                if ($corAnswer[$j]['cor_id'] == $selAnswer[$j]['sel_id']) {
                                    $totalArr[$i]['icon'] = "resources/images/icons/accept.png";
                                } else {
                                    $totalArr[$i]['icon'] = "resources/images/icons/cancel.png";
                                }
                            }
                        } else {
                            $totalArr[$i]['icon'] = "resources/images/icons/cancel.png";
                        }
                    }
                }

                $totalArr = array_values($totalArr);
                $totalArrCount = count($totalArr);
                for ($index = 0; $index < $totalArrCount; $index++) {
                    $totalArr[$index]['cor_ans'] = array_values($totalArr[$index]['cor_ans']);
                    $totalArr[$index]['sel_ans'] = array_values($totalArr[$index]['sel_ans']);
                }

                $sSql = "SELECT q1.id AS ques_id, q1.name AS ques_name, q1.quiz_type, a1.id AS cor_id, a1.name AS cor_ans, a2.id AS sel_id, a2.name AS sel_ans, q1.course_id, q1.topic_id, q1.content_id, qp1.result AS final, m1.parent_ques_id AS p_id FROM pt_quiz_content q1 ";
                $sSql .= "LEFT JOIN matching_quiz_meta m1 ON m1.ques_id = q1.id AND q1.course_id = m1.course_id AND q1.topic_id = m1.topic_id AND q1.content_id = m1.content_id ";
                $sSql .= "LEFT JOIN pt_quiz_content a1 ON m1.result_id = a1.id ";
                $sSql .="LEFT JOIN pt_admin_quiz qp1 ON qp1.que_id = q1.id AND q1.course_id = qp1.course_id AND q1.topic_id = qp1.topic_id AND q1.content_id = qp1.content_id ";
                $sSql .="LEFT JOIN pt_quiz_content a2 ON qp1.selected_ans_id = a2.id ";
                $sSql .= "WHERE q1.content_type = 'Q' AND q1.course_id = $course_id AND q1.topic_id = $topic_id AND q1.content_id = $content_id and q1.quiz_type = 2";
                
                $results = $this->_oDbInstance->query($sSql);
                while ($records = $results->fetch_assoc()) {
                    $correctAns = array();
                    $selectAns = array();
                    $mulArr[$records['ques_id']]['ques_id'] = $records['ques_id'];
                    $mulArr[$records['ques_id']]['quiz_type'] = $records['quiz_type'];
                    $mulArr[$records['ques_id']]['ques_name'] = $records['ques_name'];
                    $mulArr[$records['ques_id']]['cor_id'] = $records['cor_id'];
                    $mulArr[$records['ques_id']]['sel_id'] = $records['sel_id'];
                    $mulArr[$records['ques_id']]['course_id'] = $records['course_id'];
                    $mulArr[$records['ques_id']]['topic_id'] = $records['topic_id'];
                    $mulArr[$records['ques_id']]['content_id'] = $records['content_id'];
                    $correctAnsArr[] = $records['cor_id'];
                    $selectAnsArr[] = $records['sel_id'];
                    //$questionArr[] = $records['ques_id'];
                    $correctAns = array(
                        "cor_ans" => $records['cor_ans'],
                        "cor_id" => $records['cor_id'],
                        "quiz_type" => $records['quiz_type'],
                        "course_id" => $records['course_id'],
                        "topic_id" => $records['topic_id'],
                        "content_id" => $records['content_id'],
                        "ques_id" => $records['ques_id']
                    );
                    $selectAns = array(
                        "sel_ans" => $records['sel_ans'],
                        "sel_id" => $records['sel_id'],
                        "quiz_type" => $records['quiz_type'],
                        "course_id" => $records['course_id'],
                        "topic_id" => $records['topic_id'],
                        "content_id" => $records['content_id'],
                        "ques_id" => $records['ques_id']
                    );

                    $mulArr[$records['ques_id']]['cor_ans'][$records['cor_id']] = $correctAns;
                    $mulArr[$records['ques_id']]['sel_ans'][$records['sel_id']] = $selectAns;
                }

                $mulArr = array_values($mulArr);

                for ($i = 0; $i < count($mulArr); $i++) {
                    if ($correctAnsArr[$i] == $selectAnsArr[$i]) {
                        $mulArr[$i]['icon'] = 'resources/images/icons/accept.png';
                    } else {
                        $mulArr[$i]['icon'] = 'resources/images/icons/cancel.png';
                    }
                }
                $mulArr = array_values($mulArr);
                $mulArrCount = count($mulArr);
                for ($index = 0; $index < $mulArrCount; $index++) {
                    $mulArr[$index]['cor_ans'] = array_values($mulArr[$index]['cor_ans']);
                    $mulArr[$index]['sel_ans'] = array_values($mulArr[$index]['sel_ans']);
                }
                $totalArr = array_values($totalArr);
                $mulArr = array_values($mulArr);
                $finalArr = array_merge($totalArr, $mulArr);

                return array("success" => true, "data" => array_values($finalArr));
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
            //$database = 'sabre';
            $course_id = preg_replace('/cou/', '', $course_id);
            $percentage = array();
            $count = '';
            $ansCount = array();
            $total = 100;
            $finalArray = array();
            $totArr = array();
            $mulArr = array();
            $sql = "SELECT t1.`que_id`, t1.`selected_ans_id`, t1.`$field`, t2.`ans_id`, t2.`quiz_id` FROM `$table` t1 LEFT JOIN pt_quiz_meta t2 ON t1.que_id = t2.ques_id WHERE t2.result = 'T' AND t1.`user_id` = '$user_id' AND t1.`course_id` = '$course_id' AND t1.`topic_id` = '$topic_id' AND t1.`content_id` = '$content_id' AND quiz_id != 2 ORDER BY selected_ans_id ASC, ans_id ASC";
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
                    //$selCnt = count($totArr[$quesArr[$i]]['sel_ans']);
                    //$corCut = count($totArr[$quesArr[$i]]['cor_ans']);
                    $selectAns = array_values($totArr[$quesArr[$i]]['sel_ans']);
                    $correctAns = array_values($totArr[$quesArr[$i]]['cor_ans']);

                    if (count($correctAns) == 1 && count($correctAns) < 1) {
                        if ($correctAns[$i]['cor_id'] == $selectAns[$i]['sel_id']) {
                            $totArr[$quesArr[$i]]['percentage'] = "100";
                        } else {
                            $totArr[$quesArr[$i]]['percentage'] = "0";
                        }
                    } else {
                        if (count($correctAns) == count($selectAns)) {
                            for ($j = 0; $j < count($correctAns); $j++) {
                                if ($correctAns[$j]['cor_id'] == $selectAns[$j]['sel_id']) {
                                    $totArr[$quesArr[$i]]['percentage'] = "100";
                                } else {
                                    $totArr[$quesArr[$i]]['percentage'] = "0";
                                }
                            }
                        } else {
                            $totArr[$quesArr[$i]]['percentage'] = "0";
                        }
                    }
                }
                $totArr = array_values($totArr);
                $true = array();
                $sSql = "SELECT t1.que_id, t1.selected_ans_id, t1.result, t1.percentage, t2.parent_ques_id, t2.ques_id, t2.ans_id, t2.result_id FROM pt_admin_quiz t1 ";
                $sSql .= "LEFT JOIN matching_quiz_meta t2 ON t1.que_id = t2.ques_id ";
                $sSql .= "WHERE t2.quiz_id = 2 AND t1.course_id = $course_id AND t1.topic_id = $topic_id AND t1.content_id = $content_id";
                $result = $this->_oDbInstance->query($sSql);
                while ($record = $result->fetch_assoc()) {
                    $mulArr[$record['parent_ques_id']][] = $record;
                }
                $mulArr = array_values($mulArr);
                for ($i = 0; $i < count($mulArr); $i++) {
                    for ($j = 0; $j < count($mulArr[$i]); $j++) {
                        if ($mulArr[$i][$j]['selected_ans_id'] == $mulArr[$i][$j]['result_id']) {
                            $true[] = "100";
                        }
                    }
                    if (isset($true) && count($mulArr[$i]) == count($true)) {
                        $mulArr[$i]['percentage'] = '100';
                    } else {
                        $mulArr[$i]['percentage'] = '0';
                    }
                }
                $totalArr = array_merge($totArr, $mulArr);
                $totArrCount = count($totalArr);
                for ($i = 0, $j = 1; $i < $totArrCount; $i++, $j++) {
                    $percentage[] = $totalArr[$i]['percentage'];
                    if ($totalArr[$i]['percentage'] == "100") {
                        $outOf = $j;
                    }
                }
                $avg = array_sum($percentage) / $totArrCount;
                $i = 0;
                //$outOf = count($ansCount);
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
                if (isset($data['Answer5'])) {
                    $ans[] = $data['Answer5'];
                }

                echo $sql = "SELECT `id` FROM `" . $database . "`" . ".`$table` WHERE `que_id` = " . $data['que_id'];
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
            } else if ($data['quiz_id'] == "2") {
                $ans = array();
                $ques = array();
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
                if (isset($data['Answer5'])) {
                    $ans[] = $data['Answer5'];
                }
                if (isset($data['que_id1'])) {
                    $ques[] = $data['que_id1'];
                }
                if (isset($data['que_id2'])) {
                    $ques[] = $data['que_id2'];
                }
                if (isset($data['que_id3'])) {
                    $ques[] = $data['que_id3'];
                }
                if (isset($data['que_id4'])) {
                    $ques[] = $data['que_id4'];
                }
                if (isset($data['que_id5'])) {
                    $ques[] = $data['que_id5'];
                }
                for ($i = 0; $i < count($ques); $i++) {
                    $sql = "SELECT `result_id` FROM `sabre`.`matching_quiz_meta` WHERE ques_id = '" . $ques[$i] . "'";
                    $results = $this->_oDbInstance->query($sql);
                    while ($records = $results->fetch_assoc()) {
                        if ($ans[$i] == $records['result_id']) {
                            $percentage = "100%";
                            $result = 'T';
                        } else {
                            $percentage = "0%";
                            $result = 'F';
                        }
                        $select = "SELECT * FROM `" . $database . "`" . ".`$table` WHERE `user_id` = '$user_id' AND que_id = '" . $ques[$i] . "'";
                        $oResult = $this->_oDbInstance->query($select);
                        if ($oResult->num_rows == 0) {
                            $insert = "INSERT INTO `" . $database . "`" . ".`$table`";
                            $insert .= "(`user_id`,`que_id`,`selected_ans_id`,`result`, `$field`, `course_id`, `topic_id`, `content_id`, ";
                            $insert .= "`created_by`,`created_date`,`updated_by`,`updated_date`)";
                            $insert .= "VALUES('$user_id','" . $ques[$i] . "','" . $ans[$i] . "','$result','$percentage', '$course_id', '" . $data['topic_id'] . "', '" . $data['content_id'] . "', '$user_id',NOW(),'$user_id',NOW())";
                            $Result = $this->_oDbInstance->query($insert);
                        } else {
                            while ($oRecord = $oResult->fetch_assoc()) {
                                $update = "UPDATE `" . $database . "`" . ".`$table` SET `selected_ans_id` = '" . $ans[$i] . "', `result` = '$result', `$field` = '$percentage', `updated_date` = NOW() WHERE user_id = '$user_id' AND `id` = '" . $oRecord['id'] . "'";
                                $Result = $this->_oDbInstance->query($update);
                            }
                        }
                    }
                }
            }
            return array('success' => true);
        }

        public function AddQuiz($data) {
            $course_id = preg_replace('/cou/', '', $data['course_id']);
            if ($data['quiz_type'] == 4) {
                $data['question'] = addslashes($data['question']);
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
            } else if ($data['quiz_type'] == 3) {
                $data['question'] = addslashes($data['question']);
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
                    $update = "UPDATE `sabre`.`pt_quiz_content` SET `name` = '" . $data['question'] . "', `updated_date` = NOW() WHERE `id` =" . $data['id'];
                    $Res = $this->_oDbInstance->query($update);

                    $answerArr = array();
                    $sql = "SELECT `id`, `ans_id` FROM `sabre`.`pt_quiz_meta` WHERE ques_id =" . $data['id'];
                    $oResult = $this->_oDbInstance->query($sql);
                    if ($oResult->num_rows > 0) {
                        while ($oRecord = $oResult->fetch_assoc()) {
                            $answerArr[] = $oRecord;
                        }
                        for ($i = 0; $i < count($answerArr); $i++) {
                            $update = "UPDATE `sabre`.`pt_quiz_content` SET `name` = '" . $ansArr['answer'][$i] . "', `updated_date` = NOW() WHERE `id` =" . $answerArr[$i]['ans_id'];
                            $res = $this->_oDbInstance->query($update);
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
            } else if ($data['quiz_type'] == 2) {
                $ansArr = array();
                $ansArr['answer'][] = addslashes($data['answer1']);
                $ansArr['answer'][] = addslashes($data['answer2']);
                $ansArr['answer'][] = addslashes($data['answer3']);
                $ansArr['answer'][] = addslashes($data['answer4']);
                $ansArr['answer'][] = addslashes($data['answer5']);
                $quesArr = array();
                $quesArr['question'][] = addslashes($data['ques1']);
                $quesArr['question'][] = addslashes($data['ques2']);
                $quesArr['question'][] = addslashes($data['ques3']);
                $quesArr['question'][] = addslashes($data['ques4']);
                $quesArr['question'][] = addslashes($data['ques5']);
                $corrAnsArr = array();
                $corrAnsArr['c_ans'][] = addslashes($data['c_answer1']);
                $corrAnsArr['c_ans'][] = addslashes($data['c_answer2']);
                $corrAnsArr['c_ans'][] = addslashes($data['c_answer3']);
                $corrAnsArr['c_ans'][] = addslashes($data['c_answer4']);
                $corrAnsArr['c_ans'][] = addslashes($data['c_answer5']);
                if ($data['id'] == "new") {
                    $insert = "INSERT INTO `sabre`.`pt_quiz_content`";
                    $insert .= "(`name`,`content_type`,`quiz_type`,`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                    $insert .= "VALUES('" . $data['p_question'] . "','P','" . $data['quiz_type'] . "','$course_id','" . $data['topic_id'] . "','" . $data['content_id'] . "',1,NOW(),1,NOW())";
                    $Result = $this->_oDbInstance->query($insert);
                    if ($Result) {
                        for ($i = 0; $i < count($quesArr['question']); $i++) {
                            $select = "SELECT * FROM  `sabre`.`pt_quiz_content` WHERE `name` = '" . $quesArr['question'][$i] . "'";
                            $oResult = $this->_oDbInstance->query($select);
                            if ($oResult->num_rows == 0) {
                                $insert = "INSERT INTO `sabre`.`pt_quiz_content`";
                                $insert .= "(`name`,`content_type`,`quiz_type`,`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                                $insert .= "VALUES('" . $quesArr['question'][$i] . "','Q','" . $data['quiz_type'] . "','$course_id','" . $data['topic_id'] . "','" . $data['content_id'] . "',1,NOW(),1,NOW())";
                                $result = $this->_oDbInstance->query($insert);
                            } else {
                                return array("success" => false, "msg" => "Question already exists");
                            }
                        }
                        for ($j = 0; $j < count($ansArr['answer']); $j++) {
                            $insert = "INSERT INTO `sabre`.`pt_quiz_content`";
                            $insert .= "(`name`,`content_type`,`quiz_type`,`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                            $insert .= "VALUES('" . $ansArr['answer'][$j] . "','A','" . $data['quiz_type'] . "','$course_id','" . $data['topic_id'] . "','" . $data['content_id'] . "',1,NOW(),1,NOW())";
                            $results = $this->_oDbInstance->query($insert);
                        }
                        $quesIdArr = array();
                        for ($i = 0; $i < count($quesArr['question']); $i++) {
                            $sel = "SELECT `id` FROM  `sabre`.`pt_quiz_content` WHERE `name` = '" . $quesArr['question'][$i] . "'";
                            $results = $this->_oDbInstance->query($sel);
                            if ($results->num_rows > 0) {
                                while ($records = $results->fetch_assoc()) {
                                    $quesIdArr[] = $records['id'];
                                }
                            }
                            $ans = "SELECT `id` FROM  `sabre`.`pt_quiz_content` WHERE `name` = '" . $ansArr['answer'][$i] . "'";
                            $answer = $this->_oDbInstance->query($ans);
                            if ($answer->num_rows > 0) {
                                while ($answers = $answer->fetch_assoc()) {
                                    $ansIdArr[] = $answers['id'];
                                }
                            }
                            $ans = "SELECT * FROM  `sabre`.`pt_quiz_content` WHERE `name` = '" . $corrAnsArr['c_ans'][$i] . "'";
                            $answer = $this->_oDbInstance->query($ans);
                            if ($answer->num_rows > 0) {
                                while ($answers = $answer->fetch_assoc()) {
                                    $corAnsIdArr[] = $answers['id'];
                                }
                            }
                        }
                        $pSql = "SELECT `id` FROM  `sabre`.`pt_quiz_content` WHERE `name` = '" . $data['p_question'] . "'";
                        $result = $this->_oDbInstance->query($pSql);
                        while ($record = $result->fetch_assoc()) {
                            $parent_id = $record['id'];
                        }
                        for ($a = 0; $a < count($quesIdArr); $a++) {
                            $ins = "INSERT INTO `sabre`.`matching_quiz_meta`";
                            $ins .= "(`parent_ques_id`,`ques_id`,`ans_id`,`result_id`,`quiz_id`,`course_id`,`topic_id`,`content_id`,`created_by`,`created_date`,`updated_by`,`updated_date`)";
                            $ins .= "VALUES('$parent_id','" . $quesIdArr[$a] . "','" . $ansIdArr[$a] . "','" . $corAnsIdArr[$a] . "','" . $data['quiz_type'] . "','$course_id','" . $data['topic_id'] . "','" . $data['content_id'] . "',1,NOW(),1,NOW())";
                            $result = $this->_oDbInstance->query($ins);
                        }
                    }
                    return array("success" => true, "msg" => "Successfully Added.");
                } else {
                    $queArr = array();
                    $update = "UPDATE `sabre`.`pt_quiz_content` SET `name` = '" . $data['p_question'] . "' WHERE id=" . $data['id'];
                    $Res = $this->_oDbInstance->query($update);
                    $select = "SELECT `id`,`ques_id`, `ans_id` FROM `sabre`.`matching_quiz_meta` WHERE parent_ques_id =" . $data['id'];
                    $result = $this->_oDbInstance->query($select);
                    if ($result->num_rows > 0) {
                        while ($record = $result->fetch_assoc()) {
                            $queArr[] = $record['ques_id'];
                            $ansArr[] = $record['ans_id'];
                            $matId[] = $record['id'];
                        }
                        for ($i = 0; $i < count($queArr); $i++) {
                            $update = "UPDATE `sabre`.`pt_quiz_content` SET `name` = '" . $quesArr['question'][$i] . "' WHERE id=" . $queArr[$i];
                            $Res = $this->_oDbInstance->query($update);
                            $upd = "UPDATE `sabre`.`pt_quiz_content` SET `name` = '" . $ansArr['answer'][$i] . "' WHERE id=" . $ansArr[$i];
                            $res = $this->_oDbInstance->query($upd);
                            $ans = "SELECT * FROM  `sabre`.`pt_quiz_content` WHERE `name` = '" . $corrAnsArr['c_ans'][$i] . "'";
                            $answer = $this->_oDbInstance->query($ans);
                            if ($answer->num_rows > 0) {
                                while ($answers = $answer->fetch_assoc()) {
                                    $corAnsIdArr[] = $answers['id'];
                                }
                            }
                        }
                        for($j = 0; $j < count($corAnsIdArr); $j++){
                            $upda = "UPDATE `sabre`.`matching_quiz_meta` SET `result_id` = '".$corAnsIdArr[$j]."' WHERE id = $matId[$j]";
                            $res = $this->_oDbInstance->query($upda);
                        }
                        return array("success" => true);
                    }else{
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

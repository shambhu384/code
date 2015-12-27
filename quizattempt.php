<?php
require_once(dirname(__FILE__) . "/../../../config.php");
require_once(dirname(__FILE__) . "/../common.php");

class quizattempt extends api {

    /**
     * Will be used in submit_scores()
     */
    const WRONGANSWER = 'tnl_wrong_answer';

    public function post($id) {
        return $this->submit_scores($_POST);
    }

    /**
     * Stores user quiz score in database. Required POST parameters are valid token and quizdata
     * @todo Sanitize quizsubmitdata, transaction with try catch block and return success or failure
     */
    private function submit_scores($data) {
        $this->check_required_keys($data, 'quizsubmitdata');
        $submittype = isset($data['submittype']) ? clean_param($data['submittype'], PARAM_RAW) : 1;
        $this->token = $this->validate_token();
        $currenttime = time();
        $quizsubmitdata = json_decode($data['quizsubmitdata'], true);
        $this->tnllogger->debug(__FUNCTION__, $quizsubmitdata);
        $scoreobj = $this->sanitize_score_object($quizsubmitdata['scoreObj']);
        $quizsubmitdata = $this->sanitize_quiz_submit_data($quizsubmitdata);
        $quizattemptdata = $quizsubmitdata['quizattemptdata'];
        $questionsattemptdata = $quizsubmitdata['questionsattemptdata'];
        $quizid = $quizattemptdata['quiz'];
        $quizdetails = (!is_numeric($quizid)) ? array() : $this->get_quiz_details($quizid);
        if (empty($quizdetails)) {
            $testattemptdataresult = $this->insert_tablet_test_attempt_data($this->token->userid, $scoreobj, $quizattemptdata);
            if (is_array($testattemptdataresult) && isset($testattemptdataresult['error'])) {
                return array('responsecode' => $testattemptdataresult['error'],
                    "avgsumgrades" => 'NA', "maxsumgrades" => 'NA', "rank" => 'NA');
            }
            $this->tnllogger->info("quiz {$quizid} does not exists");
            //$quizstats = $this->get_tablet_quiz_attempt_stats($quizid, $this->token->userid);
            $quizstats = array("avgsumgrades" => 'NA', "maxsumgrades" => 'NA', "rank" => "NA"); 
            return array_merge(array('responsecode' => QUIZ_NOT_EXISTS), $quizstats);
        } else {
            $this->insert_tablet_test_attempt_data($this->token->userid, $scoreobj, $quizattemptdata);
        }

        if (!$this->utils->check_course_student($quizdetails->course, $this->token->userid)) {
            $this->tnllogger->error("user is not course student for course {$quizdetails->course} and quiz
          {$quizid} for submit score");
            return array("responsecode" => USER_IS_NOT_COURSE_STUDENT);
        }

        if ($quizdetails->tnl_isblocked) {
            $this->insert_blocked_quiz_sumgrades($quizid, $quizattemptdata['sumgrades'], $currenttime);
            $errormsg = "Score was not submitted as quiz {$quizid} is blocked";
            $this->tnllogger->info($errormsg);
            return array('responsecode' => QUIZ_BLOCKED);
        }

        try {
            $quizquestiondetails = $this->utils->get_quiz_question_details(array($quizid));
            $quizquestiondetails = $quizquestiondetails[$quizid];
            $quizquestions = array();
            foreach ($quizquestiondetails as $quizquestiondetail) {
                $quizquestions[] = $quizquestiondetail['questionid'];
            }
            $quizquestions = implode(",", $quizquestions);

            global $DB;
            $transaction = $DB->start_delegated_transaction();
            $DB->delete_records('quiz_attempts', array('userid' => $this->token->userid, 'quiz' => $quizattemptdata['quiz']));
            $currentattemptid = $this->insert_question_usage();
            $this->insert_quiz_attempt_data($quizquestions, $quizattemptdata, $this->token->userid,
                $currentattemptid, $currenttime);
            $this->insert_questions_attempt_data($questionsattemptdata, $currentattemptid, $currenttime);
            $this->insert_quiz_grade($quizdetails->sumgrades, $quizattemptdata, $this->token->userid);
            $transaction->allow_commit();
            $this->tnllogger->info("quiz score is successfully stored for quizid {$quizid}");
            //$quizstats = $this->get_tablet_quiz_attempt_stats($quizid, $this->token->userid);
            $quizstats = array("avgsumgrades" => 'NA', "maxsumgrades" => 'NA', "rank" => "NA");
            return array_merge(array('responsecode' => SUCCESS), $quizstats);
        } catch (Exception $e) {
            $transaction->rollback($e);
            $this->tnllogger->error("error in submitting score for quiz {$quizid}: $e");
            return array('responsecode' => ERROR_SAVING_IN_DB);
        }
    }

    /*private function get_tablet_quiz_attempt_stats($quizid, $userid) {
        $query = "SELECT AVG(quizattemptdata.sumgrades) as avgsumgrades, 
                         MAX(quizattemptdata.sumgrades) as maxsumgrades
                    FROM {tnl_tablet_quiz_attempt_data} quizattemptdata
                    JOIN {tnl_tablet_usage_data} tabletusagedata
                         ON tabletusagedata.id = quizattemptdata.tabletusagedataid
                   WHERE tabletusagedata.submenuid = ?";
        $quizstats = $this->dbobj->get_record_sql($query, array($quizid));
        $stats = array("avgsumgrades" => 'NA', "maxsumgrades" => 'NA', "rank" => "NA");
        if (!empty($quizstats)) {
            $stats['avgsumgrades'] = number_format($quizstats->avgsumgrades, 2);
            $stats['maxsumgrades'] = number_format($quizstats->maxsumgrades, 2);
        }
        return $stats;
    }*/

    private function insert_questions_attempt_data($questionsattemptdata, $currentattemptid, $timemodified) {
        // Pushing elements in questionids array.
        // By doing so we can get all questions summary and right answers in one db call.

        $questionids = array();
        foreach ($questionsattemptdata as $key => $questionattemptdata) {
            array_push($questionids, $questionattemptdata['questionid']);
        }
        $questionids = array_unique($questionids);
        $questionssummary = $this->get_question_summary($questionids);
        $questionsrightanswer = $this->get_right_answer($questionids);

        $slot = 0;
        $questionsattemptdataobjects = array();
        foreach ($questionsattemptdata as $key => $questionattemptdata) {
            $questionattemptdata = $this->complete_questionattemptdata($questionattemptdata);
            $slot += 1;
            $questionid = $questionattemptdata['questionid'];
            $questionsummary = $questionssummary[$questionid];
            $rightanswer = $questionsrightanswer[$questionid];
            $responsesummary = $questionattemptdata['responsesummary'];
            if (!is_null($rightanswer) && $responsesummary === 1) {
                $responsesummary = $rightanswer;
            } else {
                $responsesummary = self::WRONGANSWER;
            }
            $questionattemptsobject = new stdClass();
            $questionattemptsobject->behaviour = $questionattemptdata['behaviour'];
            $questionattemptsobject->questionid = $questionid;
            $questionattemptsobject->variant = $questionattemptdata['variant'];
            $questionattemptsobject->maxmark = $questionattemptdata['maxmark'];
            $questionattemptsobject->minfraction = $questionattemptdata['minfraction'];
            $questionattemptsobject->flagged = $questionattemptdata['flagged'];
            $questionattemptsobject->timemodified = $timemodified;
            $questionattemptsobject->questionusageid = $currentattemptid;
            $questionattemptsobject->questionsummary = $questionsummary;
            $questionattemptsobject->rightanswer = $rightanswer;
            $questionattemptsobject->responsesummary = $responsesummary;
            $questionattemptsobject->slot = $slot;
            $questionsattemptdataobjects[] = $questionattemptsobject;
        }
        if (!empty($questionsattemptdataobjects)) {
            $this->dbobj->insert_records('question_attempts', $questionsattemptdataobjects);
        }
    }

    private function insert_quiz_grade($quizsumgrades, $quizattemptdata, $userid) {
        $previousgrades = $this->get_quiz_grades_data($quizattemptdata['quiz'], $userid);
        $grade = ($quizattemptdata['sumgrades'] * 100) / $quizsumgrades;
        $quizgradeobj = new stdClass();
        $quizgradeobj->grade = number_format($grade, 5);
        $quizgradeobj->timemodified = time();

        if (!empty($previousgrades)) {
            $quizgradeobj->id = $previousgrades->id;
            $this->dbobj->update_record('quiz_grades', $quizgradeobj);
        } else {
            $quizgradeobj->quiz = $quizattemptdata['quiz'];
            $quizgradeobj->userid = $userid;
            $this->dbobj->insert_record('quiz_grades', $quizgradeobj);
        }
    }

    private function insert_quiz_attempt_data($quizlayout, $quizattemptdata, $userid, $currentattemptid, $currenttime) {
        $quizattemptdata = $this->complete_quizattemptdata($quizattemptdata);
        $quizattemptobject = new stdClass();
        $quizattemptobject->quiz = $quizattemptdata['quiz'];
        $quizattemptobject->layout = $quizlayout;
        $quizattemptobject->sumgrades = $quizattemptdata['sumgrades'];
        $quizattemptobject->state = $quizattemptdata['state'];
        $quizattemptobject->timefinish = $currenttime;
        $quizattemptobject->timestart = $currenttime - $quizattemptdata['totaltimetaken'];
        $quizattemptobject->userid = $userid;
        $quizattemptobject->attempt = $currentattemptid;
        $quizattemptobject->uniqueid = $currentattemptid;

        $this->dbobj->insert_record('quiz_attempts', $quizattemptobject, false);
    }
    private function sanitize_quiz_submit_data($quizsubmitdata) {
        $sanitizeddata = array();
        $sanitizeddata['quizattemptdata']['quiz'] = clean_param($quizsubmitdata['quizattemptdata']['quiz'], PARAM_RAW);
        $sanitizeddata['quizattemptdata']['sumgrades'] = clean_param($quizsubmitdata['quizattemptdata']['sumgrades'], PARAM_FLOAT);
        $sanitizeddata['quizattemptdata']['totaltimetaken'] = clean_param($quizsubmitdata['quizattemptdata']['totaltimetaken'], PARAM_INT);
        $sanitizeddata['quizattemptdata']['timeattempted'] = clean_param($quizsubmitdata['quizattemptdata']['timeattempted'], PARAM_INT);
        $sanitizeddata['questionsattemptdata'] = clean_param_array($quizsubmitdata['questionsattemptdata'], PARAM_INT, true);
        return $sanitizeddata;
    }

    private function sanitize_score_object($scoreobj) {
        $result = array();
        $result['usersumgrades'] = isset($scoreobj['score']) ? clean_param($scoreobj['score'], PARAM_FLOAT) : 0;
        $result['totalcorrect'] = isset($scoreobj['correctAnswer']) ? clean_param($scoreobj['correctAnswer'], PARAM_INT) : 0;
        $result['totalincorrect'] = isset($scoreobj['inCorrectAnswer']) ? clean_param($scoreobj['inCorrectAnswer'], PARAM_INT) : 0;
        $result['totalunattempted'] = isset($scoreobj['unAnswered']) ? clean_param($scoreobj['unAnswered'], PARAM_INT) : 0;
        $result['correctsumgrades'] = isset($scoreobj['correctAnswerScore']) ?
            clean_param($scoreobj['correctAnswerScore'], PARAM_FLOAT) : 0;
        $result['incorrectsumgrades'] = isset($scoreobj['inCorrectAnswerScore']) ?
            clean_param($scoreobj['inCorrectAnswerScore'], PARAM_FLOAT) : 0;
        $result['moduleid'] = isset($scoreobj['moduleId']) ? clean_param($scoreobj['moduleId'], PARAM_RAW) : "";
        $result['submenuid'] = isset($scoreobj['qpId']) ? clean_param($scoreobj['qpId'], PARAM_RAW) : "";
        return $result;
    }

    private function insert_tablet_test_attempt_data($userid, $scoreobj, $quizattemptdata) {
        $moduleid = $scoreobj['moduleid'];
        $submenuid = $scoreobj['submenuid'];
        $tabletusageid = $this->get_tablet_usage_primary_key($userid, $moduleid, $submenuid);
        if (!is_null($tabletusageid)) {
            $obj = new stdClass();
            $obj->tabletusagedataid = $tabletusageid;
            $obj->timetaken = $quizattemptdata['totaltimetaken'];
            $obj->totalcorrect = $scoreobj['totalcorrect'];
            $obj->totalincorrect = $scoreobj['totalincorrect'];
            $obj->totalunattempted = $scoreobj['totalunattempted'];
            $obj->correctsumgrades = $scoreobj['correctsumgrades'];
            $obj->incorrectsumgrades = $scoreobj['incorrectsumgrades'];
            $obj->sumgrades = $scoreobj['usersumgrades'];
            $obj->timeattempted = $quizattemptdata['timeattempted'];
            $obj->timecreated = time();
            return $this->dbobj->insert_record('tnl_tablet_quiz_attempt_data', $obj);
        } else {
            /*
             * Temporary fix. Right now question bank in tablet are not being synced properly.
             * Will change the return code after tablet team will fix the issue
             */
            return array("result" => false, "error" => QUIZ_NOT_EXISTS);
        }
    }

    private function insert_blocked_quiz_sumgrades($quizid, $sumgrades, $time) {
        $obj = new stdClass();
        $obj->userid = $this->token->userid;
        $obj->quiz = $quizid;
        $obj->sumgrades = $sumgrades;
        $obj->timemodified = $time;

        $blockedquizusersumgrades = $this->check_blocked_quiz_sumgrades($quizid, $this->token->userid);
        if ($blockedquizusersumgrades === false) {
            $this->dbobj->insert_record('tnl_blocked_quiz_sumgrades', $obj);
        } else {
            $obj->id = $blockedquizusersumgrades->id;
            $this->dbobj->update_record('tnl_blocked_quiz_sumgrades', $obj);
        }
    }

    private function check_blocked_quiz_sumgrades($quizid, $userid) {
        $rs = $this->dbobj->get_record('tnl_blocked_quiz_sumgrades', array('quiz' => $quizid, 'userid' => $userid));
        if (isset($rs->id)) {
            return $rs;
        }
        return false;
    }

    private function get_quiz_details($quizid) {
        $cacheobj = cache::make('local_tnlmemcache', 'tnlsubmitscorequizdetails');
        $cachedata = $cacheobj->get("quiz:$quizid");
        if ($cachedata) {
            $record = $cachedata;
        } else {
            $record = $this->dbobj->get_record('quiz', array('id' => $quizid), 'id, course, name, sumgrades, tnl_isblocked');
            $cacheobj->set("quiz:$quizid", $record);
            if (!empty($record)) {
                $cacheobj->set("quiz:$quizid", $record);
            }
        }
        return $record;
    }

    private function get_tablet_usage_primary_key($userid, $moduleid, $submenuid) {
        $condition = array('userid' => $userid, 'moduleid' => $moduleid, 'submenuid' => $submenuid);
        $rs = $this->dbobj->get_record('tnl_tablet_usage_data', $condition, 'id');
        if (isset($rs->id)) {
            return $rs->id;
        }
        return null;
    }

    /**
     * Inserts question usages details in database
     *
     * @return int last insert id
     */
    private function insert_question_usage() {
        $queusageobj = new stdClass();
        $queusageobj->contextid = 1;
        $queusageobj->component = 'mod_quiz';
        $queusageobj->preferredbehaviour = 'deferredfeedback';

        $lastinsertid = $this->dbobj->insert_record('question_usages', $queusageobj);
        return $lastinsertid;
    }

    /**
     * Returns complete quizattemptdata array. If some fields are not set in quizattemptdata,
     * this functions set those fields to default values
     *
     * @param array $quizattemptdata Array containing quiz attempt data
     * @return array complete quizattemptdata
     */
    private function complete_quizattemptdata($quizattemptdata) {
        if (!isset($quizattemptdata['state'])) {
            $quizattemptdata['state'] = 'finished';
        }
        return $quizattemptdata;
    }

    /**
     * Returns questions summary in plain text format for questionids
     * @param array $questionids questionids for which summary is needed
     * @return array questionssummary array having key as id and summary as value in plain text format
     */
    private function get_question_summary($questionids) {
        $cacheobj = cache::make('local_tnlmemcache', 'tnlquestions');
        $questionssummary = array();
        $uncachedquestionids = array();
        for ($i = 0; $i < count($questionids); $i++) {
            if ($cacheddata = $cacheobj->get("tnlquestion:$questionids[$i]")) {
                    $questionssummary[$cacheddata['id']] = $cacheddata['questiontext'];
            } else {
                array_push($uncachedquestionids, $questionids[$i]);
            }
        }
        if (!empty($uncachedquestionids)) {
            $questionidscsv = implode(',', $uncachedquestionids);
            $select = "id IN ($questionidscsv)";
            $rs = $this->dbobj->get_recordset_select('question', $select, null, '', 'id, questiontext');
            if (isset($rs) && $rs->valid()) {
                foreach ($rs as $record) {
                    $questionssummary[$record->id] = html_to_text($record->questiontext);
                    $cacheobj->set("tnlquestion:$record->id", array(
                        'id' => $record->id,
                        'questiontext' => html_to_text($record->questiontext)
                    ));
                }
            }
            $rs->close();
        }
        return $questionssummary;
    }

    /**
     * Returns right answers for questionids array
     * @param array $questionids questionids for which right answers are needed
     * @return array rightanswers array having key as question and right answer as value in plain text format
     */
    private function get_right_answer($questionids) {
        $questionidscsv = implode(',', $questionids);
        $select = "question IN ($questionidscsv)";
        $rs = $this->dbobj->get_recordset_select('question_answers', $select, null, '', 'question, answer, fraction');
        $rightanswers = array();
        if (isset($rs) && $rs->valid()) {
            foreach ($rs as $record) {
                if ((int) $record->fraction === 1) {
                    $rightanswers[$record->question] = html_to_text($record->answer);
                }
            }
        }
        $rs->close();
        return $rightanswers;
    }

    /**
     * Returns complete questionattemptdata array. If some fields are not set in questionattemptdata,
     * this functions set those fields to default values
     *
     * @param array $questionattemptdata Array containing question attempt data
     * @return array complete questionattemptdata
     */
    private function complete_questionattemptdata($questionattemptdata) {
        if (!isset($questionattemptdata['variant'])) {
            $questionattemptdata['variant'] = 1;
        }

        if (!isset($questionattemptdata['maxmark'])) {
            $questionattemptdata['maxmark'] = 1;
        }

        if (!isset($questionattemptdata['minfraction'])) {
            $questionattemptdata['minfraction'] = 0;
        }

        if (!isset($questionattemptdata['flagged'])) {
            $questionattemptdata['flagged'] = 0;
        }

        if (!isset($questionattemptdata['behaviour'])) {
            $questionattemptdata['behaviour'] = 'deferredfeedback';
        }
        return $questionattemptdata;
    }

    private function get_quiz_grades_data($quizid, $userid) {
        $record = $this->dbobj->get_record('quiz_grades', array('quiz' => $quizid, 'userid' => $userid));
        return $record;
    }

}

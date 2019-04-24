<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class JudgeAction extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('string');
        $this->load->library('session');
        //$this->load->library('mail');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        $this->load->model('commonModel');
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * Event list.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string  Event list
     * Date:            10-04-2019
     */

    public function list()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $user_id = $this->session->userdata('userId');

        $columns        = 'event_judges.id,events.event_id event_id,event_name,event_date,prelims_date,events.status,event_judges.prelims,events.prelims eventprelims,venue,prelims_venue';
        $condtion       = ['event_judges.judge_id =' => $user_id, 'event_judges.status !=' => DELETED, 'events.status !=' => DELETED];
        $search_value   = NULL;
        $search_like    = NULL;

        $search_keywords    = $this->input->get('search[value]');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            $search_like = array('event_name', 'event_date', 'prelims_date', 'status', 'venue', 'prelims_venue');
        }

        $joins = array(
            array(
                'table' => 'events',
                'condition' => 'event_judges.event_id=events.event_id',
                'jointype' => 'FULL'
            )
        );

        $totalevents = $this->commonModel->selectDataCommon('event_judges', 'event_judges.id', $condtion, $search_value, $search_like, $order_by = NULL, $limit = NULL, $joins);
        $iTotalRecords = $totalevents->num_rows();

        $orderColumn = intval($this->input->get("order[0][column]"));
        $orderDir = $this->input->get("order[0][dir]");
        if (isset($orderColumn) && !empty($orderColumn)) {


            $order_list = array('event_name' => 'event_name', 'date' => 'event_date', 'venue' => 'venue', 'status' => 'events.status', 'prelims' => 'prelims');
            // echo $orderColumn;
            $key = $this->input->get("columns[$orderColumn][data]");
            $order_by['key'] = $order_list[$key];
            $order_by['type'] = $orderDir;
        } else {
            $order_by = NULL;
        }
        $limit = array('start' => $start, 'length' => $length);
        $events = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value, $search_like, $order_by, $limit, $joins);

        $data = array();
        $i = 1;
        if ($events->num_rows() > 0) {
            foreach ($events->result_array() as $row) {

                $data[] = array(
                    'slno' => $i++,
                    'event_name' => $row['event_name'],
                    'eventDate' => $row['event_date'],
                    'prelims_date' => $row['prelims_date'],
                    'eventprelims' => $row['eventprelims'],
                    'prelims' => $row['prelims'],
                    'venue' => $row['venue'],
                    'prelims_venue' => $row['prelims_venue'],
                    'status' => $row['status'],
                    'id' => $row['id']
                );
            }
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $iTotalRecords,
            "recordsFiltered" => $iTotalRecords,
            "data" => $data
        );
        echo json_encode($output);


        //$result = array('success' => true, 'list' => $events);
        //echo json_encode($result);
    }
    /**
     * Get A single entry
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            10-04-2019
     */
    public function getData($id, $roleId)
    {
        $columns        = 'events.event_id,event_name,event_date,prelims_date,status,prelims,venue,prelims_venue,contact_id';
        $condtion       = ['events.event_id' => $id, 'events.status !=' => DELETED];
        $limit = array('start' => 0, 'length' => 1);
        $events = $this->commonModel->selectDataCommon('events', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

        $data = array();
        if ($events->num_rows() > 0) {
            foreach ($events->result_array() as $row) {
                //hint => key - event_name of form field in modelForm
                $data['input'] = array(
                    'eventid'   => $row['event_id'],
                    'event_name' => $row['event_name'],
                    'eventDate'  => $row['event_date'],
                    'prelimsDate' => $row['prelims_date'],
                    'prelims' => $row['prelims'],
                    'venue'     => $row['venue'],
                    'prelimsVenue' => $row['prelims_venue'],
                    'contact_id' => $row['contact_id'],
                );
                $data['withid'] = array(
                    'inputStateedit'     => $row['status'],
                    'prelimsedit'     => $row['prelims']
                );
            }
        }
        $result = array('status' => true, 'data' => $data);
        echo json_encode($result);
    }
    /**
     * Save new event data.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string
     * Date::            10-04-2019
     */

    public function store()
    {
        $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
        $this->form_validation->set_rules('eventId', 'Event details', 'required|strip_tags');
        if ($this->form_validation->run() == false) {
            $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
        } else {
            $invalidRollNo = array();
            $validRollNo = array();
            $judgeId = $this->session->userdata('userId');
            $id   = $this->input->post('eventId');
            $columns        = 'event_judges.event_id,event_judges.prelims';
            $condtion       = ['event_judges.id =' => $id, 'event_judges.judge_id =' => $judgeId, 'event_judges.status !=' => DELETED];
            
            $limit = array('start' => 0, 'length' => 1);
            $events = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

            if ($events->num_rows() > 0) {
                foreach ($events->result_array() as $row) {
                    $prelims = $row['prelims'];
                    $event_id = $row['event_id'];
                }
            }

            $marks = $this->input->post('marks');
            foreach ($marks as $val) {
                $rollNo = $val['rollNo'];
                $marksArray = $val['data'];
                $totalMark = 0;
                foreach(json_decode($marksArray) as $judgement) {
                    $totalMark += $judgement->mark;

                }

                $columns        = 'id contestant_id,company_id';
                $condtion       = ['event_id' => $event_id, 'status !=' => DELETED];
                if ($prelims == 0) {
                    $condtion['roll_no']        = $rollNo;
                } else if ($prelims == 1) {
                    $condtion['prelims_roll_no']        = $rollNo;
                }
                $limit = array('start' => 0, 'length' => 1);
                $contestant = $this->commonModel->selectDataCommon('event_registration', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);
                $contestant_id = '';
                if ($contestant->num_rows() > 0) {
                    foreach ($contestant->result_array() as $row) {
                        $contestant_id = $row['contestant_id'];
                        $company_id = $row['company_id'];
                    }
                }
                if($contestant_id != '') { 
                    $columns        = 'score_id';
                    $condtion       = ['event_id' => $event_id,'contestant_id' => $contestant_id, 'judge_id' => $judgeId, 'prelims' =>$prelims, 'status !=' => DELETED];
                    $limit = array('start' => 0, 'length' => 1);
                    $score_card = $this->commonModel->selectDataCommon('score_card', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);
                    if ($score_card->num_rows() > 0) {
                        foreach ($score_card->result_array() as $row) {
                            $score_id = $row['score_id'];
                        }

                        $data['status'] = DELETED;
                        $condtion           = ['score_id' => $score_id];
                        //user data insertion......
                        $details = $this->commonModel->updateData('score_card', $data, $condtion);
                        
                    }
                    $data = array();
                    $data['event_id'] = $event_id;
                    $data['contestant_id'] = $contestant_id;
                    $data['company_id'] = $company_id;
                    $data['prelims'] = $prelims;
                    $data['judge_id'] = $judgeId;
                    $data['total_score'] = $totalMark;
                    $data['judgement'] = $marksArray;
                    $data['status'] = ACTIVE;
                    $data['created_by'] = $judgeId;
                    $data['created_at'] = date("Y-m-d H:i:s");
                    $details = $this->commonModel->insertData('score_card', $data);
                    $validRollNo[] = $rollNo;
                }
                else {
                    $invalidRollNo[] = $rollNo;
                }
            }
            $result = array('status' => true, 'msg' => 'Successfully Added', 'reset' => true, 'validRollNo' => $validRollNo, 'invalidRollNo' => $invalidRollNo);
            
            
            
        }
        echo json_encode($result);
    }

    /**
     * Check if judge can access the event.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            21-04-2019
     */
    public function checkEvent()
    {
        $result = array('status' => false);
        if ($this->input->post('eventid') != '') {
            $id   = $this->input->post('eventid');
            $user_id = $this->session->userdata('userId');
            $roleId = $this->session->userdata('roleId');
            $columns        = 'events.event_id,event_name,events.status,event_judges.prelims,events.prelims eventprelims';
            $condtion       = ['event_judges.id =' => $id, 'event_judges.judge_id =' => $user_id, 'event_judges.status !=' => DELETED, 'events.status !=' => DELETED];
            $joins = array(
                array(
                    'table' => 'events',
                    'condition' => 'event_judges.event_id=events.event_id',
                    'jointype' => 'FULL'
                )
            );
            $limit = array('start' => 0, 'length' => 1);
            $events = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins);

            if ($events->num_rows() > 0) {
                foreach ($events->result_array() as $row) {
                    $prelims = $row['prelims'];
                    $eventprelims = $row['eventprelims'];
                    $status = $row['status'];
                    $event_id = $row['event_id'];
                }
                if ($roleId == JUDGE && $status == '1' && (($prelims == 1 && $eventprelims == 1) || ($prelims == 0 && ($eventprelims == 0 || $eventprelims == 2)))) {
                        $columns        = 'id,criteria,max_mark';
                        $condtion       = ['judgement_criteria.event_id' => $event_id, 'judgement_criteria.status' => ACTIVE];
                        $criteria = $this->commonModel->selectDataCommon('judgement_criteria', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit = NULL, $joins = NULL);

                        $result = array('status' => true, 'criteria' => $criteria->result_array());
                    }
            }
        }
        echo json_encode($result);
    }

    /**
     * Event list.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string  Event list
     * Date:            10-04-2019
     */

    public function judgementList()
    {
        $user_id = $this->session->userdata('userId');
        $id   = $this->input->get('eventid');

        $columns        = 'event_judges.event_id,event_judges.prelims';
        $condtion       = ['event_judges.id =' => $id, 'event_judges.judge_id =' => $user_id, 'event_judges.status !=' => DELETED];

        $limit = array('start' => 0, 'length' => 1);
        $events = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

        if ($events->num_rows() > 0) {
            foreach ($events->result_array() as $row) {
                $prelims = $row['prelims'];
                $event_id = $row['event_id'];
            }
            if ($prelims == 0) {
                    $columns        = 'score_card.score_id,score_card.judgement, event_registration.roll_no';
                } else if ($prelims == 1) {
                    $columns        = 'score_card.score_id,score_card.judgement, event_registration.prelims_roll_no roll_no';
                }

            $condtion       = ['score_card.event_id' => $event_id, 'judge_id' => $user_id, 'score_card.prelims' => $prelims, 'score_card.status !=' => DELETED];
            $search_value   = NULL;
            $search_like    = NULL;

            $joins = array(
                array(
                    'table' => 'event_registration',
                    'condition' => 'event_registration.id=score_card.contestant_id',
                    'jointype' => 'FULL'
                )
            );

            $events = $this->commonModel->selectDataCommon('score_card', $columns, $condtion, $search_value, $search_like, $order_by = NULL, $limit = NULL, $joins);
            $data = array();
            $i = 1;
            if ($events->num_rows() > 0) {
                foreach ($events->result_array() as $row) {

                    $data[] = array(
                        'slno' => $i++,
                        'roll_no' => $row['roll_no'],
                        'id' => $row['score_id'],
                        'judgement' => $row['judgement']
                    );
                }
            }

            $result = array('status' => true, 'data' => $data);
        } else {

            $result = array('status' => false);
        }
        echo json_encode($result);
    }

    public function judgementListOld()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $user_id = $this->session->userdata('userId');
        $id   = $this->input->get('eventid');

        $columns        = 'event_judges.event_id,event_judges.prelims';
        $condtion       = ['event_judges.id =' => $id, 'event_judges.judge_id =' => $user_id, 'event_judges.status !=' => DELETED];

        $limit = array('start' => 0, 'length' => 1);
        $events = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

        if ($events->num_rows() > 0) {
            foreach ($events->result_array() as $row) {
                $prelims = $row['prelims'];
                $event_id = $row['event_id'];
            }
            if ($prelims == 0) {
                    $columns        = 'score_card.score_id, event_registration.roll_no';
                } else if ($prelims == 1) {
                    $columns        = 'score_card.score_id, event_registration.prelims_roll_no roll_no';
                }

            $condtion       = ['score_card.event_id' => $event_id, 'score_card.prelims' => $prelims, 'score_card.status !=' => DELETED];
            $search_value   = NULL;
            $search_like    = NULL;

            $search_keywords    = $this->input->get('search[value]');
            if (isset($search_keywords) && !empty($search_keywords)) {
                $search_value = $search_keywords;
                $search_like = array('roll_no');
            }

            $joins = array(
                array(
                    'table' => 'event_registration',
                    'condition' => 'event_registration.id=score_card.contestant_id',
                    'jointype' => 'FULL'
                )
            );

            $totalevents = $this->commonModel->selectDataCommon('score_card', 'score_card.score_id', $condtion, $search_value, $search_like, $order_by = NULL, $limit = NULL, $joins);
            $iTotalRecords = $totalevents->num_rows();

            $orderColumn = intval($this->input->get("order[0][column]"));
            $orderDir = $this->input->get("order[0][dir]");
            if (isset($orderColumn) && !empty($orderColumn)) {


                $order_list = array('roll_no' => 'roll_no');
                // echo $orderColumn;
                $key = $this->input->get("columns[$orderColumn][data]");
                $order_by['key'] = $order_list[$key];
                $order_by['type'] = $orderDir;
            } else {
                $order_by = NULL;
            }
            $limit = array('start' => $start, 'length' => $length);
            $events = $this->commonModel->selectDataCommon('score_card', $columns, $condtion, $search_value, $search_like, $order_by, $limit, $joins);

            $data = array();
            $i = 1;
            if ($events->num_rows() > 0) {
                foreach ($events->result_array() as $row) {

                    $data[] = array(
                        'slno' => $i++,
                        'roll_no' => $row['roll_no'],
                        'id' => $row['score_id']
                    );
                }
            }

            $output = array(
                "draw" => $draw,
                "recordsTotal" => $iTotalRecords,
                "recordsFiltered" => $iTotalRecords,
                "data" => $data
            );
        } else {

            $output = array(
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array()
            );
        }




        echo json_encode($output);


        //$result = array('success' => true, 'list' => $events);
        //echo json_encode($result);
    }

    public function checkconnection()
    {
        $result = array('status' => true);
        echo json_encode($result);
    }
}

/* End of file User Controller*/

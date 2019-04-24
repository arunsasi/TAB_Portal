<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller
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
        $roleId = $this->session->userdata('roleId');

        $columns        = 'events.event_id,event_name,event_date,prelims_date,status,prelims,venue,prelims_venue';
        $condtion       = ['status !=' => DELETED];

        if($roleId != ADMIN)
        {
            $condtion['contact_id'] = $roleId;
        }
        $search_value   = NULL;
        $search_like    = NULL;

        $search_keywords    = $this->input->get('search[value]');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            $search_like = array('event_name', 'event_date', 'prelims_date', 'status', 'venue', 'prelims_venue');
        }


        $totalevents = $this->commonModel->selectDataCommon('events', 'events.event_id', $condtion, $search_value, $search_like, $order_by = NULL, $limit = NULL, $joins = NULL);
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
        $events = $this->commonModel->selectDataCommon('events', $columns, $condtion, $search_value, $search_like, $order_by, $limit, $joins);

        $data = array();
        $i = 1;
        if ($events->num_rows() > 0) {
            foreach ($events->result_array() as $row) {

                $data[] = array(
                    'slno' => $i++,
                    'event_name' => $row['event_name'],
                    'eventDate' => $row['event_date'],
                    'prelims_date' => $row['prelims_date'],
                    'prelims' => $row['prelims'],
                    'venue' => $row['venue'],
                    'prelims_venue' => $row['prelims_venue'],
                    'status' => $row['status'],
                    'id' => $row['event_id']
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
        $this->form_validation->set_rules('event_name', 'Event name', 'required|strip_tags');
        if ($this->form_validation->run() == false) {
            $result = array('status' => false, 'error' => $this->form_validation->error_array(), 'reset' => false);
        } else {
            $data['event_name'] = $this->input->post('event_name');
            $data['event_date'] = $this->input->post('eventDate');
            $data['contact_id'] = $this->input->post('contact');
            $data['prelims'] = $this->input->post('prelims');
            $data['venue'] = trim($this->input->post('venue'));
            $data['status'] = $this->input->post('inputState');
            $data['created_date'] = date("Y-m-d H:i:s");
            if ($data['prelims'] != 0) {
                $data['prelims_venue'] = $this->input->post('prelimsVenue');
                $data['prelims_date'] = $this->input->post('prelimsDate');
            }
            //user data insertion......
            $details = $this->commonModel->insertData('events', $data);
            if ($details > 0) {
                $result = array('status' => true, 'msg' => 'Successfully Added', 'reset' => true);
                /// redirect('login');
            } else {
                $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
            }
        }
        echo json_encode($result);
    }

    /**
     * Update event data.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            10-04-2019
     */

    public function edit()
    {
        if ($this->input->post('eventid') != '') {
            $id   = $this->input->post('eventid');
            $this->form_validation->set_rules('event_name', 'Event name', 'required|strip_tags');
            if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array(), 'reset' => false);
            } else {

                $data['event_name'] = $this->input->post('event_name');
                $data['event_date'] = $this->input->post('eventDate');
                $data['venue'] = trim($this->input->post('venue'));
                $data['prelims'] = $this->input->post('prelims');
                $data['status'] = $this->input->post('inputState');
                if ($data['prelims'] != 0) {
                    $data['prelims_venue'] = $this->input->post('prelimsVenue');
                    $data['prelims_date'] = $this->input->post('prelimsDate');
                } else {
                    $data['prelims_venue'] = NULL;
                    $data['prelims_date'] = NULL;
                }

                $condtion           = ['event_id' => $id];
                //user data insertion......
                $details = $this->commonModel->updateData('events', $data, $condtion);
                if ($details) {
                    $result = array('status' => true, 'msg' => 'Successfully updated.');
                } else {
                    $result = array('status' => true, 'msg' => 'Nothing to update.');
                }
            }
        } else {
            $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
        }
        echo json_encode($result);
    }

    /**
     * Delete event data
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date:            10-04-2019
     */

    public function destroy()
    {
        if ($this->input->post('id') != '') {
            $id   = $this->input->post('id');
            $result = array('status' => false, 'msg' => 'Something went wrong.');
            $condtion           = ['event_id' => $id];
            $data['status']      = DELETED;
            //user data deletion......
            $details = $this->commonModel->updateData('events', $data, $condtion);
            if ($details) {
                $result = array('status' => true, 'msg' => 'Success', 'reload' => true);
            }
        }
        echo json_encode($result);
    }
    /**
     * Fetch company list
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date:            13-04-2019
     */

    public function getCoordinatorData()
    {
        $columns        = 'user_id as id,name as value';
        $condtion           = ['role_id' => USER, 'status !=' => DELETED];
        $list = $this->commonModel->selectDataCommon('tab_user', $columns, $condtion);

        $data = array();
        if ($list->num_rows() > 0) {
            $data = $list->result_array();
        }
        $result = array('status' => true, 'data' => $data);
        echo json_encode($result);
    }

    /**
     * Get events assigned and unassigned judes list 
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string
     * Date::            15-04-2019
     */

    public function getAssignJudges()
    {
        if ($this->input->post('eventid') != '') {
            $id   = $this->input->post('eventid');
            $columns        = 'events.event_id,event_name,status,prelims';
            $condtion       = ['events.event_id' => $id, 'events.status !=' => DELETED];
            $limit = array('start' => 0, 'length' => 1);
            $events = $this->commonModel->selectDataCommon('events', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

            $data = array();
            if ($events->num_rows() > 0) {
                foreach ($events->result_array() as $row) {

                    $data['event_name'] = $row['event_name'];
                    $prelims = $row['prelims'];
                    $status = $row['status'];
                }
                $data['prelimsdisable'] = '';
                $data['finalsdisable'] = '';
                if($prelims == 0 && ($status == 2 || $status == 3))
                {
                    $data['finalsdisable'] = 'disabled';
                }
                elseif($prelims == 1 && ($status == 2 || $status == 3))
                {
                    $data['prelimsdisable'] = 'disabled';
                }
                elseif($prelims == 2 && ($status == 0 || $status == 1))
                {
                    $data['prelimsdisable'] = 'disabled';
                }
                elseif($prelims == 2 && ($status == 2 || $status == 3))
                {
                    $data['prelimsdisable'] = 'disabled';
                    $data['finalsdisable'] = 'disabled';
                }

                if ($prelims != 0) {
                    //Have Prelims
                    $data['prelimsJudges'] = array();
                    //Get Prelims Assigned Judges (if any)
                    $columns        = 'event_judges.judge_id as id,tab_user.name as value';
                    $condtion       = ['event_judges.event_id' => $id, 'event_judges.prelims' => 1, 'event_judges.status !=' => DELETED];
                    $joins = array(
                        array(
                            'table' => 'tab_user',
                            'condition' => 'tab_user.user_id=event_judges.judge_id and tab_user.role_id =' . JUDGE . ' and tab_user.status!=' . DELETED,
                            'jointype' => 'FULL'
                        )
                    );
                    $prAssignedJudges = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit = NULL, $joins);
                    if ($prAssignedJudges->num_rows() > 0) {

                        $data['prelimsJudges']['assigned'] = $prAssignedJudges->result_array();
                        foreach ($prAssignedJudges->result_array() as $row) {
                            $prAssigned[] = $row['id'];
                        }
                    }

                    //Get Prelims Unassigned Judges (if any)
                    $columns        = 'tab_user.user_id as id,tab_user.name as value';
                    $condtion       = ['tab_user.role_id' => JUDGE, 'tab_user.status !=' => DELETED];
                    if (isset($prAssigned)) {
                        $where_not_in = 'user_id';
                        $where_not_in_data = $prAssigned;
                    } else {
                        $where_not_in = NULL;
                        $where_not_in_data = NULL;
                    }

                    $prUnassignedJudges = $this->commonModel->selectDataCommon('tab_user', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit = NULL, $joins = NULL, $where_in = NULL, $where_in_data = NULL, $where_not_in, $where_not_in_data);
                    if ($prUnassignedJudges->num_rows() > 0) {
                        $data['prelimsJudges']['unassigned'] = $prUnassignedJudges->result_array();
                    }
                }
                $data['finalsJudges'] = array();
                //Get Finals Assigned Judges (if any)
                $columns        = 'event_judges.judge_id as id,tab_user.name as value';
                $condtion       = ['event_judges.event_id' => $id, 'event_judges.prelims' => 0, 'event_judges.status !=' => DELETED];
                $joins = array(
                    array(
                        'table' => 'tab_user',
                        'condition' => 'tab_user.user_id=event_judges.judge_id and tab_user.role_id =' . JUDGE . ' and tab_user.status!=' . DELETED,
                        'jointype' => 'FULL'
                    )
                );
                $fnAssignedJudges = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit = NULL, $joins);
                if ($fnAssignedJudges->num_rows() > 0) {

                    $data['finalsJudges']['assigned'] = $fnAssignedJudges->result_array();
                    foreach ($fnAssignedJudges->result_array() as $row) {
                        $fnAssigned[] = $row['id'];
                    }
                }

                //Get Finals Unassigned Judges (if any)
                $columns        = 'tab_user.user_id as id,tab_user.name as value';
                $condtion       = ['tab_user.role_id' => JUDGE, 'tab_user.status !=' => DELETED];
                if (isset($fnAssigned)) {
                    $where_not_in = 'user_id';
                    $where_not_in_data = $fnAssigned;
                } else {
                    $where_not_in = NULL;
                    $where_not_in_data = NULL;
                }

                $fnUnassignedJudges = $this->commonModel->selectDataCommon('tab_user', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit = NULL, $joins = NULL, $where_in = NULL, $where_in_data = NULL, $where_not_in, $where_not_in_data);
                if ($fnUnassignedJudges->num_rows() > 0) {
                    $data['finalsJudges']['unassigned'] = $fnUnassignedJudges->result_array();
                }
                $result = array('status' => true, 'data' => $data);
            } else {
                $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
            }
        } else {
            $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
        }
        echo json_encode($result);
    }

    /**
     * Manage events assigned and unassigned judes list 
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string
     * Date::            15-04-2019
     */

    public function editAssignJudges()
    {
        if ($this->input->post('eventid') != '') {
            $event_id = $this->input->post('eventid');
            $haveprelims        = $this->input->post('haveprelims');
            $havefinals         = $this->input->post('havefinals');
            $prelimsdisable     = trim($this->input->post('prelimsdisable'));
            $finalsdisable      = $this->input->post('finalsdisable');
            $finalsJudges      = $this->input->post('finalsJudges');
            $prelimsJudges      = $this->input->post('prelimsJudges');

            if($haveprelims == 1 && $prelimsdisable == '')
            {
                $data['status'] = DELETED;
                $condtion           = ['event_id' => $event_id, 'prelims' => 1, 'status !=' =>DELETED];
                $details = $this->commonModel->updateData('event_judges', $data, $condtion);
                if(!empty($prelimsJudges))
                {
                    foreach($prelimsJudges as $value)
                    {
                        $columns        = 'id';
                        $condtion       = ['event_judges.event_id' => $event_id, 'event_judges.prelims' => 1, 'event_judges.judge_id' => $value];
                        $limit = array('start' => 0, 'length' => 1);
                        $judgeExist = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

                        if ($judgeExist->num_rows() > 0) {
                            foreach ($judgeExist->result_array() as $row) {
                                $data = array();
                                $data['status'] = ACTIVE;
                                $condtion           = ['id' => $row['id']];
                                $details = $this->commonModel->updateData('event_judges', $data, $condtion);
                            }
                        }
                        else{
                            $data = array();
                            $data['judge_id'] = $value;
                            $data['event_id'] = $event_id;
                            $data['prelims'] = 1;
                            $data['status'] = ACTIVE;
                            $details = $this->commonModel->insertData('event_judges', $data);
                        }
                    }
                }
            }
            if($havefinals == 1 && $finalsdisable == '' )
            {
                $data = array();
                $data['status'] = DELETED;
                $condtion           = ['event_id' => $event_id, 'prelims' => 0, 'status !=' =>DELETED];
                $details = $this->commonModel->updateData('event_judges', $data, $condtion);
                if(!empty($finalsJudges))
                {
                    foreach($finalsJudges as $value)
                    {
                        $columns        = 'id';
                        $condtion       = ['event_judges.event_id' => $event_id, 'event_judges.prelims' => 0, 'event_judges.judge_id' => $value];
                        $limit = array('start' => 0, 'length' => 1);
                        $judgeExist = $this->commonModel->selectDataCommon('event_judges', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

                        if ($judgeExist->num_rows() > 0) {
                            foreach ($judgeExist->result_array() as $row) {
                                $data = array();
                                $data['status'] = ACTIVE;
                                $condtion           = ['id' => $row['id']];
                                $details = $this->commonModel->updateData('event_judges', $data, $condtion);
                            }
                        }
                        else{
                            $data = array();
                            $data['judge_id'] = $value;
                            $data['event_id'] = $event_id;
                            $data['prelims'] = 0;
                            $data['status'] = ACTIVE;
                            $details = $this->commonModel->insertData('event_judges', $data);
                        }
                    }
                }
            }
            $result = array('status' => true, 'msg' => 'Successfully updated.');
        } else {
            $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
        }
        echo json_encode($result);

    }
    /**
     * Fetch company list
     * @author          RJ
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date:            15-04-2019
     */

    public function getJudgementCriteria()
    {
        $id = $this->input->post('event');
        $columns = 'criteria,max_mark';
        $condtion = ['event_id' => $id, 'status' => 1];
        $list = $this->commonModel->selectDataCommon('judgement_criteria', $columns, $condtion);

        $data = array();
        if ($list->num_rows()>0) {
            $data = $list->result_array();
        }
        $result = array('status' => 'success','data' => $data);
        echo json_encode($result);
        exit;
    }

    /**
     * Fetch company list
     * @author          RJ
     * @since           Version 1.0.0
     * @param array
     * @return string
     * Date:            17-04-2019
     */

    public function updateCriteria()
    {
        $postData = $this->input->post('postData');
        $dataArray = json_decode($postData, true);
        $delData = array("status" => 0);
        $cond = array("event_id"=>$dataArray['eventId'], "status" => 1);
        $sel = $this->commonModel->selectDataCommon('judgement_criteria', 'count(*) AS cnt', $cond)->row_array();
        if($sel['cnt'] > 0){
            $del = $this->commonModel->updateData('judgement_criteria', $delData, $cond);
            $insertData = array();
            if($del){
                foreach ($dataArray['criteria'] as $key => $value) {
                    $insertData[$key]['event_id'] = $dataArray['eventId'];
                    $insertData[$key]['criteria'] = $value;
                    $insertData[$key]['max_mark'] = $dataArray['max_mark'][$key];
                    $insertData[$key]['status'] = 1; //active
                    $insertData[$key]['created_date'] = date("Y-m-d H:i:s");
                }
                $res = $this->commonModel->batch_insert('judgement_criteria',$insertData);
                if($res){
                    echo json_encode(array('status' => 'success', 'data' => 'Updated Successfully!'));
                } else {
                    echo json_encode(array('status' => 'failed', 'data' => 'Update failed!'));
                }
                exit;
            } else {
                echo json_encode(array('status'=>'failed','data'=>'Update failed!'));
                exit;
            }
        } else {
            foreach ($dataArray['criteria'] as $key => $value) {
                $insertData[$key]['event_id'] = $dataArray['eventId'];
                $insertData[$key]['criteria'] = $value;
                $insertData[$key]['max_mark'] = $dataArray['max_mark'][$key];
                $insertData[$key]['status'] = 1; //active
                $insertData[$key]['created_date'] = date("Y-m-d H:i:s");
            }
            $res = $this->commonModel->batch_insert('judgement_criteria',$insertData);
            if($res){
                echo json_encode(array('status' => 'success', 'data' => 'Updated Successfully!'));
            } else {
                echo json_encode(array('status' => 'failed', 'data' => 'Update failed!'));
            }
            exit;
        }


        $list = $this->commonModel->selectDataCommon('judgement_criteria', $columns, $condtion);

        $data = array();
        if ($list->num_rows()>0) {
            $data = $list->result_array();
        }
        $result = array('status' => true,'data' => $data);
        echo json_encode($result);
    }

    /**
     * Upload Judgement sheets
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string
     * Date::            23-04-2019
     */
    public function uploadfile()
    {
        $this->load->helper('upload');
        upload();
    }
}

/* End of file User Controller*/

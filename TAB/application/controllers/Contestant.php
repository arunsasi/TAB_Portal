<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contestant extends CI_Controller
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
     * list.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string  Event list
     * Date:            14-04-2019
     */

    public function list()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $eventId = intval($this->input->get("eventId"));

        $columns        = 'event_registration.id,event_registration.event_id,name,email,contact_no,prelims_roll_no,roll_no,company_name,event_name,event_registration.status';
        $condtion       = ['event_registration.event_id =' => $eventId, 'event_registration.status !=' => DELETED];
        $search_value   = null;
        $search_like    = null;

        $search_keywords    = $this->input->get('search[value]');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            $search_like = array('name', 'email', 'contact_no', 'prelims_roll_no', 'roll_no', 'company_name');
        }

        $joins = array(
            array(
                'table' => 'events',
                'condition' => 'events.event_id=event_registration.event_id',
                'jointype' => 'FULL'
            ),
            array(
                'table' => 'company_list',
                'condition' => 'company_list.company_id=event_registration.company_id',
                'jointype' => 'LEFT'
            )
        );

        $totalevents = $this->commonModel->selectDataCommon('event_registration', 'event_registration.id', $condtion, $search_value, $search_like, $order_by = NULL, $limit = NULL, $joins);
        $iTotalRecords = $totalevents->num_rows();

        $orderColumn = intval($this->input->get("order[0][column]"));
        $orderDir = $this->input->get("order[0][dir]");
        if (isset($orderColumn) && !empty($orderColumn)) {


            $order_list = array('name' => 'name', 'email' => 'email', 'contact_no' => 'contact_no', 'prelims_roll_no' => 'prelims_roll_no', 'roll_no' => 'roll_no', 'company_name' => 'company_name', 'event_name' => 'event_name');
            // echo $orderColumn;
            $key = $this->input->get("columns[$orderColumn][data]");
            $order_by['key'] = $order_list[$key];
            $order_by['type'] = $orderDir;
        } else {
                $order_by = NULL;
            }

        $limit = array('start' => $start, 'length' => $length);
        $events = $this->commonModel->selectDataCommon('event_registration', $columns, $condtion, $search_value, $search_like, $order_by, $limit, $joins);

        $data = array();
        $i = 1;
        if ($events->num_rows() > 0) {
            foreach ($events->result_array() as $row) {

                $data[] = array(
                    'slno' => $i++,
                    'event_name' => $row['event_name'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'contact_no' => $row['contact_no'],
                    'prelims_roll_no' => $row['prelims_roll_no'],
                    'roll_no' => $row['roll_no'],
                    'company_name' => $row['company_name'],
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
    }
    /**
     * Get A single entry
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            14-04-2019
     */
    public function getData($id)
    {
        $columns        = 'event_registration.id,event_registration.event_id,name,email,contact_no,prelims_roll_no,roll_no,company_id,event_registration.status';
        $condtion       = ['event_registration.id' => $id, 'event_registration.status !=' => DELETED];
        $limit = array('start' => 0, 'length' => 1);
        $contestant = $this->commonModel->selectDataCommon('event_registration', $columns, $condtion, $search_value = NULL, $search_like = NULL, $order_by = NULL, $limit, $joins = NULL);

        $data = array();
        if ($contestant->num_rows() > 0) {
            foreach ($contestant->result_array() as $row) {
                //hint => key - event_name of form field in modelForm
                $data['input'] = array(
                    'contestant_id'   => $row['id'],
                    'memberName' => $row['name'],
                    'email'  => $row['email'],
                    'contact_no' => $row['contact_no'],
                    'prelimsRollno' => $row['prelims_roll_no'],
                    'rollno'     => $row['roll_no'],
                    'company_id' => $row['company_id']
                );
                $data['withid'] = array(
                    'inputStateedit'     => $row['status']
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
     * Date::            14-04-2019
     */

    public function store()
    {
        if ($this->input->post('eventid') != '') {
                $this->form_validation->set_rules('memberName', 'Name', 'required|strip_tags');
                $this->form_validation->set_rules('contact_no', 'Mobile', 'strip_tags');
                $this->form_validation->set_rules('email', 'Email', 'strip_tags|valid_email');

                if ($this->form_validation->run() == false) {
                    $result = array('status' => false, 'error' => $this->form_validation->error_array(), 'reset' => false);
                } else {
                    $data['event_id']   = $this->input->post('eventid');
                    $data['name'] = $this->input->post('memberName');
                    $data['contact_no'] = $this->input->post('contact_no');
                    $data['email'] = trim($this->input->post('email'));
                    $data['prelims_roll_no'] = md5($this->input->post('prelimsRollno'));
                    $data['roll_no'] = md5($this->input->post('rollno'));
                    $data['status'] = ACTIVE;
                    $data['created_date'] = date("Y-m-d H:i:s");

                    //contestant data insertion......
                    $details = $this->commonModel->insertData('event_registration', $data);
                    if ($details > 0) {
                        $result = array('status' => true, 'msg' => 'Successfully Added', 'reset' => true);
                    } else {
                        $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
                    }
                }
            } else {
                $result = array('status' => false, 'msg' => 'Something went wrong.', 'reset' => false);
            }
        echo json_encode($result);
    }

    /**
     * Update event data.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            14-04-2019
     */

    public function edit()
    {
        if ($this->input->post('contestant_id') != '') {
                $id   = $this->input->post('contestant_id');
                $this->form_validation->set_rules('memberName', 'Name', 'required|strip_tags');
                if ($this->form_validation->run() == false) {
                    $result = array('status' => false, 'error' => $this->form_validation->error_array(), 'reset' => false);
                } else {
                    $data['name'] = $this->input->post('memberName');
                    $data['contact_no'] = $this->input->post('contact_no');
                    $data['prelims_roll_no'] = $this->input->post('prelimsRollno');
                    $data['roll_no'] = trim($this->input->post('rollno'));
                    $data['email'] = $this->input->post('email');
                    $data['company_id'] = $this->input->post('company');
                    $data['status'] = $this->input->post('inputState');
                    $condtion           = ['id' => $id];
                    //user data insertion......
                    $details = $this->commonModel->updateData('event_registration', $data, $condtion);
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
     * Date:            14-04-2019
     */

    public function destroy()
    {
        if ($this->input->post('id') != '') {
                $id   = $this->input->post('id');
                $result = array('status' => false, 'msg' => 'Something went wrong.');
                $condtion           = ['id' => $id];
                $data['status']      = DELETED;
                //user data deletion......
                $details = $this->commonModel->updateData('event_registration', $data, $condtion);
                if ($details) {
                    $result = array('status' => true, 'msg' => 'Success', 'reload' => true);
                }
            }
        echo json_encode($result);
    }
}

/* End of file User Controller*/

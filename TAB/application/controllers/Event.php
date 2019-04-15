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
        $roleId = intval($this->input->get("role"));
        
        $columns        = 'events.event_id,event_name,event_date,prelims_date,status,prelims,venue,prelims_venue';
        $condtion       = ['status !=' => DELETED];
        $search_value   = null;
        $search_like    = null;

        $search_keywords    = $this->input->get('search[value]');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            $search_like = array('event_name','event_date','prelims_date','status','venue','prelims_venue');
        }

        
        $totalevents = $this->commonModel->selectDataCommon('events', 'events.event_id', $condtion, $search_value, $search_like,$order_by = NULL,$limit = NULL ,$joins = NULL);
        $iTotalRecords = $totalevents->num_rows();

        $orderColumn = intval($this->input->get("order[0][column]"));
        $orderDir = $this->input->get("order[0][dir]");
        if (isset($orderColumn) && !empty($orderColumn)) {


            $order_list = array('event_name' => 'event_name', 'date' =>'event_date', 'venue' => 'venue','status' =>'events.status','prelims' =>'prelims');
           // echo $orderColumn;
            $key = $this->input->get("columns[$orderColumn][data]");
            $order_by['key'] = $order_list[$key];
            $order_by['type'] = $orderDir;
        }
        else 
        {
            $order_by = NULL;
        }
        $limit = array('start' => $start , 'length' => $length);
        $events = $this->commonModel->selectDataCommon('events', $columns, $condtion, $search_value, $search_like,$order_by,$limit ,$joins);

        $data = array();
        $i = 1;
        if ($events->num_rows()>0) {
            foreach($events->result_array() as $row) {

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
    public function getData($id,$roleId)
    {
        $columns        = 'events.event_id,event_name,event_date,prelims_date,status,prelims,venue,prelims_venue,contact_id';
        $condtion       = ['events.event_id' => $id, 'events.status !=' => DELETED];
        $limit = array('start' => 0 , 'length' => 1);
        $events = $this->commonModel->selectDataCommon('events', $columns, $condtion,$search_value = NULL,$search_like = NULL,$order_by = NULL,$limit,$joins = NULL);

        $data = array();
        if ($events->num_rows()>0) {
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
            $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
        } else {
            $data['event_name'] = $this->input->post('event_name');
            $data['event_date'] = $this->input->post('eventDate');
            $data['contact_id'] = $this->input->post('contact');
            $data['prelims'] = $this->input->post('prelims');
            $data['venue'] = trim($this->input->post('venue'));
            $data['status'] = $this->input->post('inputState');
            $data['created_date'] = date("Y-m-d H:i:s");
            if($data['prelims'] != 0)
            {
                $data['prelims_venue'] = $this->input->post('prelimsVenue');
                $data['prelims_date'] = $this->input->post('prelimsDate');
            }
            //user data insertion......
            $details = $this->commonModel->insertData('events', $data);
            if ($details > 0) {
                    $result = array('status' => true, 'msg' => 'Successfully Added','reset' => true);
                    /// redirect('login');
                } else {
                    $result = array('status' => false, 'msg' => 'Something went wrong.','reset' => false);
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
        if( $this->input->post('eventid')!='')
        { 
            $id   = $this->input->post('eventid');
            $this->form_validation->set_rules('event_name', 'Event name', 'required|strip_tags');
            if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
            } else {
                
                $data['event_name'] = $this->input->post('event_name');
                $data['event_date'] = $this->input->post('eventDate');
                $data['venue'] = trim($this->input->post('venue'));
                $data['prelims'] = $this->input->post('prelims');
                $data['status'] = $this->input->post('inputState');
                if($data['prelims'] != 0)
                {
                    $data['prelims_venue'] = $this->input->post('prelimsVenue');
                    $data['prelims_date'] = $this->input->post('prelimsDate');
                }
                else 
                {
                    $data['prelims_venue'] = NULL;
                    $data['prelims_date'] = NULL;
                }

                $condtion           = ['event_id' => $id];
                //user data insertion......
                $details = $this->commonModel->updateData('events', $data, $condtion);
                if ($details) {
                    $result = array('status' => true ,'msg' => 'Successfully updated.');
                } else {
                    $result = array('status' => true, 'msg' => 'Nothing to update.');
                }
            }
        }
        else 
        {
            $result = array('status' => false, 'msg' => 'Something went wrong.','reset' => false);
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
        if( $this->input->post('id')!='')
        { 
            $id   = $this->input->post('id');
            $result = array('status' => false, 'msg' => 'Something went wrong.');
            $condtion           = ['event_id' => $id];
            $data['status']      = DELETED;
            //user data deletion......
            $details = $this->commonModel->updateData('events', $data, $condtion);
            if ($details) {
                $result = array('status' => true,'msg' => 'Success' ,'reload' => true);
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
        if ($list->num_rows()>0) {
            $data = $list->result_array();
        }
        $result = array('status' => true,'data' => $data);
        echo json_encode($result);
    }

    /**
     * Save new contestant data.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string
     * Date::            14-04-2019
     */

    public function contestantstore()
    {
        if( $this->input->post('eventid')!='')
        {
            $this->form_validation->set_rules('memberName', 'Name', 'required|strip_tags');
            $this->form_validation->set_rules('contact_no', 'Mobile', 'strip_tags');
            $this->form_validation->set_rules('email', 'Email', 'strip_tags|valid_email');

            if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
            } else {
                $data['event_id']   = $this->input->post('eventid');
                $data['name'] = $this->input->post('memberName');
                $data['contact_no'] = $this->input->post('contact_no');
                $data['email'] = trim($this->input->post('email'));
                $data['prelims_roll_no'] = md5($this->input->post('prelimsRollno'));
                $data['roll_no'] = md5($this->input->post('rollno'));
                $data['status'] = APPROVED;
                $data['created_date'] = date("Y-m-d H:i:s");
                
                //contestant data insertion......
                $details = $this->commonModel->insertData('event_registration', $data);
                if ($details > 0) {
                        $result = array('status' => true, 'msg' => 'Successfully Added','reset' => true);
                    } else {
                        $result = array('status' => false, 'msg' => 'Something went wrong.','reset' => false);
                    }
            }
        }
        else 
        {
            $result = array('status' => false, 'msg' => 'Something went wrong.','reset' => false);
        }
        echo json_encode($result);
    }
}

/* End of file User Controller*/

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
        
        $columns        = 'events.id,name,eventdate,status,venue,eventType';
        $condtion       = ['status !=' => DELETED];
        $search_value   = null;
        $search_like    = null;

        $search_keywords    = $this->input->get('search[value]');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            $search_like = array('name','eventdate','status','venue','eventType');
        }

        
        $totalevents = $this->commonModel->selectDataCommon('events', 'events.id', $condtion, $search_value, $search_like,$order_by = NULL,$limit = NULL ,$joins = NULL);
        $iTotalRecords = $totalevents->num_rows();

        $orderColumn = intval($this->input->get("order[0][column]"));
        $orderDir = $this->input->get("order[0][dir]");
        if (isset($orderColumn) && !empty($orderColumn)) {


            $order_list = array('name' => 'name', 'date' =>'eventdate', 'venue' => 'venue','status' =>'events.status','eventType' =>'eventType');
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
            foreach($events->result_array() as $r) {

                $data[] = array(
                    'slno' => $i++,
                    'name' => $r['name'],
                    'date' => $r['eventdate'],
                    'eventType' => $r['eventType'],
                    'venue' => $r['venue'],
                    'status' => $r['status'],
                    'id' => $r['id']           
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
        $columns        = 'events.id,name,eventdate,status,venue,eventType';
        $condtion       = ['events.id' => $id, 'events.status !=' => DELETED];
        $limit = array('start' => 0 , 'length' => 1);
        $events = $this->commonModel->selectDataCommon('events', $columns, $condtion,$search_value = NULL,$search_like = NULL,$order_by = NULL,$limit,$joins = NULL);

        $data = array();
        if ($events->num_rows()>0) {
            foreach ($events->result_array() as $row) {
                //hint => key - name of form field in modelForm
                $data['input'] = array(
                    'eventid'   => $row['id'],
                    'name' => $row['name'],
                    'eventDate'  => $row['eventdate'],
                    'venue'     => $row['venue']     
                );
                $data['select'] = array(
                    'inputStateedit'     => $row['status'], 
                    'eventTypeedit'     => $row['eventType']      
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
        $this->form_validation->set_rules('name', 'Event name', 'required|strip_tags');
        if ($this->form_validation->run() == false) {
            $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
        } else {
            $data['name'] = $this->input->post('name');
            $data['eventDate'] = $this->input->post('eventDate');
            $data['venue'] = trim($this->input->post('venue'));
            $data['eventType'] = $this->input->post('eventType');
             $data['status'] = $this->input->post('inputState');
            $data['created_at'] = date("Y-m-d H:i:s");
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
            $this->form_validation->set_rules('name', 'Name', 'required|strip_tags');
            if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
            } else {
                
                $data['name'] = $this->input->post('name');
                $data['eventdate'] = $this->input->post('eventDate');
                $data['venue'] = trim($this->input->post('venue'));
                $data['eventType'] = $this->input->post('eventType');
                $data['status'] = $this->input->post('inputState');
                $condtion           = ['id' => $id];
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
            $condtion           = ['id' => $id];
            $data['status']      = DELETED;
            //user data deletion......
            $details = $this->commonModel->updateData('events', $data, $condtion);
            if ($details) {
                $result = array('status' => true,'msg' => 'Success' ,'reload' => true);
            } 
        }
        echo json_encode($result);
    }
}

/* End of file User Controller*/

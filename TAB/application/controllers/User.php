<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
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
     * User list.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string  user list
     * Date:            30-03-2019
     */

    public function list()
    {
        $draw = intval($this->input->get("draw"));
        $start = intval($this->input->get("start"));
        $length = intval($this->input->get("length"));
        $roleId = intval($this->input->get("role"));
        
        $columns        = 'tab_user.user_id,tab_user.name,role_name,tab_user.status,contact_no,company_name,about_user';
        $condtion       = ['tab_user.status !=' => DELETED, 'tab_user.role_id' => $roleId];
        $search_value   = null;
        $search_like    = null;

        $search_keywords    = $this->input->get('search[value]');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            if($roleId == USER)
                $search_like = array('tab_user.name','role_name','tab_user.status','contact_no','company_name');
            if($roleId == JUDGE)
                $search_like = array('tab_user.name','role_name','tab_user.status','contact_no','about_user');    
        }

        

        $joins = array(
            array(
                'table' => 'role',
                'condition' => 'role.role_id=tab_user.role_id',
                'jointype' => 'FULL'
            ),
            array(
                'table' => 'company_list',
                'condition' => 'company_list.company_id=tab_user.company_id',
                'jointype' => 'LEFT'
            )
        );

        
        $totalUsers = $this->commonModel->selectDataCommon('tab_user', 'tab_user.user_id', $condtion, $search_value, $search_like,$order_by = NULL,$limit = NULL ,$joins);
        $iTotalRecords = $totalUsers->num_rows();

        $orderColumn = intval($this->input->get("order[0][column]"));
        $orderDir = $this->input->get("order[0][dir]");
        if (isset($orderColumn) && !empty($orderColumn)) {


            $order_list = array('name' => 'tab_user.name', 'role' => 'role_name', 'contact_no' =>'contact_no', 'company_name' => 'company_name','status' =>'tab_user.status','about' => 'about_user');
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
        $users = $this->commonModel->selectDataCommon('tab_user', $columns, $condtion, $search_value, $search_like,$order_by,$limit ,$joins);

        $data = array();
        $i = 1;
        if ($users->num_rows()>0) {
            foreach($users->result_array() as $row) {

                $data[] = array(
                    'slno' => $i++,
                    'name' => $row['name'],
                    'role' => $row['role_name'],
                    'contact_no' => $row['contact_no'],
                    'company_name' => $row['company_name'],
                    'status' => $row['status'],
                    'about' => $row['about_user'],
                    'id' => $row['user_id']           
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


        //$result = array('success' => true, 'list' => $users);
        //echo json_encode($result);
    }
    /**
     * Get A single entry
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            30-03-2019
     */
    public function getData($id,$roleId)
    {
        $columns        = 'tab_user.user_id,tab_user.name,email,contact_no,tab_user.role_id,tab_user.status,role_name as role,about_user,tab_user.company_id';
        $condtion       = ['tab_user.user_id' => $id, 'tab_user.status !=' => DELETED];
        $joins = array(
                array(
                'table' => 'role',
                'condition' => 'role.role_id=tab_user.role_id',
                'jointype' => 'FULL'
                ),
                array(
                    'table' => 'company_list',
                    'condition' => 'company_list.company_id=tab_user.company_id',
                    'jointype' => 'LEFT'
                )
            );
        $limit = array('start' => 0 , 'length' => 1);
        $users = $this->commonModel->selectDataCommon('tab_user', $columns, $condtion,$search_value = NULL,$search_like = NULL,$order_by = NULL,$limit,$joins);

        $data = array();
        if ($users->num_rows()>0) {
            foreach ($users->result_array() as $row) {
                //hint => key - name of form field in modelForm
                $data['input'] = array(
                    'userid'   => $row['user_id'],
                    'memberName' => $row['name'],
                    'contact_no'  => $row['contact_no'],
                    'email'     => $row['email'],
                    'company_id' => $row['company_id']    
                );
                if($roleId=JUDGE)
                {
                    $data['withid'] = array(
                        'aboutUseredit' => $row['about_user']    
                    );
                }

            }
        }
        $result = array('status' => true, 'data' => $data);
        echo json_encode($result);
    }
    /**
     * Save new user data.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string
     * Date::            30-03-2019
     */

    public function store()
    {
        $this->form_validation->set_rules('memberName', 'Name', 'required|strip_tags');
        $this->form_validation->set_rules('contact_no', 'Mobile', 'required|strip_tags');
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email|matches[confirm_email]|is_unique[tab_user.email]', array(
            'required'      => 'You have not provided a valid %s.',
            'is_unique'     => 'This %s already exists.'
        ));
        $this->form_validation->set_rules('confirm_email', 'Confirm Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');

        if ($this->form_validation->run() == false) {
            $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
        } else {
            $data['name'] = $this->input->post('memberName');
            $data['contact_no'] = $this->input->post('contact_no');
            $data['email'] = trim($this->input->post('email'));
            $data['password'] = md5($this->input->post('password'));
            
            $data['role_id'] = trim($this->input->post('roleid'));
            $data['status'] = APPROVED;
            $data['created_date'] = date("Y-m-d H:i:s");
            if(!empty($this->input->post('company')))
            {
                $data['company_id'] = intval($this->input->post('company'));
            }
            if(!empty($this->input->post('aboutUser')))
            {
                $data['about_user'] = trim($this->input->post('aboutUser'));
            }
            //user data insertion......
            $details = $this->commonModel->insertData('tab_user', $data);
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
     * Update user data.
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            30-03-2019
     */

    public function edit()
    {
        if( $this->input->post('userid')!='')
        { 
            $id   = $this->input->post('userid');
            $this->load->library('MY_Form_validation');
            $this->form_validation->set_rules('memberName', 'Name', 'required|strip_tags');
            $this->form_validation->set_rules('contact_no', 'Mobile', 'required|strip_tags');
            $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email|edit_unique[tab_user.email.'.$id.']', array(
                'required'      => 'You have not provided a valid %s.',
            ));
            if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
            } else {
                
                $data['name']       = $this->input->post('memberName');
                $data['contact_no']   = $this->input->post('contact_no');
                $data['email']      = trim($this->input->post('email'));
                if(!empty($this->input->post('company')))
                {
                    $data['company_id'] = intval($this->input->post('company'));
                }
                if(!empty($this->input->post('aboutUser')))
                {
                    $data['about_user'] = trim($this->input->post('aboutUser'));
                }
                $condtion           = ['user_id' => $id];
                //user data insertion......
                $details = $this->commonModel->updateData('tab_user', $data, $condtion);
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
     * Delete user data
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date:            30-03-2019
     */

    public function destroy()
    {
        if( $this->input->post('id')!='')
        { 
            $id   = $this->input->post('id');
            $result = array('status' => false, 'msg' => 'Something went wrong.');
            $condtion           = ['user_id' => $id];
            $data['status']      = DELETED;
            //user data deletion......
            $details = $this->commonModel->updateData('tab_user', $data, $condtion);
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

    public function getCompanyData()
    {
        $columns        = 'company_id as id,company_name as value';
        $list = $this->commonModel->selectDataCommon('company_list', $columns);

        $data = array();
        if ($list->num_rows()>0) {
            $data = $list->result_array();
        }
        $result = array('status' => true,'data' => $data);
        echo json_encode($result);
    }
}

/* End of file User Controller*/

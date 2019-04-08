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
        
        $columns        = 'users.id,users.name,roles.name as role,users.status,phone_no,organization,about_user';
        $condtion       = ['users.status !=' => DELETED, 'users.role_id' => $roleId];
        $search_value   = null;
        $search_like    = null;

        $search_keywords    = $this->input->get('search[value]');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            if($roleId == USER)
                $search_like = array('users.name','roles.name','users.status','phone_no','organization','users.id');
            if($roleId == JUDGE)
                $search_like = array('users.name','roles.name','users.status','phone_no','about_user','users.id');    
        }

        

        $joins = array(
            array(
            'table' => 'roles',
            'condition' => 'roles.id=users.role_id',
            'jointype' => 'FULL'
            )
        );

        
        $totalUsers = $this->commonModel->selectDataCommon('users', 'users.id', $condtion, $search_value, $search_like,$order_by = NULL,$limit = NULL ,$joins);
        $iTotalRecords = $totalUsers->num_rows();

        $orderColumn = intval($this->input->get("order[0][column]"));
        $orderDir = $this->input->get("order[0][dir]");
        if (isset($orderColumn) && !empty($orderColumn)) {


            $order_list = array('name' => 'users.name', 'role' => 'roles.name', 'phone_no' =>'phone_no', 'organization' => 'organization','status' =>'users.status','about' => 'about_user');
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
        $users = $this->commonModel->selectDataCommon('users', $columns, $condtion, $search_value, $search_like,$order_by,$limit ,$joins);

        $data = array();
        $i = 1;
        if ($users->num_rows()>0) {
            foreach($users->result_array() as $r) {

                $data[] = array(
                    'slno' => $i++,
                    'name' => $r['name'],
                    'role' => $r['role'],
                    'phone_no' => $r['phone_no'],
                    'organization' => $r['organization'],
                    'status' => $r['status'],
                    'about' => $r['about_user'],
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
        $columns        = 'users.id,users.name,email,phone_no,role_id,users.status,roles.name as role,about_user';
        $condtion       = ['users.id' => $id, 'users.status !=' => DELETED];
        $joins = array(
                array(
                'table' => 'roles',
                'condition' => 'roles.id=users.role_id',
                'jointype' => 'FULL'
                )
            );
        $limit = array('start' => 0 , 'length' => 1);
        $users = $this->commonModel->selectDataCommon('users', $columns, $condtion,$search_value = NULL,$search_like = NULL,$order_by = NULL,$limit,$joins);

        $data = array();
        if ($users->num_rows()>0) {
            foreach ($users->result_array() as $row) {
                //hint => key - name of form field in modelForm
                $data = array(
                    'userid'   => $row['id'],
                    'memberName' => $row['name'],
                    'contact_no'  => $row['phone_no'],
                    'email'     => $row['email']       
                );
                if($roleId=JUDGE)
                {
                    $data['aboutUser'] = $row['about_user'];
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
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email|matches[confirm_email]|is_unique[users.email]', array(
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
            $data['phone_no'] = $this->input->post('contact_no');
            $data['email'] = trim($this->input->post('email'));
            $data['password'] = md5($this->input->post('password'));
            $data['role_id'] = trim($this->input->post('roleid'));
            $data['status'] = APPROVED;
            $data['created_at'] = date("Y-m-d H:i:s");
            if(!empty($this->input->post('organization')))
            {
                $data['organization'] = trim($this->input->post('organization'));
            }
            if(!empty($this->input->post('aboutUser')))
            {
                $data['about_user'] = trim($this->input->post('aboutUser'));
            }
            //user data insertion......
            $details = $this->commonModel->insertData('users', $data);
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
            $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email|edit_unique[users.email.'.$id.']', array(
                'required'      => 'You have not provided a valid %s.',
            ));
            if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array(),'reset' => false);
            } else {
                
                $data['name']       = $this->input->post('memberName');
                $data['phone_no']   = $this->input->post('contact_no');
                $data['email']      = trim($this->input->post('email'));
                $condtion           = ['id' => $id];
                //user data insertion......
                $details = $this->commonModel->updateData('users', $data, $condtion);
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
            $condtion           = ['id' => $id];
            $data['status']      = DELETED;
            //user data deletion......
            $details = $this->commonModel->updateData('users', $data, $condtion);
            if ($details) {
                $result = array('status' => true,'msg' => 'Success' ,'reload' => true);
            } 
        }
        echo json_encode($result);
    }
}

/* End of file User Controller*/

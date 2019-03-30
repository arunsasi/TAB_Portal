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
        $columns        = 'id,name,role_id,status';
        $condtion       = ['status !=' => DELETED];
        $search_value   = null;
        $search_like    = null;

        $search_keywords    = $this->input->post('search_keywords');
        if (isset($search_keywords) && !empty($search_keywords)) {
            $search_value = $search_keywords;
            $search_like = array('name');
        }

        $users = $this->commonModel->selectDataCommon('users', $columns, $condtion, $search_value, $search_like);
        $result = array('success' => true, 'list' => $users);
        echo json_encode($result);
    }
    /**
     * Get A single entry
     * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date::            30-03-2019
     */
    public function getData($id)
    {
        $columns        = 'users.id,users.name,email,role_id,users.status,roles.name as role';
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
        /* if (!empty($users)) {
            foreach ($users as $row) {
                $userCode   = $row['name'];
                $roleId     = $row['role_id'];
                $useStatus  = $row['status'];
                $userId     = $row['id'];
            }
        } */
        $result = array('success' => true, 'list' => $users);
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
        $this->form_validation->set_rules('name', 'Name', 'required|strip_tags');
        $this->form_validation->set_rules('contact_no', 'Mobile', 'required|strip_tags');
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email|matches[confirm_email]|is_unique[users.email]', array(
            'required'      => 'You have not provided a valid %s.',
            'is_unique'     => 'This %s already exists.'
        ));
        $this->form_validation->set_rules('confirm_email', 'Confirm Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');

        if ($this->form_validation->run() == false) {
            $result = array('status' => false, 'error' => $this->form_validation->error_array());
        } else {
            $data['name'] = $this->input->post('name');
            $data['phone_no'] = $this->input->post('contact_no');
            $data['email'] = trim($this->input->post('email'));
            $data['password'] = md5($this->input->post('password'));
            $data['role_id'] = USER;
            $data['status'] = APPROVED;
            $data['created_at'] = date("Y-m-d H:i:s");
            //user data insertion......
            $details = $this->commonModel->insertData('users', $data);
            if ($details > 0) {
                    $result = array('status' => true);
                    /// redirect('login');
                } else {
                    $result = array('status' => false, 'msg' => 'Something went wrong.');
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

    public function edit($id)
    {
        $this->load->library('MY_Form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required|strip_tags');
        $this->form_validation->set_rules('contact_no', 'Mobile', 'required|strip_tags');
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email|edit_unique[users.email.'.$id.']', array(
            'required'      => 'You have not provided a valid %s.',
        ));
        if ($this->form_validation->run() == false) {
            $result = array('status' => false, 'error' => $this->form_validation->error_array());
        } else {
            $data['name']       = $this->input->post('name');
            $data['phone_no']   = $this->input->post('contact_no');
            $data['email']      = trim($this->input->post('email'));
            $condtion           = ['id' => $id];
            //user data insertion......
            $details = $this->commonModel->updateData('users', $data, $condtion);
            if ($details) {
                $result = array('status' => true);
            } else {
                $result = array('status' => false, 'msg' => 'Something went wrong.');
            }
        }
        echo json_encode($result);
    }

    /**
     * Delete user data.
 	 * @author          Chinnu
     * @since           Version 1.0.0
     * @param int $id
     * @return string
     * Date:            30-03-2019
     */

    public function destroy($id)
    {
        $result = array('status' => false, 'msg' => 'Something went wrong.');
        if ($id) {
            $condtion           = ['id' => $id];
            //user data insertion......
            $details = $this->commonModel->deleteData('users', $condtion);
            if ($details) {
                $result = array('status' => true);
            } 
        }
        echo json_encode($result);
    }
}

/* End of file User Controller*/

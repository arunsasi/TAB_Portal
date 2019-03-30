<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CompanyApi extends CI_Controller
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
     * Login.
 	 * @author          Chinnu
     * @since           Version 1.0.0
     * @param NULL
     * @return string 
     * Date:            30-03-2019
     */
    public function login()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email', array(
            'required'      => 'You have not provided a valid %s.'
        ));
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array());
            } else {
                $data['email'] = trim($this->input->post('email'));
                $data['password'] = md5($this->input->post('password'));
                $columns    = 'id,name,role_id,status';
                $condtion   = ['email' => $email];
                $user = $this->commonModel->selectDataCommon('users', $columns, $condtion);
                if (!empty($user)) {
                        foreach ($user as $row) {
                                $userCode = $row['name'];
                                $roleId = $row['role_id'];
                                $useStatus = $row['status'];
                                $userId = $row['id'];
                            }
                        if ($useStatus == BLOCKED || $useStatus == DELETED) {
                                $result = array('status' => false, 'msg' => 'Something went wrong.');
                            } else {
                                if ($roleId == ADMIN) {
                                        $result = array('status' => true, 'msg' => 'Success', 'url' => '');
                                    } elseif ($roleId == USER) {
                                        $result = array('status' => true, 'msg' => 'Success', 'url' => '');
                                    }
                            }
                    }
            }
        echo json_encode($result);
    }

    /**
     * Check- Email already Exist.
 	 * @author          Chinnu
     * @since           Version 1.0
 	 * @param NULL
     * @return string
     * Date:            23-03-2019.
     */

    public function checkEmail()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email', array(
            'required'      => 'You have not provided a valid %s.'
        ));
        if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'error' => $this->form_validation->error_array());
            } else {
                $email = $this->input->post('email');
                $email = $columns = 'count(id) as cnt';
                $condtion = ['email' => $email];
                $emailExist = $this->commonModel->selectDataCommon('users', $columns, $condtion);
                $emailcnt = 0;
                foreach ($emailExist as $row) {
                        $emailcnt = $row['cnt'];
                    }
                if ($emailcnt > 0) {
                        $result = array('status' => false);
                    } else {
                        $result = array('status' => true);
                    }
            }
        echo json_encode($result);
    }

    /**
     * User registration
 	 * @author          Chinnu
     * @since           Version 1.0
     * @param NULL
     * @return string
 	 * Date::            23-03-2019
     */

    public function register()
    {
        $this->form_validation->set_rules('organization', 'Organization Name', 'required|strip_tags');
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
                $data['organization'] = $this->input->post('organization');
                $data['name'] = $this->input->post('name');
                $data['phone_no'] = $this->input->post('contact_no');
                $data['email'] = trim($this->input->post('email'));
                $data['password'] = md5($this->input->post('password'));
                $data['role_id'] = USER;
                $data['status'] = EMAIL_NOT_VERIFIED;
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['email_verification_code'] = md5($this->randomString(10));
                //user data insertion......
                $details = $this->commonModel->insertData('users', $data);
                if ($details > 0) {
                        if ($data['email'] != null) {

                                $email1 = $data['email'];
                                $this->load->library('mail');
                                $subject = "Natana";
                                #$data['email_subject'] = $subject;
                                $url = site_url('registration/validate-email?mail=' . $data['email'] . 'token=' . $data['email_verification_code']);

                                $content = "Dear " . $data['name'] . ", <br/>Thank you for registering with 'TAB'.<br/>Please click the link to verify your email address : <br/><a href='" . $url . "' target='blank'> Email verification link </a>";
                                #$content = $this->load->view('user/mail_template_new',$data,TRUE);
                                #$this->mail->send_verification_mail($email1,$subject,$content);
                            }
                        $result = array('status' => true);
                        /// redirect('login');
                    } else {
                        $result = array('status' => false, 'msg' => 'Something went wrong.');
                    }
            }
        echo json_encode($result);
    }


    /**
     * One time password creation.
 	 * @author          Chinnu
     * @since           Version 1.0
     * @param NULL
     * @return string
 	 * Date::            23-03-2019
     */
    public function randomString($length)
    {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    /**
     * email verification
 	 * @author          Chinnu
     * @since           Version 1.0
     * @param NULL
     * @return string
 	 * Date::            23-03-2019
     */
    public function emailVerification()
    {
        $this->form_validation->set_rules('token', 'Token', 'required|strip_tags');
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email');

        if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'msg' => 'You cant access this URL');
            } else {
                $email              = $this->input->post('email');
                $verificationCode   = $this->input->post('token');

                $columns    = 'id,email_verification_code,status';
                $condtion   = ['email' => $email];
                $code = $this->commonModel->selectDataCommon('users', $columns, $condtion);
                if (!empty($code)) {
                        foreach ($code as $codes) {
                                $userCode = $codes['email_verification_code'];
                                $useStatus = $codes['status'];
                                $userId = $codes['id'];
                            }
                        if ($useStatus > EMAIL_NOT_VERIFIED) {
                                $result = array('status' => true, 'msg' => 'Email already verified');
                            } elseif ($verificationCode === $userCode) {
                                $data['status'] = EMAIL_VERIFIED;
                                $data['email_verification_code'] = '';
                                $condtion   = ['id' => $userId];
                                $stat = $this->commonModel->updateData('users', $data, $condtion);
                                if ($stat) {
                                        $result = array('status' => true, 'msg' => 'Success');
                                    } else {
                                        $result = array('status' => false, 'msg' => 'Something went wrong.');
                                    }
                            } else {
                                $result = array('status' => false, 'msg' => 'No such Email ID');
                            }
                    } else {
                        $result = array('status' => false, 'msg' => 'No such Email ID');
                    }
            }

        echo json_encode($result);
    }

    /**
     * forget password.
 	 * @author          Chinnu
     * @since           Version 1.0
     * @param NULL
     * @return string
 	 * Date::            23-03-2019 
     * Description:     
     */
    public function passwordResetMail()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email');
        if ($this->form_validation->run() == false) {
                $result = array('status' => false, 'msg' => 'You cant access this URL');
            } else {
                $email  = $this->input->post('email');
                $columns    = 'id,status';
                $condtion   = ['email' => $email];
                $emailExist = $this->commonModel->selectDataCommon('users', $columns, $condtion);
                if (!empty($emailExist)) {
                        foreach ($emailExist as $row) {
                                $useStatus = $row['status'];
                                $userId = $row['id'];
                            }
                        if ($useStatus == EMAIL_NOT_VERIFIED) {
                                $result = array('status' => false, 'msg' => 'Email verification process pending');
                            } elseif ($useStatus == EMAIL_VERIFIED) {
                                $result = array('status' => false, 'msg' => 'Account Under administrator approval session');
                            } elseif ($useStatus == BLOCKED || $useStatus == DELETED) {
                                $result = array('status' => false, 'msg' => 'Your account is temerorly suspended please contact administrator');
                            } else {
                                $data['email_verification_code'] = md5($this->randomString(10));
                                $condtion   = ['id' => $userId];
                                $stat = $this->commonModel->updateData('users', $data, $condtion);
                                if ($stat) {
                                        $this->load->library('mail');
                                        $subject = "Natana";
                                        #$data['email_subject'] = $subject;
                                        $url = site_url('registration/reset-password?mail=' . $email . 'token=' . $data['email_verification_code']);

                                        $content = "Dear " . $data['name'] . ", <br/>Please click the link to reset your password: <br/><a href='" . $url . "' target='blank'> Reset Password Link </a>";
                                        #$content = $this->load->view('user/mail_template_new',$data,TRUE);
                                        #$this->mail->send_verification_mail($email,$subject,$content);
                                        $result = array('status' => true, 'msg' => 'Success');
                                    } else {
                                        $result = array('status' => false, 'msg' => 'Something went wrong.');
                                    }
                            }
                    } else {
                        $result = array('status' => false, 'msg' => 'No such Email ID');
                    }
            }
    }

    /**
     * change password webservice
 	 * @author          Chinnu
     * @since           Version 1.0
     * @param NULL
     * @return string
 	 * Date::            23-03-2019 
     * Description:      
     */
    public function passwordReset()
    {
        $this->form_validation->set_rules('token', 'Token', 'required|strip_tags');
        $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');

        if ($this->form_validation->run() == false) {
                // $result = array('status'=> FALSE, 'msg'=> 'You cant access this URL');
                $result = array('status' => false, 'error' => $this->form_validation->error_array());
            } else {
                $email              = $this->input->post('email');
                $verificationCode   = $this->input->post('token');

                $columns    = 'id,email_verification_code,status';
                $condtion   = ['email' => $email];
                $emailExist = $this->commonModel->selectDataCommon('users', $columns, $condtion);
                if (!empty($emailExist)) {
                        foreach ($emailExist as $row) {
                                $userCode = $row['email_verification_code'];
                                $useStatus = $row['status'];
                                $userId = $row['id'];
                            }
                        if ($verificationCode === $userCode) {
                                $data['password'] = md5($this->input->post('password'));
                                $data['email_verification_code'] = '';
                                $condtion   = ['id' => $userId];
                                $stat = $this->commonModel->updateData('users', $data, $condtion);
                                if ($stat) {
                                        $result = array('status' => true, 'msg' => 'Success');
                                    } else {
                                        $result = array('status' => false, 'msg' => 'Something went wrong.');
                                    }
                            } else {
                                $result = array('status' => false, 'msg' => 'No such Email ID');
                            }
                    } else {
                        $result = array('status' => false, 'msg' => 'No such Email ID');
                    }
            }

        echo json_encode($result);
    }
}

/* End of file Registration */



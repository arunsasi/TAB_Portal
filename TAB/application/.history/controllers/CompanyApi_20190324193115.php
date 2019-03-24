<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyApi extends CI_Controller {

    function __construct() 
    {
		parent::__construct();
		$this->load->helper('url'); 
		$this->load->helper('string');
		$this->load->library('session');
		//$this->load->library('mail');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url', 'captcha'));
        $this->load->model('commonModel');
        date_default_timezone_set('Asia/Kolkata');
    }


	/**
     * CodeIgniter
     * @package        NTANA
 	 * @author          Chinnu
     * @since           Version 1.0
 	 * Date:            23-03-2019
	 * Description: 	Check- Email already Exist
     */	
	  
    public function check_email()
    {
        $email = $this->input->post('email');
        $email = $columns = 'count(id) as cnt';
        $condtion = ['email' => $email];
        $email_exist = $this->commonModel->selectDataCommon('user',$columns,$condtion);
        $emailcnt = 0;
        foreach($email_exist as $row)
        {
            $emailcnt = $row['cnt'];
        }
        if($emailcnt > 0)
        {
            $result = array('status'=> FALSE);
        }
        else
        {
            $result = array('status'=> TRUE);
        }
    }
	
	/**
     * CodeIgniter
     * @package        NTANA
 	 * @author          Chinnu
     * @since           Version 1.0
 	 * Date:            23-03-2019
	 * Description: 	User registration
     */	
	public function register()
	{   
            $this->form_validation->set_rules('organization', 'Organization Name', 'required|strip_tags');
            $this->form_validation->set_rules('name', 'Name', 'required|strip_tags');
            $this->form_validation->set_rules('contact_no', 'Mobile', 'required|strip_tags');
            $this->form_validation->set_rules('email', 'Email', 'required|strip_tags|matches[confirm_email]');
            $this->form_validation->set_rules('confirm_email', 'Confirm Email', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required|matches[confirm_password]');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required');

			if ($this->form_validation->run() == FALSE)
			{ 
                    echo $errors = json_encode($this->form_validation->error_array());

			}
			else
			{
                    $data['organization'] = $this->input->post('organization');
                    $data['name'] = $this->input->post('name');
                    $data['phone_no'] = $this->input->post('contact_no');
                    $data['email'] = trim($this->input->post('email'));
                    $data['password'] = $this->input->post('password');
                    $data['role_id'] = USER;
                    $data['status'] = EMAIL_NOT_VERIFIED;
                    $data['created_at'] = date("Y-m-d H:i:s");
                    $data['email_verification_code'] = md5($this->random_string(10));


                    $columns = 'count(id) as cnt';
                    $condtion = ['email' =>$data['email']];
                    $email_exist = $this->commonModel->selectDataCommon('user',$columns,$condtion);
                    $emailcnt = 0;
                    foreach($email_exist as $row)
                    {
                        $emailcnt = $row['cnt'];
                    }
                    if($emailcnt > 0)
                    {
                        
                            echo 'Registration Failed. Email ID Already exist.';
                    }
                    else
                    { 
                            //user data insertion......
                            $details = $this->commonModel->insertData('user', $data); 
                            if($details > 0)
                            {   
                                if($data['email'] != NULL)
                                {
                                    
                                    $email1 = $data['email'];
                                    $this->load->library('mail');
                                    $subject = "Natana";
                                    #$data['email_subject'] = $subject;
                                    $url = site_url('user_registration/validate-email?mail='.$data['email'].'token='.$data['email_verification_code']);
                                    
                                    $content = "Dear ".$data['name'].", <br/>Thank you for registering with 'TAB'.<br/>Please click the link to verify your email address : <br/><a href='".$url."' target='blank'> Email verification link </a>";
                                    #$content = $this->load->view('user/mail_template_new',$data,TRUE);
                                    #$this->mail->send_verification_mail($email1,$subject,$content);
                                }
                               /// redirect('login');
                            }
                    }
						
			}
	}
	
	
	/**
     * CodeIgniter
     * @package        NTANA
 	 * @author          Chinnu
     * @since           Version 1.0
 	 * Date:            23-03-2019
	 * Description: 	One time password creation
     */	
    public function random_string($length) 
    {
            $key = '';
            $keys = array_merge(range(0, 9), range('a', 'z'));
        
            for ($i = 0; $i < $length; $i++) {
                $key .= $keys[array_rand($keys)];
            }
        
            return $key;
    }
     
	/**
     * CodeIgniter
     * @package        NTANA
 	 * @author          Chinnu
     * @since           Version 1.0
 	 * Date:            23-03-2019
     * Description: 	email verification
     */	
	public function email_verification()
	{
        $email              = $this->input->get('mail');
		$verificatio_code   = $this->input->get('id');
        if(isset($email)&& trim($email!='') && isset($verificatio_code) && trim($verificatio_code!=''))
        {
            $columns    = 'id,email_verification_code';
            $condtion   = ['email' => $email];
            $code = $this->commonModel->selectDataCommon('user',$columns,$condtion);
            if($code->num_rows()>0)
            {
                foreach($code->result() as $codes)
                {
                    $user_code = $codes->email_verification_code;
                    $userId = $codes->id;  
                }
                if (($verificatio_code) === ($user_code)) 
                {
                    $data['stat'] = $this->commonModel->updateData('user',$columns,$condtion);
                    if($data['stat'])
                    {
                        $this->session->set_flashdata('Reg_Success', 'Email ID successfully verified.');
						$newdata = array(
						'e_verify' => 1,
						);
						$this->session->unset_userdata('e_verify');
						$this->session->set_userdata($newdata); 
						
						if($this->session->userdata('m_verify') == 1)
						{
							$newdata = array(
							'verified' => 0,
							);
							$this->session->unset_userdata('verified');
							$this->session->set_userdata($newdata); 
						}
                    }
                    else
                    {
                        $this->session->set_flashdata('Reg_Success', 'Email ID already verified');
                    }

                }
                else
                {
                    $this->session->set_flashdata('Reg_Success', 'No such Email ID');
                }
            }
            else
            {
                $this->session->set_flashdata('Reg_Success', 'No such Email ID');
            }
        }
        else
        {
            $this->session->set_flashdata('Reg_Success', 'You cant access this URL');
        }
                    
       	$user_id = $this->session->userdata('id'); 
        if ($user_id == $data['id']) 
        {
            redirect('user_home');
        }
        else
        {
        	//$this->session->sess_destroy();
        	$array_items = array('id', 'name', 'email', 'mobile', 'status', 'role', 'accessPermission');

        	$this->session->unset_userdata($array_items);
            redirect('home');
        }
		
		
	}

	/**
     * CodeIgniter
     * @package        NTANA
 	 * @author          Chinnu
     * @since           Version 1.0
 	 * Date:            23-03-2019 
     * Description:     change password webservice
     */
    public function change_password_webservice()
    { echo "rtrdtr";
		
        $device = $this->device_validation->check_user_device();
        if($device == '1')
        {
            langs();
            $data['old_password'] = $this->input->post('current_password');
            $data['password'] = $this->input->post('confirm_password');
            $data['user_id'] = $this->input->post('user_id');
            $data['imei_number'] = $this->input->post('imei_number');
            $data['check_value'] = $this->input->post('check_value');
            $user_exist = $this->device_validation->check_user_exist($data['user_id'],$data['imei_number'],$data['check_value']);
            if($user_exist)
            {
                if (($data['user_id'] != '') && ($data['password'] != '') && ($data['old_password'] != '')) 
                {
                    $data['pwd'] = $this->User_model->get_user_pwd($data['user_id']);
                    $data['status'] = $this->User_model->get_user_status($data['user_id']);
                    if($data['pwd'] == ($data['old_password']))
                    {
                        if ($data['status'] == '1') 
                        {   
                                $d['change_password'] = $this->User_model->update_user_password($data);
                                if($d['change_password'])
                                {   
                                    $results = array("status" => '0');
                                    echo json_encode($results);
                                }
                                else
                                {
                                    $results = array("status" => '3');
                                    echo json_encode($results);
                                }
                            
                        }
                        else
                        {
                            $results = array("status" => '2');
                            echo json_encode($results);
                        }
                    }
                    else
                    {
                        $results = array("status" => '1');
                        echo json_encode($results);
                    }
                }
                else
                {
                    $results = array("status" => '4');
                    echo json_encode($results);
                }
            }
            else
            {
                $results = array("status" => '5');
                echo json_encode($results);
            }
            
        }
        else
        {
            $results = array("status" => '6');
            echo json_encode($results);
        }
        

	}
	
	 /**
     * CodeIgniter
     * @package        NTANA
 	 * @author          Chinnu
     * @since           Version 1.0
 	 * Date:            23-03-2019 
     * Description:     forget password
     */
    public function password_send()
    {
       
        $data['mobile'] = $this->input->post('phone');
        //$data['code'] = 4567;
        $data['code'] = mt_rand(100000, 999999);
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|max_length[10]');
                
        if ($this->form_validation->run() == FALSE) 
        {

            redirect('forget');
           
        } 
        else
        {
            $condition = "mobile= ".$data['mobile'];
            $data['userid'] = $this->User_model->user_exist($condition);
            if($data['userid'])
            {
                    $data['uid'] = $this->User_model->update_forgetpwd_code($data);
                    if($data['uid'])
                    {
                        //SMS
                                                                             
                                        $message_sms = "Thank you for using 'Karshika Vivara Sangetham Oru Viral Thumbil'. We received a request to reset your password. Your new Password is ".$data['code'].". Please change the password once you login.";
                                        $data['message_sms'] = $message_sms;                                        
                                        $data['tel']= $data['mobile'];
                                        $resp = send_sms($data);  
                                        //if($resp != 404) {
                                       // $det = $this->User_model->save_sms($data);
                                        //}
                                        //send_sms($data);
                                        //$det = $this->user_model->save_sms($data);
                                //SMS End
                        $this->session->set_flashdata('Success', 'OTP sent to your mobile number.');
                        //redirect('home');
                        $this->load->view('include/header');
                        redirect('home');

                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Some error occured. Please try again later');  
                        redirect('forget');
                    }
            }
            else
            {
                $this->session->set_flashdata('error', 'Invalid phone number.');  
                redirect('forget');
            }
                    
        }
    }
}

/* End of file Registration */
/* Location: ./application/controllers/Registration.php */
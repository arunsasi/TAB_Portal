<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Library Class: Imap */

class Mail {


        // Open IMAP connection

    function send_verification_mail($to,$subject,$content)
    {
        $ci =& get_instance();
        $ci->load->library('email');
//        $ci->load->helper('url');
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.gmail.com";
        $config['smtp_port'] = "465";
        $config['auth'] = true;
        $config['smtp_user'] = "chinschips@gmail.com"; //username
        $config['smtp_pass'] = "bibi&chipsmybuddies#";//password
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";

        $ci->email->initialize($config);

        $ci->email->from('chinschips@gmail.com', 'Test Mail');
        $list = array($to);
        $ci->email->to($list);
        //$this->email->reply_to('my-email@gmail.com', 'Explendid Videos');
        //$url = site_url('user_registration/email_verification?mail='.$to.'&id='.$random);
        $ci->email->subject($subject);
        $ci->email->message($content);
        $ci->email->send();
    }
}

?>

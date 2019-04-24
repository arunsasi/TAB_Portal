<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    if ( ! function_exists('upload'))
    {
            function upload()
            {
                    $CI =& get_instance();
                    $CI->load->database();
                    $CI->load->library('upload');
                    $CI->load->model('commonModel');
                    $CI->load->helper('security');
                    $CI->load->library('session');
                    $user_id = $CI->session->userdata('userId');
					//$output_dir = base_url().'uploads/';
                    $config['upload_path'] = 'uploads';
                    
                    $config['allowed_types'] = 'gif|jpg|png|jpeg|xls|xlsx|doc|docx|pdf|mp3|mp4|mov|3gp|avi|mkv|JPEG|JPG|PNG|GIF|wav|3gpp|awb|aac';

		            $CI->upload->initialize($config); // Important
                    if ($CI->security->xss_clean($_FILES["myfile"], TRUE) === FALSE)
                    {
                        // file failed the XSS test
                    }
                    else
                    {
                            if(isset($_FILES["myfile"]))
                            {
                                    $ret = array();

                                    $ext = $_FILES["myfile"]["type"];
                                    $md5 = md5($_FILES["myfile"]["name"]);
                                    $config['file_name'] = $_POST['id']."-".$md5."-".$_FILES["myfile"]["name"];
                                    //$CI->upload->initialize($config); // Important
                                    //You need to handle  both cases
                                    //If Any browser does not support serializing of multiple files using FormData() 
                                    if(!is_array($_FILES["myfile"]["name"])) //single file
                                    {
                                            $moved = $CI->upload->do_upload("myfile");
                                            $image_type =array('.gif','.jpg','.png','.jpeg','.GIF','.JPG','.PNG','.JPEG');
                                            $video_type =array('.mp3','.mp4','.mov','.3gp','.avi','.mkv');
                                            $other_type =array('.pdf','.doc','.docx','.xls','.xlsx');

                                            $file_data1 = $CI->upload->data();
                                            $data['filename1'] = $file_data1['file_name'];
                                        //     $arr = array(
                                        //         'docid' => $_POST['id'],
                                        //         'type_id' => $_POST['type'],
                                        //         'attachment' => $data['filename1'],
                                        //         'type' => $file_data1['file_ext'],
                                        //         );
                                        //     $table = 'attachment_new';
                                        //     $CI->common_query_model->insertData($table,$arr);

                                                $arr = array(
                                                        'event_id' => $_POST['id'],
                                                        'filename' => $data['filename1'],
                                                        'file_type' => $file_data1['file_ext'],
                                                        'attachment_type' => $_POST['attype'],
                                                        'prilims' => $_POST['prelims'],
                                                        'status' => ACTIVE,
                                                        'created_by' => $user_id,
                                                        'created_at' => date("Y-m-d H:i:s")
                                                        );

                                                $details = $CI->commonModel->insertData('attachments', $arr);
                                            

                                    }



                            }

                    }
            }
    }
/* End of file upload_helper.php */
/* Location: ./system/application/helper/upload_helper.php */


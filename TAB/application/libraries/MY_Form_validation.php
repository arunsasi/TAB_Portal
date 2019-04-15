<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation{

    public function edit_unique($str, $field)
    {
        $this->CI->form_validation->set_message('edit_unique', "Sorry, that %s is already being used.");
        sscanf($field, '%[^.].%[^.].%[^.]', $table, $field, $id);
        return isset($this->CI->db)
            ? ($this->CI->db->limit(1)->get_where($table, array($field => $str, 'user_id !=' => $id))->num_rows() === 0)
            : FALSE;
    }

}
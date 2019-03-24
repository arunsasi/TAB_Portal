<?php

class commonModel extends CI_Model{


	public function insertData($table, $data){
		$this->db->insert($table, $data);
		if($this->db->affected_rows() > 0){
			return $this->db->last_insert_id();
		} else {
			return false;
		}
	}

	public function updateData($table, $data, $condition){
		$this->db->where($condition);
		$this->db->update($table, $data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function deleteData($table, $condition){
		$this->db->where($condition);
		$this->db->delete($table);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function selectData($table, $fields, $condition, $sort=NULL, $order=NULL, $offset=NULL, $limit=NULL){
		if($sort != NULL && $order != NULL){
			$this->db->order_by($sort, $order);
		}
		if($offset!=NULL && $limit!=NULL){
			$this->db->limit($offset, $limit);
		}

		$this->db->select($fields);
		$this->db->from($table);
		$res = $this->db->get();
		return $res->result();
	}

	/**
     * @package         NTANA
     * @author          Chinnu
     * @since           Version 1.0
     * Date:            23-03-2019	 
	 * Description: 	Get data from any joined tables
     */	
    public function selectDataCommon($table, $columns,$condtion = NULL,$search_value = NULL,$search_like = NULL,$joins,$limit,$where_in = NULL,$where_in_data = NULL,$order_by = NULL)
    {
        //Getting values for DataTale

        $this->db->select($columns,false)->from($table);
        if (is_array($joins) && count($joins) > 0)
        {
            foreach($joins as $k => $v)
            {
                $this->db->join($v['table'], $v['condition'], $v['jointype']);
            }
        }
        if($condtion != NULL){
            if (is_array($condtion) && count($condtion) > 0)
            {
                foreach($condtion as $k => $v)
                {  
                     if($v['value'] != '' && $v['value'] != NULL){
                        $this->db->where($v['key'], $v['value']);
                     }
                }
            }
        }
        if($search_value != NULL && $search_value != ''){
            if (is_array($search_like) && count($search_like) > 0)
            {
             $this->db->group_start();
                foreach($search_like as $k )
                {  
                    $this->db->or_like($k,$search_value);                   
                }
              $this->db->group_end(); 
            }
        } 
        //Where in clause
        if($where_in != NULL && (!empty($where_in_data))){
         $this->db->where_in($where_in,$where_in_data);
        }
        if(is_array($limit) && count($limit) > 0){
            $this->db->limit($limit['length'],$limit['start']);
        }
        if($order_by != NULL){
          if(is_array($order_by) && count($order_by) > 0){
            $this->db->order_by($order_by['key'],$order_by['type']);
          }
        }
        $query = $this->db->get(); 
        return $query->result_array();
    }
}

?>
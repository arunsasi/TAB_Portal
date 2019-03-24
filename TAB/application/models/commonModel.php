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
}

?>
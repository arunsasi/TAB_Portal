<?php

class CommonModel extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function insertData($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    public function updateData($table, $data, $condition)
    {
        if ($condition != null) {
            if (is_array($condition) && count($condition) > 0) {
                    foreach ($condition as $k => $v) {
                            if ($v != '' && $v != null) {
                                $this->db->where($k, $v);
                            }
                        }
                }
        }
        $this->db->update($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function deleteData($table, $condition)
    {
        if ($condition != null) {
            if (is_array($condition) && count($condition) > 0) {
                    foreach ($condition as $k => $v) {
                            if ($v != '' && $v != null) {
                                $this->db->where($k, $v);
                            }
                        }
                }
        }
        $this->db->delete($table);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get data from any joined tables.
 	 * @author          Chinnu
     * @since           Version 1.0.0
     * @param string $table table name required
     * @return array  lisr array
     * Date:            23-03-2019
     */

    public function selectDataCommon($table, $columns = null, $condition = null, $search_value = null, $search_like = null, $order_by = null, $limit = null, $joins = null, $where_in = null, $where_in_data = null)
    {
        $this->db->select($columns, false)->from($table);
        if (is_array($joins) && count($joins) > 0) {
                foreach ($joins as $k => $v) {
                        $this->db->join($v['table'], $v['condition'], $v['jointype']);
                    }
            }
        if ($condition != null) {
            if (is_array($condition) && count($condition) > 0) {
                    foreach ($condition as $k => $v) {
                            if ($v != '' && $v != null) {
                                $this->db->where($k, $v);
                            }
                        }
                }
        }
        if ($search_value != null && $search_value != '') {
            if (is_array($search_like) && count($search_like) > 0) {
                    $this->db->group_start();
                    foreach ($search_like as $k) {
                            $this->db->or_like($k, $search_value);
                        }
                    $this->db->group_end();
                }
        }
        //Where in clause
        if ($where_in != null && (!empty($where_in_data))) {
            $this->db->where_in($where_in, $where_in_data);
        }
        if (is_array($limit) && count($limit) > 0) {
            $this->db->limit($limit['length'], $limit['start']);
        }
        if ($order_by != null) {
            if (is_array($order_by) && count($order_by) > 0) {
                $this->db->order_by($order_by['key'], $order_by['type']);
            }
        }
        return $query = $this->db->get();
        //return $query->result_array();
    }

    public function deleteWithArray($table, $cond){
        if($this->db->delete($table, $cond)){
            return true;
        } else {
            return false;
        }

    }

    public function batch_insert($table, $insertData){
        $this->db->insert_batch($table, $insertData);
        if($this->db->affected_rows() > 0){
            return true;
        } else {
            return false;
        }
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo_model extends CRM_Model
{


    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all user todos
     * @param  boolean $finished is finished todos or not
     * @param  mixed $page     pagination limit page
     * @return array
     */
    public function get_todo_items($finished, $page = '')
    {

        $pagination_limit = get_option('tables_pagination_limit');
        $this->db->select();
        $this->db->from('tbltodoitems');
        $this->db->where('finished', $finished);
        $this->db->where('staffid', get_staff_user_id());
        $this->db->order_by('item_order', 'asc');

        if ($page != '' && $this->input->post('todo_page')) {
            $position = ($page * $pagination_limit);
            $this->db->limit($pagination_limit, $position);
        } else {
            $this->db->limit(4);
        }
        $todos = $this->db->get()->result_array();

        // format date
        $i = 0;
        foreach ($todos as $todo) {
            $todos[$i]['dateadded']    = _dt($todo['dateadded']);
            $todos[$i]['datefinished'] = _dt($todo['datefinished']);
            $i++;
        }

        return $todos;
    }

    /**
     * Add new user todo
     * @param mixed $data todo $_POST data
     */
    public function add($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['staffid']   = get_staff_user_id();

        $this->db->insert('tbltodoitems', $data);
        return $this->db->insert_id();
    }

    /**
     * Update todo's order / Ajax - Sortable
     * @param  mixed $data todo $_POST data
     */
    public function update_todo_items_order($data)
    {

        for ($i = 0; $i < count($data['data']); $i++) {
            $update = array(
                'item_order' => $data['data'][$i][1],
                'finished' => $data['data'][$i][2]
            );

            if ($data['data'][$i][2] == 1) {
                $update['datefinished'] = date('Y-m-d H:i:s');
            }

            $this->db->where('todoid', $data['data'][$i][0]);
            $this->db->update('tbltodoitems', $update);
        }
    }

    /**
     * Delete todo
     * @param  mixed $id todo id
     * @return boolean
     */
    public function delete_todo_item($id)
    {
        $this->db->where('todoid', $id);
        $this->db->where('staffid', get_staff_user_id());
        $this->db->delete('tbltodoitems');

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Change todo status / finished or not finished
     * @param  mixed $id     todo id
     * @param  integer $status can be passed 1 or 0
     * @return array
     */
    public function change_todo_status($id, $status)
    {
        $this->db->where('todoid', $id);
        $this->db->where('staffid', get_staff_user_id());
        $date = date('Y-m-d H:i:s');
        $this->db->update('tbltodoitems', array(
            'finished' => $status,
            'datefinished' => $date
        ));
        if ($this->db->affected_rows() > 0) {
            return array(
                'success' => true
            );
        }
        return array(
            'success' => false
        );
    }
}

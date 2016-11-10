<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_modes_model extends CRM_Model
{
    private $online_payment_modes = array();
    function __construct()
    {
        $this->online_payment_modes = array(
            array(
                'id' => 'paypal',
                'name' => 'Paypal',
                'description' => '',
                'active' => get_option('paymentmethod_paypal_active')
            ),
            array(
                'id' => 'stripe',
                'name' => 'Stripe',
                'description' => '',
                'active' => get_option('paymentmethod_stripe_active')
            )
        );
        parent::__construct();
    }

    /**
     * Get payment mode
     * @param  integer $id payment mode id
     * @return mixed    if id passed return object else array
     */
    public function get($id = '', $all = false)
    {

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblinvoicepaymentsmodes')->row();
        } else if (!empty($id)) {
            foreach ($this->online_payment_modes as $online_mode) {
                if ($online_mode['id'] == $id) {
                    if ($online_mode['active'] == 0) {
                        continue;
                    }

                    $mode     = new stdCLass();
                    $mode->id = $id;
                    $mode->name->online_mode['name'];
                    $mode->description = $online_mode['description'];
                    return $mode;
                }
            }

            return false;
        }

        if ($all !== true) {
            $this->db->where('active', 1);
        }
        $modes = $this->db->get('tblinvoicepaymentsmodes')->result_array();
        $modes = array_merge($modes, $this->get_online_payment_modes($all));
        return $modes;
    }

    /**
     * Add new payment mode
     * @param array $data payment mode $_POST data
     */
    public function add($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (!isset($data['active'])) {
            $data['active'] = 0;
        } else {
            $data['active'] = 1;
        }

        $this->db->insert('tblinvoicepaymentsmodes', array(
            'name' => $data['name'],
            'description' => nl2br($data['description']),
            'active' => $data['active'],
        ));
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Payment Mode Added [ID: ' . $insert_id . ', Name:' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Update payment mode
     * @param  array $data payment mode $_POST data
     * @return boolean
     */
    public function edit($data)
    {

        $id = $data['paymentmodeid'];
        unset($data['paymentmodeid']);

        if (!isset($data['active'])) {
            $data['active'] = 0;
        } else {
            $data['active'] = 1;
        }



        $this->db->where('id', $id);
        $this->db->update('tblinvoicepaymentsmodes', array(
            'name' => $data['name'],
            'description' => nl2br($data['description']),
            'active' => $data['active'],
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Payment Mode Updated [ID: ' . $id . ', Name:' . $data['name'] . ']');
            return true;
        }

        return false;
    }
    /**
     * Delete payment mode from database
     * @param  mixed $id payment mode id
     * @return mixed / if referenced array else boolean
     */
    public function delete($id)
    {
        // Check if the payment mode is using in the invoiec payment records table.
        if (is_reference_in_table('paymentmode', 'tblinvoicepaymentrecords', $id)) {
            return array(
                'referenced' => true
            );
        }

        $this->db->where('id', $id);
        $this->db->delete('tblinvoicepaymentsmodes');

        if ($this->db->affected_rows() > 0) {
            logActivity('Payment Mode Deleted [' . $id . ']');
            return true;
        }

        return false;
    }
    /**
     * Get all online payment modes
     * @since   1.0.1
     * @return array payment modes
     */
    public function get_online_payment_modes($all = false)
    {

        $modes = array();
        foreach ($this->online_payment_modes as $mode) {
            if ($all !== true) {
                if ($mode['active'] == 0) {
                    continue;
                }
            }
            $modes[] = $mode;
        }

        return $modes;
    }
    /**
     * @since  Version 1.0.1
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update payment mode status Active/Inactive
     */
    public function change_payment_mode_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblinvoicepaymentsmodes', array(
            'active' => $status
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Payment Mode Status Changed [ModeID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }
    /**
     * @since  Version 1.0.1
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update payment mode show to client Active/Inactive
     */
    public function change_payment_mode_show_to_client_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblinvoicepaymentsmodes', array(
            'showtoclient' => $status
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Payment Mode Show to Client Changed [ModeID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }
}

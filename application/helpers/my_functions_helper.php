<?php

/* Add your own functions here */

function get_processor_object_list(){

    $processors = array();

    $files = list_files(FCPATH .'packages/processors');

    foreach ($files as $processor){

        $processors[] = array(
            'name'=>strtolower(basename($processor,'.php'))
        );
    }

    return $processors;

}

function format_processor_status($status, $classes = '', $label = true)
{
    if ($status == 1) {
        $status      = 'Active';
        $label_class = 'success';
    } else {
        // status 0
        $status      = 'InActive';
        $label_class = 'danger';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}

function format_merchant_status($status, $classes = '', $label = true)
{
    if ($status == 1) {
        $status      = 'Active';
        $label_class = 'success';
    } else {
        // status 0
        $status      = 'InActive';
        $label_class = 'danger';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}

function format_trx_status($status, $classes = '', $label = true)
{
    if ($status == 0) {
        $status      = 'Approved';
        $label_class = 'success';
    } else {
        // status 0
        $status      = 'Declined';
        $label_class = 'danger';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}

/**
 * Format invoice status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_transaction_status($status, $classes = '', $label = true)
{
    if ($status == 1) {
        $status      = 'Approved';
        $label_class = 'success';
    } else if ($status == 2) {
        $status      = 'Declined';
        $label_class = 'warning';
    } else {
        $status      = 'Retrived';
        $label_class = 'danger';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class Pdf extends TCPDF
{
	protected $last_page_flag = false;
	function __construct()
	{
		parent::__construct();
	}
	public function Close() {
		$this->last_page_flag = true;
		parent::Close();
	}
	public function Header() {
		$this->SetFont('helvetica', 'B', 20);
	}
	public function Footer() {
        // Position at 15 mm from bottom
		$this->SetY(-15);
        // Set font
		$this->SetFont('helvetica', 'I', 8);
        // Page number
	}
}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */

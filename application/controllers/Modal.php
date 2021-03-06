<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 *  @author   : Creativeitem
 *  date    : 14 september, 2017
 *  Ekattor School Management System Pro
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */
class Modal extends CI_Controller {


	function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->library('session');
    /*cache control*/
    $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
    $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    $this->output->set_header('Pragma: no-cache');
    $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  }

	function popup($page_name = '' , $param2 = '' , $param3 = '', $param4 = '', $param5 = '', $param6 = '', $param7 = '')
	{
		$logged_in_user_role 		= strtolower($this->session->userdata('role'));
		$page_data['param2']		=	$param2;
		$page_data['param3']		=	$param3;
		$page_data['param4']		=	$param4;
		$page_data['param5']		=	$param5;
		$page_data['param6']		=	$param6;
		$page_data['param7']		=	$param7;
		$this->load->view( 'backend/'.$logged_in_user_role.'/'.$page_name.'.php' ,$page_data);
	}

	function upload_video($ins_name , $course)
	{
	    $data = array();
	    $data['directory'] = $ins_name.'/'.$course;
		$this->load->view('backend/upload_video.php' , $data);
	}

	function type_popup($page_name = '' , $param2 = '' , $param3 = '', $param4 = '', $param5 = '', $param6 = '', $param7 = '')
	{
		$logged_in_user_role 		= strtolower($this->session->userdata('role'));
		$page_data['param2']		=	$param2;
		$page_data['param3']		=	$param3;
		$page_data['param4']		=	$param4;
		$page_data['param5']		=	$param5;
		$page_data['param6']		=	$param6;
		$page_data['param7']		=	$param7;
		$this->load->view( 'backend/'.$logged_in_user_role.'/'.$page_name.'.php' ,$page_data);
	}

	public function create_course(){
		$logged_in_user_role = strtolower($this->session->userdata('role'));
		if($logged_in_user_role == 'institute'){
			$institute_id = $this->session->userdata('user_id');
			$page_data['instructors'] = $this->crud_model->sync_instructors($institute_id);	
		}
		$page_data['categories'] = $this->crud_model->get_categories();
        $page_data['page_title'] = get_phrase('add_course');
        $page_data['institutes'] = $this->user_model->get_institute();
 		$page_name = 'create_course_popup';
		$this->load->view( 'backend/'.$logged_in_user_role.'/'.$page_name.'.php' ,$page_data);
	}

	public function create_live_session($param1 = ''){
		$classes = $this->db->get_where('classes', array('course_id' => $param1))->result_array();
		$logged_in_user_role = strtolower($this->session->userdata('role'));
		$page_data['page_title'] = get_phrase('create_live)session');
		$page_data['classes'] = $classes;
		$page_data['course_id'] = $param1;
 		$page_name = 'create_live_session_popup';
		$this->load->view( 'backend/'.$logged_in_user_role.'/'.$page_name.'.php' ,$page_data);

	}

}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('session');
        /*cache control*/
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        if (!$this->session->userdata('cart_items')) {
            $this->session->set_userdata('cart_items', array());
        }
    }

  

    public function sessions()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['page_name'] = 'sessions';
        $page_data['page_title'] = get_phrase('live_sessions');
        $this->load->view('backend/index', $page_data);
    }


    public function get_plans(){
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['plans'] = $this->crud_model->get_all_plans();
        $page_data['page_name'] = 'purchase_plan';
        $page_data['institute_id'] = $this->input->get('institute_id');
        $page_data['page_title'] = get_phrase('purchase_plan');
        $this->load->view('backend/index.php', $page_data);
      }

      public function purchase_plan(){
        $institute_id = $this->input->post('institute_id');
        $plan_id = $this->input->post('plan_id');
        $this->crud_model->purchased_plan($plan_id, $institute_id);
        $this->session->set_flashdata('flash_message', get_phrase('plan_purchased_successfully'));
        redirect(site_url('admin/institutes'), 'refresh');
      }

    public function amazons3_setting($param1='', $param2=''){
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        if($param1 == "add"){
            $this->crud_model->add_s3_settings();
        }elseif($param1 == "edit"){
            $this->crud_model->edit_s3_settings($param2);
        }
        redirect(site_url('admin/amazons3_setting_form/add_form'), 'refresh');
        
    }

    public function live_session($param1='', $param2=''){
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        if($param1 == "add"){
            $data = $this->crud_model->insert_live_session();
            $course_id = $this->input->post('course_id');
            // $page_data['admin_url'] = $data['admin_url'];
            // $page_data['student_url'] = $data['student_url'];
            // $page_data['page_name'] = $data['page_name'];
            // $this->load->view('backend/index.php', $page_data);
            redirect(site_url('admin/course_form/course_edit/'.$course_id), 'refresh');
        }elseif ($param1 == "delete") {
            $live_session = $this->db->get_where('live_sessions', array('id' => $param2))->row_array();
            $this->crud_model->delete_live_session($param2);
            redirect(site_url('admin/course_form/course_edit/'.$live_session['course_id']), 'refresh');
        }
    }

    public function amazons3_setting_form($param1='', $param2=''){
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        if($param1 == "add_form"){
            $page_data['page_name'] = 'amazons3_setting';
            $page_data['page_title'] = get_phrase('s3_setting');
            $this->load->view('backend/index.php', $page_data);
        }
    }

    public function index()
    {
        if ($this->session->userdata('admin_login') == true) {
            $this->dashboard();
        } else {
            redirect(site_url('login'), 'refresh');
        }
    }
    public function dashboard()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['page_name'] = 'dashboard';
        $page_data['page_title'] = get_phrase('dashboard');
        $this->load->view('backend/index.php', $page_data);
    }

    public function blank_template()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['page_name'] = 'blank_template';
        $this->load->view('backend/index.php', $page_data);
    }

    public function ajax_get_instructor()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $institute_id = $this->input->post('institute_id');
        $data = $this->user_model->get_all_instructor($institute_id);
        echo json_encode($data);
    }

    public function categories($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'add') {
            $this->crud_model->add_category();
            $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            redirect(site_url('admin/categories'), 'refresh');
        } elseif ($param1 == "edit") {
            $this->crud_model->edit_category($param2);
            $this->session->set_flashdata('flash_message', get_phrase('data_updated_successfully'));
            redirect(site_url('admin/categories'), 'refresh');
        } elseif ($param1 == "delete") {
            $this->crud_model->delete_category($param2);
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(site_url('admin/categories'), 'refresh');
        }
        $page_data['page_name'] = 'categories';
        $page_data['page_title'] = get_phrase('categories');
        $page_data['categories'] = $this->crud_model->get_categories($param2);
        $this->load->view('backend/index', $page_data);
    }

    public function category_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        if ($param1 == "add_category") {
            $page_data['page_name'] = 'category_add';
            $page_data['categories'] = $this->crud_model->get_categories()->result_array();
            $page_data['page_title'] = get_phrase('add_category');
        }
        if ($param1 == "edit_category") {
            $page_data['page_name'] = 'category_edit';
            $page_data['page_title'] = get_phrase('edit_category');
            $page_data['categories'] = $this->crud_model->get_categories()->result_array();
            $page_data['category_id'] = $param2;
        }

        $this->load->view('backend/index', $page_data);
    }

    public function sub_categories_by_category_id($category_id = 0)
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        $category_id = $this->input->post('category_id');
        redirect(site_url("admin/sub_categories/$category_id"), 'refresh');
    }

    public function sub_category_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'add_sub_category') {
            $page_data['page_name'] = 'sub_category_add';
            $page_data['page_title'] = get_phrase('add_sub_category');
        } elseif ($param1 == 'edit_sub_category') {
            $page_data['page_name'] = 'sub_category_edit';
            $page_data['page_title'] = get_phrase('edit_sub_category');
            $page_data['sub_category_id'] = $param2;
        }
        $page_data['categories'] = $this->crud_model->get_categories();
        $this->load->view('backend/index', $page_data);
    }

    public function instructors($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == "add") {
            $this->user_model->add_user(4);
            redirect(site_url('admin/instructors'), 'refresh');
        } elseif ($param1 == "edit") {
            $this->user_model->edit_user($param2);
            redirect(site_url('admin/instructors'), 'refresh');
        } elseif ($param1 == "delete") {
            $this->user_model->delete_user($param2);
            redirect(site_url('admin/instructors'), 'refresh');
        }

        $page_data['page_name'] = 'instructors';
        $page_data['page_title'] = get_phrase('instructor');
        $page_data['instructors'] = $this->user_model->get_instructors();
        $this->load->view('backend/index', $page_data);
    }

    public function video_form($param1 = "", $param2 = "") {
        if ($this->session->userdata('admin_login') != true) {
          redirect(site_url('login'), 'refresh');
        }
        elseif ($param1 == 'add_video_form') {
          $page_data['page_name'] = 'video_add';
          $page_data['page_title'] = get_phrase('video_add');
          $page_data['institutes'] = $this->user_model->get_institute();
          $this->load->view('backend/index', $page_data);
        }
        elseif ($param1 == 'edit_video_form') {
          $page_data['page_name'] = 'video_edit';
          $page_data['user_id'] = $param2;
          $page_data['institutes'] = $this->user_model->get_institute();
          $page_data['page_title'] = get_phrase('video_edit');
          $this->load->view('backend/index', $page_data);
        }
    }
    
    public function instructor_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == 'add_instructor_form') {
            $page_data['page_name'] = 'instructor_add';
            $page_data['page_title'] = get_phrase('instructor_add');
            $page_data['institutes'] = $this->user_model->get_institute();
            $this->load->view('backend/index', $page_data);
        } elseif ($param1 == 'edit_instructor_form') {
            $page_data['page_name'] = 'instructor_edit';
            $page_data['user_id'] = $param2;
            $page_data['institutes'] = $this->user_model->get_institute();
            $page_data['page_title'] = get_phrase('instructor_edit');
            $this->load->view('backend/index', $page_data);
        }
    }

    public function plans($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == "add") {
            $this->crud_model->add_plan();
            redirect(site_url('admin/plans'), 'refresh');
        } elseif ($param1 == "edit") {
            $this->crud_model->edit_plan($param2);
            redirect(site_url('admin/plans'), 'refresh');
        } elseif ($param1 == "delete") {
            $this->crud_model->delete_plan($param2);
            redirect(site_url('admin/plans'), 'refresh');
        }

        $page_data['page_name'] = 'plans';
        $page_data['page_title'] = get_phrase('plan');
        $page_data['plans'] = $this->crud_model->get_plans();
        $this->load->view('backend/index', $page_data);
    }
    public function plan_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == 'add_plan_form') {
            $page_data['page_name'] = 'plan_add';
            $page_data['institutes'] = $this->user_model->get_institute();
            $page_data['page_title'] = get_phrase('plan_add');
            $this->load->view('backend/index', $page_data);
        } elseif ($param1 == 'edit_plan_form') {
            $page_data['page_name'] = 'plan_edit';
            $page_data['plan_id'] = $param2;
            $page_data['institutes'] = $this->user_model->get_institute();
            $page_data['page_title'] = get_phrase('plan_edit');
            $this->load->view('backend/index', $page_data);
        }
    }

    public function classes($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == "add") {
            $this->crud_model->add_class();
            redirect(site_url('admin/classes'), 'refresh');
        } elseif ($param1 == "edit") {
            $this->crud_model->edit_class($param2);
            redirect(site_url('admin/classes'), 'refresh');
        } elseif ($param1 == "delete") {
            $this->crud_model->delete_class($param2);
            redirect(site_url('admin/classes'), 'refresh');
        }
        $page_data['selected_course_id'] = isset($_GET['course_id']) ? $_GET['course_id'] : "all";
        $page_data['selected_institute_id'] = isset($_GET['institute_id']) ? $_GET['institute_id'] : "all";
        $page_data['selected_instructor_id'] = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : "all";

        $page_data['page_name'] = 'classes';
        $page_data['page_title'] = get_phrase('class');
        // $page_data['courses'] = $this->crud_model->get_classes_course();
        // $page_data['instructors'] = $this->user_model->get_classes_instructor();
        // $page_data['institutes'] = $this->user_model->get_classes_institute();
        // $page_data['live_sessions'] = $arr;
        $page_data['classes'] = $this->crud_model->get_classes($page_data['selected_course_id'],$page_data['selected_institute_id'],$page_data['selected_instructor_id']);
        
        $this->load->view('backend/index', $page_data);
    }

    public function enroll_student($class_id)
    {
        $page_data['page_name'] = 'enroll_students';
        $page_data['page_title'] = 'Enroll Students';
        // $page_data['courses'] = $this->crud_model->get_classes_course();
        // $page_data['instructors'] = $this->user_model->get_classes_instructor();
        // $page_data['institutes'] = $this->user_model->get_classes_institute();
        // $page_data['live_sessions'] = $arr;
        $page_data['class_id'] = $class_id;
        $page_data['course_id'] = $this->user_model->get_course($class_id);
        $page_data['students'] = $this->crud_model->get_students($page_data['course_id']);
        $this->load->view('backend/index', $page_data);
    }
    public function enroll($studentId , $courseId , $classId)
    {
        $this->crud_model->enrol_a_student($courseId, $studentId , $classId);
        redirect(site_url('admin/enroll_student/'.$classId), 'refresh');
    }
    public function class_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == 'add_class_form') {
            $page_data['page_name'] = 'class_add';
            $page_data[] = $this->user_model->get_institute();
            $page_data['institutes'] = $this->user_model->get_institute();
            $page_data['page_title'] = get_phrase('class_add');
            $this->load->view('backend/index', $page_data);
        } elseif ($param1 == 'edit_class_form') {
            $page_data['page_name'] = 'class_edit';
            $page_data['class_id'] = $param2;
            // echo $param3;
            // die;
            $page_data[] = $this->user_model->get_institute();
            $page_data['institutes'] = $this->user_model->get_institute();
            // $page_data['classes'] = $this->user_model->get_institute();
            $page_data['page_title'] = get_phrase('class_edit');
            $this->load->view('backend/index', $page_data);
        }
    }

    public function ajax_sync_course()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $instructor_id = $this->input->post('instructor_id');
        $data = $this->crud_model->sync_courses($instructor_id);
        echo json_encode($data);
    }

    // public function ajax_sync_institute(){
    //   $institute_id = $this->input->post('institute_id')
    //   $data = $this->crud_model->sync_instructors($institute_id);
    // }

    public function institutes($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == "add") {
            $this->user_model->add_user(3);
            redirect(site_url('admin/institutes'), 'refresh');
        } elseif ($param1 == "edit") {
            $this->user_model->edit_user($param2);
            redirect(site_url('admin/institutes'), 'refresh');
        } elseif ($param1 == "delete") {
            $this->user_model->delete_user($param2);
            redirect(site_url('admin/institutes'), 'refresh');
        }

        $page_data['page_name'] = 'institutes';
        $page_data['page_title'] = get_phrase('institute');
        $page_data['institutes'] = $this->user_model->get_institute($param2);
        $this->load->view('backend/index', $page_data);
    }

    public function institute_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } elseif ($param1 == 'add_insttitue_form') {
            $page_data['page_name'] = 'institute_add';
            $page_data['page_title'] = get_phrase('institute_add');
            $this->load->view('backend/index', $page_data);
        } elseif ($param1 == 'edit_institute_form') {
            $page_data['page_name'] = 'institute_edit';
            $page_data['user_id'] = $param2;
            // $page_data['instructors'] = $this->user_model->get_instructors();
            $page_data['page_title'] = get_phrase('institute_edit');
            $this->load->view('backend/index', $page_data);
        }
    }

    public function remove_enrollment($id)
    {
        $this->user_model->delete_enrolled_user($id);
        redirect(site_url('admin/classes'), 'refresh');
    }

    public function users($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        if ($param1 == "add") {
            $this->user_model->add_user(2, $param2);
            redirect(site_url('admin/classes'), 'refresh');
        }
        if ($param1 == "edit") {
            $this->user_model->edit_user($param2);
            redirect(site_url('admin/classes'), 'refresh');
        } elseif ($param1 == "delete") {
            $this->user_model->delete_user($param2);
            redirect(site_url('admin/classes'), 'refresh');
        }

        $page_data['page_name'] = 'users';
        $page_data['page_title'] = get_phrase('student');
        $page_data['users'] = $this->user_model->get_user($param2);
        $this->load->view('backend/index', $page_data);
    }

    public function import_students($param1 = '', $param2 = ''){
          if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
          $page_data['class_id'] = $param2;
          $page_data['page_name'] = 'import_students';
          $page_data['page_title'] = get_phrase('import_students');
          $this->load->view('backend/index', $page_data);
      }

    public function user_form($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'add_user_form') {
            $page_data['page_name'] = 'user_add';
            $page_data['page_title'] = get_phrase('student_add');
            $this->load->view('backend/index', $page_data);
        } elseif ($param1 == 'edit_user_form') {
            $page_data['page_name'] = 'user_edit';
            $page_data['user_id'] = $param2;
            $page_data['page_title'] = get_phrase('student_edit');
            $this->load->view('backend/index', $page_data);
        }
    }

    public function class_id($param1 = '', $param2 = ''){
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        } else {
            if ($param2 == 'add_student'){
            $page_data['class_id'] = $param1;
            $page_data['page_name'] = 'user_add';
            $page_data['page_title'] = get_phrase('student_add');

            $this->load->view('backend/index', $page_data);
            }
            elseif($param2 == 'users'){
            $page_data['class_id'] = $param1;
            $page_data['page_name'] = 'users';
            $page_data['page_title'] = get_phrase('students');
            $page_data['users'] = $this->user_model->get_class_enrolled_students($param1);
            $this->load->view('backend/index', $page_data);
            }
        }
    }

    public function enrol_history($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 != "") {
            $date_range = $this->input->get('date_range');
            $date_range = explode(" - ", $date_range);
            $page_data['timestamp_start'] = strtotime($date_range[0]);
            $page_data['timestamp_end'] = strtotime($date_range[1]);
        } else {
            $first_day_of_month = "1 " . date("M") . " " . date("Y") . ' 00:00:00';
            $last_day_of_month = date("t") . " " . date("M") . " " . date("Y") . ' 23:59:59';
            $page_data['timestamp_start'] = strtotime($first_day_of_month);
            $page_data['timestamp_end'] = strtotime($last_day_of_month);
        }
        $page_data['page_name'] = 'enrol_history';
        $page_data['enrol_history'] = $this->crud_model->enrol_history_by_date_range($page_data['timestamp_start'], $page_data['timestamp_end']);
        $page_data['page_title'] = get_phrase('enrol_history');
        $this->load->view('backend/index', $page_data);
    }

    public function enrol_student($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        if ($param1 == 'enrol') {
            $this->crud_model->enrol_a_student_manually();
            redirect(site_url('admin/enrol_history'), 'refresh');
        }
        $page_data['page_name'] = 'enrol_student';
        $page_data['page_title'] = get_phrase('enrol_a_student');
        $this->load->view('backend/index', $page_data);
    }
    public function admin_revenue($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 != "") {
            $date_range = $this->input->get('date_range');
            $date_range = explode(" - ", $date_range);
            $page_data['timestamp_start'] = strtotime($date_range[0]);
            $page_data['timestamp_end'] = strtotime($date_range[1]);
        } else {
            $page_data['timestamp_start'] = strtotime('-29 days', time());
            $page_data['timestamp_end'] = strtotime(date("m/d/Y"));
        }

        $page_data['page_name'] = 'admin_revenue';
        $page_data['payment_history'] = $this->crud_model->get_revenue_by_user_type($page_data['timestamp_start'], $page_data['timestamp_end'], 'admin_revenue');
        $page_data['page_title'] = get_phrase('admin_revenue');
        $this->load->view('backend/index', $page_data);
    }

    public function instructor_revenue($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        $page_data['page_name'] = 'instructor_revenue';
        $page_data['payment_history'] = $this->crud_model->get_revenue_by_user_type("", "", 'instructor_revenue');
        $page_data['page_title'] = get_phrase('instructor_revenue');
        $this->load->view('backend/index', $page_data);
    }

    public function invoice($payment_id = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['page_name'] = 'invoice';
        $page_data['payment_details'] = $this->crud_model->get_payment_details_by_id($payment_id);
        $page_data['page_title'] = get_phrase('invoice');
        $this->load->view('backend/index', $page_data);
    }

    public function payment_history_delete($param1 = "", $redirect_to = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $this->crud_model->delete_payment_history($param1);
        $this->session->set_flashdata('flash_message', get_phrase('data_deleted_successfully'));
        redirect(site_url('admin/' . $redirect_to), 'refresh');
    }

    public function enrol_history_delete($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $this->crud_model->delete_enrol_history($param1);
        $this->session->set_flashdata('flash_message', get_phrase('data_deleted_successfully'));
        redirect(site_url('admin/enrol_history'), 'refresh');
    }

    public function purchase_history()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['page_name'] = 'purchase_history';
        $page_data['purchase_history'] = $this->crud_model->purchase_history();
        $page_data['page_title'] = get_phrase('purchase_history');
        $this->load->view('backend/index', $page_data);
    }

    public function system_settings($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'system_update') {
            $this->crud_model->update_system_settings();
            $this->session->set_flashdata('flash_message', get_phrase('system_settings_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }

        if ($param1 == 'logo_upload') {
            move_uploaded_file($_FILES['logo']['tmp_name'], 'assets/backend/logo.png');
            $this->session->set_flashdata('flash_message', get_phrase('backend_logo_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }

        if ($param1 == 'favicon_upload') {
            move_uploaded_file($_FILES['favicon']['tmp_name'], 'assets/favicon.png');
            $this->session->set_flashdata('flash_message', get_phrase('favicon_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }

        $page_data['languages'] = $this->crud_model->get_all_languages();
        $page_data['page_name'] = 'system_settings';
        $page_data['page_title'] = get_phrase('system_settings');
        $this->load->view('backend/index', $page_data);
    }

    public function frontend_settings($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'frontend_update') {
            $this->crud_model->update_frontend_settings();
            $this->session->set_flashdata('flash_message', get_phrase('frontend_settings_updated'));
            redirect(site_url('admin/frontend_settings'), 'refresh');
        }

        if ($param1 == 'banner_image_update') {
            $this->crud_model->update_frontend_banner();
            $this->session->set_flashdata('flash_message', get_phrase('banner_image_update'));
            redirect(site_url('admin/frontend_settings'), 'refresh');
        }
        if ($param1 == 'light_logo') {
            $this->crud_model->update_light_logo();
            $this->session->set_flashdata('flash_message', get_phrase('logo_updated'));
            redirect(site_url('admin/frontend_settings'), 'refresh');
        }
        if ($param1 == 'dark_logo') {
            $this->crud_model->update_dark_logo();
            $this->session->set_flashdata('flash_message', get_phrase('logo_updated'));
            redirect(site_url('admin/frontend_settings'), 'refresh');
        }
        if ($param1 == 'small_logo') {
            $this->crud_model->update_small_logo();
            $this->session->set_flashdata('flash_message', get_phrase('logo_updated'));
            redirect(site_url('admin/frontend_settings'), 'refresh');
        }
        if ($param1 == 'favicon') {
            $this->crud_model->update_favicon();
            $this->session->set_flashdata('flash_message', get_phrase('favicon_updated'));
            redirect(site_url('admin/frontend_settings'), 'refresh');
        }

        $page_data['page_name'] = 'frontend_settings';
        $page_data['page_title'] = get_phrase('frontend_settings');
        $this->load->view('backend/index', $page_data);
    }
    public function payment_settings($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'system_currency') {
            $this->crud_model->update_system_currency();
            redirect(site_url('admin/payment_settings'), 'refresh');
        }
        if ($param1 == 'paypal_settings') {
            $this->crud_model->update_paypal_settings();
            redirect(site_url('admin/payment_settings'), 'refresh');
        }
        if ($param1 == 'stripe_settings') {
            $this->crud_model->update_stripe_settings();
            redirect(site_url('admin/payment_settings'), 'refresh');
        }

        $page_data['page_name'] = 'payment_settings';
        $page_data['page_title'] = get_phrase('payment_settings');
        $this->load->view('backend/index', $page_data);
    }

    public function smtp_settings($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'update') {
            $this->crud_model->update_smtp_settings();
            $this->session->set_flashdata('flash_message', get_phrase('smtp_settings_updated_successfully'));
            redirect(site_url('admin/smtp_settings'), 'refresh');
        }

        $page_data['page_name'] = 'smtp_settings';
        $page_data['page_title'] = get_phrase('smtp_settings');
        $this->load->view('backend/index', $page_data);
    }

    public function instructor_settings($param1 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        if ($param1 == 'update') {
            $this->crud_model->update_instructor_settings();
            $this->session->set_flashdata('flash_message', get_phrase('instructor_settings_updated'));
            redirect(site_url('admin/instructor_settings'), 'refresh');
        }

        $page_data['page_name'] = 'instructor_settings';
        $page_data['page_title'] = get_phrase('instructor_settings');
        $this->load->view('backend/index', $page_data);
    }

    public function theme_settings($action = '')
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $page_data['page_name'] = 'theme_settings';
        $page_data['page_title'] = get_phrase('theme_settings');
        $this->load->view('backend/index', $page_data);
    }

    public function theme_actions($action = "", $theme = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($action == 'activate') {
            $theme_to_active = $this->input->post('theme');
            $installed_themes = $this->crud_model->get_installed_themes();
            if (in_array($theme_to_active, $installed_themes)) {
                $this->crud_model->activate_theme($theme_to_active);
                echo true;
            } else {
                echo false;
            }
        } elseif ($action == 'remove') {
            if ($theme == get_frontend_settings('theme')) {
                $this->session->set_flashdata('error_message', get_phrase('activate_a_theme_first'));
            } else {
                $this->crud_model->remove_files_and_folders(APPPATH . '/views/frontend/' . $theme);
                $this->crud_model->remove_files_and_folders(FCPATH . '/assets/frontend/' . $theme);
                $this->session->set_flashdata('flash_message', $theme . ' ' . get_phrase('theme_removed_successfully'));
            }
            redirect(site_url('admin/theme_settings'), 'refresh');
        }

    }

    public function courses()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        $page_data['selected_category_id'] = isset($_GET['category_id']) ? $_GET['category_id'] : "all";
        $page_data['selected_institute_id'] = isset($_GET['institute_id']) ? $_GET['institute_id'] : "all";
        $page_data['selected_price'] = isset($_GET['price']) ? $_GET['price'] : "all";
        $page_data['selected_status'] = isset($_GET['status']) ? $_GET['status'] : "all";

        // Courses query is used for deciding if there is any course or not. Check the view you will get it
        $page_data['courses'] = $this->crud_model->filter_course_for_backend($page_data['selected_category_id'], $page_data['selected_institute_id'], $page_data['selected_price'], $page_data['selected_status']);
        $page_data['status_wise_courses'] = $this->crud_model->get_status_wise_courses();
        $page_data['institutes'] = $this->user_model->get_institutes();
        $page_data['page_name'] = 'courses';
        $page_data['categories'] = $this->crud_model->get_categories();
        $page_data['page_title'] = get_phrase('active_courses');
        $this->load->view('backend/index', $page_data);
    }

    // This function is responsible for loading the course data from server side for datatable SILENTLY
    public function get_courses()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $courses = array();
        // Filter portion
        $filter_data['selected_category_id'] = $this->input->post('selected_category_id');
        $filter_data['selected_instructor_id'] = $this->input->post('selected_instructor_id');
        $filter_data['selected_price'] = $this->input->post('selected_price');
        $filter_data['selected_status'] = $this->input->post('selected_status');

        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'title',
            2 => 'category',
            3 => 'lesson_and_section',
            4 => 'enrolled_student',
            5 => 'status',
            6 => 'price',
            7 => 'actions',
            8 => 'course_id',
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir = $this->input->post('order')[0]['dir'];

        $totalData = $this->lazyload->count_all_courses($filter_data);
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        if (empty($this->input->post('search')['value'])) {
            $courses = $this->lazyload->courses($limit, $start, $order, $dir, $filter_data);
        } else {
            $search = $this->input->post('search')['value'];
            $courses = $this->lazyload->course_search($limit, $start, $search, $order, $dir, $filter_data);
            $totalFiltered = $this->lazyload->course_search_count($search);
        }

        // Fetch the data and make it as JSON format and return it.
        $data = array();
        if (!empty($courses)) {
            foreach ($courses as $key => $row) {
                $instructor_details = $this->user_model->get_all_user($row->user_id)->row_array();
                $category_details = $this->crud_model->get_category_details_by_id($row->sub_category_id)->row_array();
                $sections = $this->crud_model->get_section('course', $row->id);
                $lessons = $this->crud_model->get_lessons('course', $row->id);
                $enroll_history = $this->crud_model->enrol_history($row->id);

                $status_badge = "badge-success-lighten";
                if ($row->status == 'pending') {
                    $status_badge = "badge-danger-lighten";
                } elseif ($row->status == 'draft') {
                    $status_badge = "badge-dark-lighten";
                }

                $price_badge = "badge-dark-lighten";
                $price = 0;
                if ($row->is_free_course == null) {
                    if ($row->discount_flag == 1) {
                        $price = currency($row->discounted_price);
                    } else {
                        $price = currency($row->price);
                    }
                } elseif ($row->is_free_course == 1) {
                    $price_badge = "badge-success-lighten";
                    $price = get_phrase('free');
                }

                $view_course_on_frontend_url = site_url('home/course/' . slugify($row->title) . '/' . $row->id);
                $edit_this_course_url = site_url('admin/course_form/course_edit/' . $row->id);
                $section_and_lesson_url = site_url('admin/course_form/course_edit/' . $row->id);

                if ($row->status == 'active') {
                    $course_status_changing_message = get_phrase('mark_as_pending');
                    if ($row->user_id != $this->session->userdata('user_id')) {
                        $course_status_changing_action = "showAjaxModal('" . site_url('modal/popup/mail_on_course_status_changing_modal/pending/' . $row->id . '/' . $filter_data['selected_category_id'] . '/' . $filter_data['selected_instructor_id'] . '/' . $filter_data['selected_price'] . '/' . $filter_data['selected_status']) . "', '" . $course_status_changing_message . "')";
                    } else {
                        $course_status_changing_action = "confirm_modal('" . site_url('admin/change_course_status_for_admin/pending/' . $row->id . '/' . $filter_data['selected_category_id'] . '/' . $filter_data['selected_instructor_id'] . '/' . $filter_data['selected_price'] . '/' . $filter_data['selected_status']) . "')";
                    }
                } else {
                    $course_status_changing_message = get_phrase('mark_as_active');
                    if ($row->user_id != $this->session->userdata('user_id')) {
                        $course_status_changing_action = "showAjaxModal('" . site_url('modal/popup/mail_on_course_status_changing_modal/active/' . $row->id . '/' . $filter_data['selected_category_id'] . '/' . $filter_data['selected_instructor_id'] . '/' . $filter_data['selected_price'] . '/' . $filter_data['selected_status']) . "', '" . $course_status_changing_message . "')";
                    } else {
                        $course_status_changing_action = "confirm_modal('" . site_url('admin/change_course_status_for_admin/active/' . $row->id . '/' . $filter_data['selected_category_id'] . '/' . $filter_data['selected_instructor_id'] . '/' . $filter_data['selected_price'] . '/' . $filter_data['selected_status']) . "')";
                    }
                }

                $delete_course_url = "confirm_modal('" . site_url('admin/course_actions/delete/' . $row->id) . "')";

                $action = '
        <div class="dropright dropright">
        <button type="button" class="btn btn-sm btn-outline-primary btn-rounded btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="mdi mdi-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="' . $view_course_on_frontend_url . '" target="_blank">' . get_phrase("view_course_on_frontend") . '</a></li>
        <li><a class="dropdown-item" href="' . $edit_this_course_url . '">' . get_phrase("edit_this_course") . '</a></li>
        <li><a class="dropdown-item" href="' . $section_and_lesson_url . '">' . get_phrase("section_and_lesson") . '</a></li>
        <li><a class="dropdown-item" href="javascript::" onclick="' . $course_status_changing_action . '">' . $course_status_changing_message . '</a></li>
        <li><a class="dropdown-item" href="javascript::" onclick="' . $delete_course_url . '">' . get_phrase("delete") . '</a></li>
        </ul>
        </div>
        ';

                $nestedData['#'] = $key + 1;

                $nestedData['title'] = '<strong><a href="' . site_url('admin/course_form/course_edit/' . $row->id) . '" title="' . $row->title . '">' . substr($row->title, 0, 25) . '</a></strong><br>
        <small title="' . $instructor_details['first_name'] . $instructor_details['last_name'] . '" class="text-muted">' . get_phrase('instructor') . ': <b>' . substr($instructor_details['first_name'], 0, 10) . '</b></small>';

                $nestedData['category'] = '<span class="badge badge-dark-lighten">' . $category_details['name'] . '</span>';

                $nestedData['lesson_and_section'] = '
        <small class="text-muted"><b>' . get_phrase('total_section') . '</b>: ' . $sections->num_rows() . '</small><br>
        <small class="text-muted"><b>' . get_phrase('total_lesson') . '</b>: ' . $lessons->num_rows() . '</small><br>';

                $nestedData['enrolled_student'] = '<small class="text-muted"><b>' . get_phrase('total_enrolment') . '</b>: ' . $enroll_history->num_rows() . '</small>';

                $nestedData['status'] = '<span class="badge ' . $status_badge . '">' . get_phrase($row->status) . '</span>';

                $nestedData['price'] = '<span class="badge ' . $price_badge . '">' . get_phrase($price) . '</span>';

                $nestedData['actions'] = $action;

                $nestedData['course_id'] = $row->id;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function pending_courses()
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        $page_data['page_name'] = 'pending_courses';
        $page_data['page_title'] = get_phrase('pending_courses');
        $this->load->view('backend/index', $page_data);
    }

    public function course_actions($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == "add") {
            $instructor_id = $this->input->post('instructors');
            if($instructor_id == ''){
              $this->session->set_flashdata('error_message', get_phrase('please_select_a_instructor'));
              redirect(site_url('admin/course_form/add_course/'), 'refresh');
            }
            $course_id = $this->crud_model->add_course('', $instructor_id);
            if ($course_id > 0){
            redirect(site_url('admin/course_form/course_edit/' . $course_id), 'refresh');
            }else{
                redirect(site_url('admin/courses/'), 'refresh');
            }

        } elseif ($param1 == "edit") {
            $this->crud_model->update_course($param2);
            redirect(site_url('admin/courses'), 'refresh');

        } elseif ($param1 == 'delete') {
            $this->is_drafted_course($param2);
            $this->crud_model->delete_course($param2);
            redirect(site_url('admin/courses'), 'refresh');
        }
    }

    public function course_form($param1 = "", $param2 = "")
    {

        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'add_course') {
            $page_data['languages'] = $this->crud_model->get_all_languages();
            $page_data['categories'] = $this->crud_model->get_categories();
            $page_data['page_name'] = 'course_add';
            $page_data['page_title'] = get_phrase('add_course');
            $page_data['institutes'] = $this->user_model->get_institute();
            $this->load->view('backend/index', $page_data);

        } elseif ($param1 == 'course_edit') {
            $this->is_drafted_course($param2);
            $page_data['page_name'] = 'course_edit';
            $page_data['course_id'] = $param2;
            $page_data['page_title'] = get_phrase('edit_course');
            $page_data['languages'] = $this->crud_model->get_all_languages();
            $page_data['categories'] = $this->crud_model->get_categories();
            $page_data['institutes'] = $this->user_model->get_institute();
            $this->load->view('backend/index', $page_data);
        }
    }

    private function is_drafted_course($course_id)
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
        if ($course_details['status'] == 'draft') {
            $this->session->set_flashdata('error_message', get_phrase('you_do_not_have_right_to_access_this_course'));
            redirect(site_url('admin/courses'), 'refresh');
        }
    }

    public function change_course_status($updated_status = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $course_id = $this->input->post('course_id');
        $category_id = $this->input->post('category_id');
        $instructor_id = $this->input->post('instructor_id');
        $price = $this->input->post('price');
        $status = $this->input->post('status');
        if (isset($_POST['mail_subject']) && isset($_POST['mail_body'])) {
            $mail_subject = $this->input->post('mail_subject');
            $mail_body = $this->input->post('mail_body');
            $this->email_model->send_mail_on_course_status_changing($course_id, $mail_subject, $mail_body);
        }
        $this->crud_model->change_course_status($updated_status, $course_id);
        $this->session->set_flashdata('flash_message', get_phrase('course_'.$updated_status.'_updated'));
        redirect('admin/courses');
    }

    public function change_course_type($updated_type = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $course_id = $this->input->post('course_id');
        $category_id = $this->input->post('category_id');
        $instructor_id = $this->input->post('instructor_id');
        $price = $this->input->post('price');
        $status = $this->input->post('status');
        $type = $this->input->post('type');
        if (isset($_POST['mail_subject']) && isset($_POST['mail_body'])) {
            $mail_subject = $this->input->post('mail_subject');
            $mail_body = $this->input->post('mail_body');
            $this->email_model->send_mail_on_course_status_changing($course_id, $mail_subject, $mail_body);
        }
        $this->crud_model->change_course_type($updated_type, $course_id);
        $this->session->set_flashdata('flash_message', get_phrase('course_'.$type.'_updated'));
        redirect('admin/courses');
    }

    public function change_course_status_for_admin($updated_status = "", $course_id = "", $category_id = "", $status = "", $instructor_id = "", $price = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $this->crud_model->change_course_status($updated_status, $course_id);
        $this->session->set_flashdata('flash_message', get_phrase('course_status_updated'));
        redirect(site_url('admin/courses?category_id=' . $category_id . '&status=' . $status . '&instructor_id=' . $instructor_id . '&price=' . $price), 'refresh');
    }

    public function sections($param1 = "", $param2 = "", $param3 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param2 == 'add') {
            $this->crud_model->add_section($param1);
            $this->session->set_flashdata('flash_message', get_phrase('section_has_been_added_successfully'));
        } elseif ($param2 == 'edit') {
            $this->crud_model->edit_section($param3);
            $this->session->set_flashdata('flash_message', get_phrase('section_has_been_updated_successfully'));
        } elseif ($param2 == 'delete') {
            $this->crud_model->delete_section($param1, $param3);
            $this->session->set_flashdata('flash_message', get_phrase('section_has_been_deleted_successfully'));
        }
        redirect(site_url('admin/course_form/course_edit/' . $param1));
    }

    public function lessons($course_id = "", $param1 = "", $param2 = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $course = $this->db->get_where('course', array('id' => $course_id))->row_array();
        $institute = $this->db->get_where('users', array('id' => $course['institute_id']))->row_array();
        $institute_name = $institute['first_name'] . '_'.$institute['last_name'];
        if ($param1 == 'add') {
            $this->crud_model->add_lesson( $institute_name);
            // $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_added_successfully'));
            redirect('admin/course_form/course_edit/' . $course_id);
        } elseif ($param1 == 'edit') {
            $this->crud_model->edit_lesson($param2,  $institute_name);
            $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_updated_successfully'));
            redirect('admin/course_form/course_edit/' . $course_id);
        } elseif ($param1 == 'delete') {
            $this->crud_model->delete_lesson($param2);
            $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_deleted_successfully'));
            redirect('admin/course_form/course_edit/' . $course_id);
        } elseif ($param1 == 'filter') {
            redirect('admin/lessons/' . $this->input->post('course_id'));
        }
        $page_data['page_name'] = 'lessons';
        $page_data['lessons'] = $this->crud_model->get_lessons('course', $course_id);
        $page_data['course_id'] = $course_id;
        $page_data['page_title'] = get_phrase('lessons');
        $this->load->view('backend/index', $page_data);
    }

    public function watch_video($slugified_title = "", $lesson_id = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $lesson_details = $this->crud_model->get_lessons('lesson', $lesson_id)->row_array();
        $page_data['provider'] = $lesson_details['video_type'];
        $page_data['video_url'] = $lesson_details['video_url'];
        $page_data['lesson_id'] = $lesson_id;
        $page_data['page_name'] = 'video_player';
        $page_data['page_title'] = get_phrase('video_player');
        $this->load->view('backend/index', $page_data);
    }

    // Language Functions
    public function manage_language($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'add_language') {
            saveDefaultJSONFile(trimmer($this->input->post('language')));
            $this->session->set_flashdata('flash_message', get_phrase('language_added_successfully'));
            redirect(site_url('admin/manage_language'), 'refresh');
        }
        if ($param1 == 'add_phrase') {
            $new_phrase = get_phrase($this->input->post('phrase'));
            $this->session->set_flashdata('flash_message', $new_phrase . ' ' . get_phrase('has_been_added_successfully'));
            redirect(site_url('admin/manage_language'), 'refresh');
        }

        if ($param1 == 'edit_phrase') {
            $page_data['edit_profile'] = $param2;
        }

        if ($param1 == 'delete_language') {
            if (file_exists('application/language/' . $param2 . '.json')) {
                unlink('application/language/' . $param2 . '.json');
                $this->session->set_flashdata('flash_message', get_phrase('language_deleted_successfully'));
                redirect(site_url('admin/manage_language'), 'refresh');
            }
        }
        $page_data['languages'] = $this->crud_model->get_all_languages();
        $page_data['page_name'] = 'manage_language';
        $page_data['page_title'] = get_phrase('multi_language_settings');
        $this->load->view('backend/index', $page_data);
    }

    public function update_phrase_with_ajax()
    {
        $current_editing_language = $this->input->post('currentEditingLanguage');
        $updatedValue = $this->input->post('updatedValue');
        $key = $this->input->post('key');
        saveJSONFile($current_editing_language, $key, $updatedValue);
        echo $current_editing_language . ' ' . $key . ' ' . $updatedValue;
    }

    public function message($param1 = 'message_home', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'send_new') {
            $message_thread_code = $this->crud_model->send_new_private_message();
            $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
            redirect(site_url('admin/message/message_read/' . $message_thread_code), 'refresh');
        }

        if ($param1 == 'send_reply') {
            $this->crud_model->send_reply_message($param2); //$param2 = message_thread_code
            $this->session->set_flashdata('flash_message', get_phrase('message_sent!'));
            redirect(site_url('admin/message/message_read/' . $param2), 'refresh');
        }

        if ($param1 == 'message_read') {
            $page_data['current_message_thread_code'] = $param2; // $param2 = message_thread_code
            $this->crud_model->mark_thread_messages_read($param2);
        }
        $page_data['institutes'] = $this->user_model->get_institute();
        $page_data['message_inner_page_name'] = $param1;
        $page_data['page_name'] = 'message';
        $page_data['page_title'] = get_phrase('private_messaging');
        $this->load->view('backend/index', $page_data);
    }


    public function ajax_sync_classes(){
        if ($this->session->userdata('admin_login') != true) {
          redirect(site_url('login'), 'refresh');
        }
        $course_id = $this->input->post('course_id');
        $data = $this->crud_model->single_instructor_cls($course_id);
          echo json_encode($data);
      }

      public function ajax_sync_students(){
        if ($this->session->userdata('admin_login') != true) {
          redirect(site_url('login'), 'refresh');
        }
        $course_id = $this->input->post('course_id');
        $data = $this->crud_model->get_course_students($course_id);
          echo json_encode($data);
      }

    /******MANAGE OWN PROFILE AND CHANGE PASSWORD***/
    public function profile($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'update_profile_info') {
            $this->user_model->edit_user($param2);
            redirect(site_url('admin/manage_profile'), 'refresh');
        }
        if ($param1 == 'change_password') {
            $this->user_model->change_password($param2);
            redirect(site_url('admin/manage_profile'), 'refresh');
        }
        $page_data['page_name'] = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data'] = $this->db->get_where('users', array(
            'id' => $this->session->userdata('user_id'),
        ))->result_array();
        $this->load->view('backend/index', $page_data);
    }

    public function paypal_checkout_for_instructor_revenue()
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        $page_data['amount_to_pay'] = $this->input->post('amount_to_pay');
        $page_data['payment_id'] = $this->input->post('payment_id');
        $page_data['course_title'] = $this->input->post('course_title');
        $page_data['instructor_name'] = $this->input->post('instructor_name');
        $page_data['production_client_id'] = $this->input->post('production_client_id');
        $this->load->view('backend/admin/paypal_checkout_for_instructor_revenue', $page_data);
    }

    // PAYPAL CHECKOUT ACTIONS
    public function paypal_payment($payment_id = "", $paypalPaymentID = "", $paypalPaymentToken = "", $paypalPayerID = "")
    {
        $payment_details = $this->db->get_where('payment', array('id' => $payment_id))->row_array();
        $course_id = $payment_details['course_id'];
        $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
        $instructor_id = $course_details['user_id'];
        $instructor_data = $this->db->get_where('users', array('id' => $instructor_id))->row_array();
        $paypal_keys = json_decode($instructor_data['paypal_keys'], true);
        $production_client_id = $paypal_keys[0]['production_client_id'];
        $production_secret_key = $paypal_keys[0]['production_secret_key'];
        // $production_client_id = 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R';
        // $production_secret_key = 'EFI50pO1s1cV1cySQ85wg2Pncn4VOPxKvTLBhyeGtd1QHNac-OJFsQlS7GAwlXZSg2wtm-BOJ9Ar3XJy';

        //THIS IS HOW I CHECKED THE PAYPAL PAYMENT STATUS
        $status = $this->payment_model->paypal_payment($paypalPaymentID, $paypalPaymentToken, $paypalPayerID, $production_client_id, $production_secret_key);
        if (!$status) {
            $this->session->set_flashdata('error_message', get_phrase('an_error_occurred_during_payment'));
            redirect(site_url('admin/instructor_revenue'), 'refresh');
        }
        $this->crud_model->update_instructor_payment_status($payment_id);
        $this->session->set_flashdata('flash_message', get_phrase('instructor_payment_has_been_done'));
        redirect(site_url('admin/instructor_revenue'), 'refresh');
    }

    public function stripe_checkout_for_instructor_revenue()
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        $page_data['amount_to_pay'] = $this->input->post('amount_to_pay');
        $page_data['payment_id'] = $this->input->post('payment_id');
        $page_data['course_title'] = $this->input->post('course_title');
        $page_data['instructor_name'] = $this->input->post('instructor_name');
        $page_data['public_live_key'] = $this->input->post('public_live_key');
        $page_data['secret_live_key'] = $this->input->post('secret_live_key');
        $this->load->view('backend/admin/stripe_checkout_for_instructor_revenue', $page_data);
    }

    // STRIPE CHECKOUT ACTIONS
    public function stripe_payment($payment_id = "")
    {
        //THIS IS HOW I CHECKED THE STRIPE PAYMENT STATUS
        $token_id = $this->input->post('stripeToken');
        $payment_details = $this->db->get_where('payment', array('id' => $payment_id))->row_array();
        $course_id = $payment_details['course_id'];
        $course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
        $instructor_id = $course_details['user_id'];
        $instructor_data = $this->db->get_where('users', array('id' => $instructor_id))->row_array();
        $stripe_keys = json_decode($instructor_data['stripe_keys'], true);

        $status = $this->payment_model->stripe_payment($token_id, $this->session->userdata('user_id'), $payment_details['instructor_revenue'], $stripe_keys[0]['secret_live_key']);

        if (!$status) {
            $this->session->set_flashdata('error_message', get_phrase('an_error_occurred_during_payment'));
            redirect(site_url('admin/instructor_revenue'), 'refresh');
        }
        $this->crud_model->update_instructor_payment_status($payment_id);
        $this->session->set_flashdata('flash_message', get_phrase('instructor_payment_has_been_done'));
        redirect(site_url('admin/instructor_revenue'), 'refresh');
    }

    public function preview($course_id = '')
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        $this->is_drafted_course($course_id);
        if ($course_id > 0) {
            $courses = $this->crud_model->get_course_by_id($course_id);
            if ($courses->num_rows() > 0) {
                $course_details = $courses->row_array();
                redirect(site_url('home/lesson/' . slugify($course_details['title']) . '/' . $course_details['id']), 'refresh');
            }
        }
        redirect(site_url('admin/courses'), 'refresh');
    }

    // Manage Quizes
    public function quizes($course_id = "", $action = "", $quiz_id = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($action == 'add') {
            $this->crud_model->add_quiz($course_id);
            $this->session->set_flashdata('flash_message', get_phrase('quiz_has_been_added_successfully'));
        } elseif ($action == 'edit') {
            $this->crud_model->edit_quiz($quiz_id);
            $this->session->set_flashdata('flash_message', get_phrase('quiz_has_been_updated_successfully'));
        } elseif ($action == 'delete') {
            $this->crud_model->delete_section($course_id, $quiz_id);
            $this->session->set_flashdata('flash_message', get_phrase('quiz_has_been_deleted_successfully'));
        }
        redirect(site_url('admin/course_form/course_edit/' . $course_id));
    }

    // Manage Quize Questions
    public function quiz_questions($quiz_id = "", $action = "", $question_id = "")
    {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }
        $quiz_details = $this->crud_model->get_lessons('lesson', $quiz_id)->row_array();

        if ($action == 'add') {
            $response = $this->crud_model->add_quiz_questions($quiz_id);
            echo $response;
        } elseif ($action == 'edit') {
            $response = $this->crud_model->update_quiz_questions($question_id);
            echo $response;
        } elseif ($action == 'delete') {
            $response = $this->crud_model->delete_quiz_question($question_id);
            $this->session->set_flashdata('flash_message', get_phrase('question_has_been_deleted'));
            redirect(site_url('admin/course_form/course_edit/' . $quiz_details['course_id']));
        }
    }

    // software about page
    public function about()
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        $page_data['application_details'] = $this->crud_model->get_application_details();
        $page_data['page_name'] = 'about';
        $page_data['page_title'] = get_phrase('about');
        $this->load->view('backend/index', $page_data);
    }

    public function install_theme($theme_to_install = '')
    {

        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        $uninstalled_themes = $this->crud_model->get_uninstalled_themes();
        if (!in_array($theme_to_install, $uninstalled_themes)) {
            $this->session->set_flashdata('error_message', get_phrase('this_theme_is_not_available'));
            redirect(site_url('admin/theme_settings'));
        }

        $zipped_file_name = $theme_to_install;
        $unzipped_file_name = substr($zipped_file_name, 0, -4);
        // Create update directory.
        $views_directory = 'application/views/frontend';
        $assets_directory = 'assets/frontend';

        //Unzip theme zip file and remove zip file.
        $theme_path = 'themes/' . $zipped_file_name;
        $theme_zip = new ZipArchive;
        $theme_result = $theme_zip->open($theme_path);
        if ($theme_result === true) {
            $theme_zip->extractTo('themes');
            $theme_zip->close();
        }

        // unzip the views zip file to the application>views folder
        $views_path = 'themes/' . $unzipped_file_name . '/views/' . $zipped_file_name;
        $views_zip = new ZipArchive;
        $views_result = $views_zip->open($views_path);
        if ($views_result === true) {
            $views_zip->extractTo($views_directory);
            $views_zip->close();
        }

        // unzip the assets zip file to the assets/frontend folder
        $assets_path = 'themes/' . $unzipped_file_name . '/assets/' . $zipped_file_name;
        $assets_zip = new ZipArchive;
        $assets_result = $assets_zip->open($assets_path);
        if ($assets_result === true) {
            $assets_zip->extractTo($assets_directory);
            $assets_zip->close();
        }

        unlink($theme_path);
        $this->crud_model->remove_files_and_folders('themes/' . $unzipped_file_name);
        $this->session->set_flashdata('flash_message', get_phrase('theme_imported_successfully'));
        redirect(site_url('admin/theme_settings'));
    }

    //ADDON MANAGER PORTION STARTS HERE
    public function addon($param1 = "", $param2 = "", $param3 = "")
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }
        // ADD NEW ADDON FORM
        if ($param1 == 'add') {

            $page_data['page_name'] = 'addon_add';
            $page_data['page_title'] = get_phrase('add_addon');
        }

        // INSTALLING AN ADDON
        if ($param1 == 'install') {
            $this->addon_model->install_addon();
        }

        // ACTIVATING AN ADDON
        if ($param1 == 'activate') {
            $update_message = $this->addon_model->addon_activate($param2);
            $this->session->set_flashdata('flash_message', get_phrase($update_message));
            redirect(site_url('admin/addon'), 'refresh');
        }

        // DEACTIVATING AN ADDON
        if ($param1 == 'deactivate') {
            $update_message = $this->addon_model->addon_deactivate($param2);
            $this->session->set_flashdata('flash_message', get_phrase($update_message));
            redirect(site_url('admin/addon'), 'refresh');
        }

        // REMOVING AN ADDON
        if ($param1 == 'delete') {
            $this->addon_model->addon_delete($param2);
            $this->session->set_flashdata('flash_message', get_phrase('addon_is_deleted_successfully'));
            redirect(site_url('admin/addon'), 'refresh');
        }

        // SHOWING LIST OF INSTALLED ADDONS
        if (empty($param1)) {
            $page_data['page_name'] = 'addons';
            $page_data['addons'] = $this->addon_model->addon_list()->result_array();
            $page_data['page_title'] = get_phrase('addon_manager');
        }
        $this->load->view('backend/index', $page_data);
    }

    //AVAILABLE_ADDONS
    public function available_addons()
    {
        if ($this->session->userdata('admin_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        $page_data['page_name'] = 'available_addon';
        $page_data['page_title'] = get_phrase('available_addon');
        $this->load->view('backend/index', $page_data);
    }

    // AJAX PORTION
    // this function is responsible for managing multiple choice question
    public function manage_multiple_choices_options()
    {
        $page_data['number_of_options'] = $this->input->post('number_of_options');
        $this->load->view('backend/admin/manage_multiple_choices_options', $page_data);
    }

    public function ajax_get_sub_category($category_id)
    {
        $page_data['sub_categories'] = $this->crud_model->get_sub_categories($category_id);

        return $this->load->view('backend/admin/ajax_get_sub_category', $page_data);
    }

    public function ajax_get_section($course_id)
    {
        $page_data['sections'] = $this->crud_model->get_section('course', $course_id)->result_array();
        return $this->load->view('backend/admin/ajax_get_section', $page_data);
    }

    public function ajax_get_video_details()
    {
        $video_details = $this->video_model->getVideoDetails($_POST['video_url']);
        echo $video_details['duration'];
    }
    public function ajax_sort_section()
    {
        $section_json = $this->input->post('itemJSON');
        $this->crud_model->sort_section($section_json);
    }
    public function ajax_sort_lesson()
    {
        $lesson_json = $this->input->post('itemJSON');
        $this->crud_model->sort_lesson($lesson_json);
    }
    public function ajax_sort_question()
    {
        $question_json = $this->input->post('itemJSON');
        $this->crud_model->sort_question($question_json);
    }
}

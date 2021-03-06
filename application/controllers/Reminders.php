<?php class Reminders extends CI_Controller { public function __construct() { parent::__construct(); 
    // $this->load->library('email');
    $this->load->model('Appointment_model');
  }

  function get_instructor($class_id){
    $this->db->where('id =', $class_id);
    $class = $this->db->get('classes')->result_array();
  
    $this->db->where('id =', $class[0]['course_id']);
    $course = $this->db->get('course')->result_array();
   
    $this->db->where('id =', $course[0]['instructor_id']);
    $instructor = $this->db->get('users')->result_array();
    return $instructor;
  }
//   public function switch_server_status(){
//     $ec2_server = new EC2_model();
//     $live_sessions = $this->Appointment_model->get_days_appointments();
//     $live_continued_sessions = $this->Appointment_model->get_continued_appointments();
//     if(!empty($live_sessions))
//     {
//         $ec2_server->switch_ec2_on_server();
//         echo 'server is going to on';
//     }
//     else{
//         echo 'server is going to off';
//         if (empty($live_continued_sessions)){
//             $ec2_server->switch_ec2_off_server(); 
//         }
//     }
   
//   }
  
  
  public function index()
  {
           
        // if(!$this->input->is_cli_request())
        // {
        //     echo "This script can only be accessed via the command line" . PHP_EOL;
        //     return;
        // }
        // $ec2_server = new EC2_model();
        $live_sessions = $this->Appointment_model->get_days_appointments();
        $live_continued_sessions = $this->Appointment_model->get_continued_appointments();
        $this->update_continued_appointments($live_continued_sessions);    
        if(!empty($live_sessions))
        {
           
              // starting my ec2 server
            // $ec2_server->switch_ec2_on_server();
            foreach($live_sessions as $live_session)
            {
                
                $class_students = $this->user_model->get_class_enrolled_students($live_session['class_id'])->result_array();
                $current_instructor=$this->get_instructor($live_session['class_id']);
                $data= $this->crud_model->create_live_session($current_instructor[0]['first_name'],$class_students, $live_session);
                log_message('info', 'class_std ' .  json_encode($class_students));
                log_message('info', 'class_Data ' . json_encode($data));
                if (empty($class_students)){
                    // admin email
                    $admin_email = 'sunnan.fazal95@hotmail.com';
                    $mail_subject = 'No students available for live session';
                    $mail_body = 'No Students Registered !';
                    $this->email_model->send_mail_for_live_session_confirmation($admin_email, $mail_body,$mail_subject);
                    break;
                }
                if ($data == -1){
                    // admin email
                    $admin_email = 'sunnan.fazal95@hotmail.com';
                    $mail_subject = 'Big Blue Button Create API not working check API link and shared secret key';
                    $mail_body = 'Something went wrong Contact Admin';
                    $this->email_model->send_mail_for_live_session_confirmation($admin_email, $mail_body,$mail_subject);
                    break;
                }
                for ($i = 0; $i < count($class_students); $i++){
                // foreach($class_students as $class_student ){
                       $current_time = strtotime("now"); 
                       if ($live_session['end_time'] <=  $current_time){
                           $this->Appointment_model->mark_end($live_session['id']);
                           $currest_live_session = $this->db->get_where('live_sessions', array('id' => $live_session['id']))->row_array();
                          $url = 'https://dynamiclogicltd.info/bigbluebutton/api/end?meetingID='.$currest_live_session['meeting_id'].'&password=333444&checksum='.$currest_live_session['checksum'];

                           $ch = curl_init();
                            curl_setopt ( $ch, CURLOPT_URL, $url );
                            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
                            curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
                            $http_respond = curl_exec($ch);
                            $http_respond = trim( strip_tags( $http_respond ) );
                            $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
                            curl_close( $ch );

                           continue;
                           

                       }
                        if($i == 0){
                            
                            $this->db->where('id =', $live_session['class_id']);
                            $class = $this->db->get('classes')->result_array();
                            
                            $this->db->where('id =', $class[0]['course_id']);
                            $course = $this->db->get('course')->result_array();
                           
                            $this->db->where('id =', $course[0]['instructor_id']);
                            $instructor = $this->db->get('users')->result_array();
                          
                	$mail_body = 'Dear Teacher, <br>Your Session is going to start at '.gmdate("Y-m-d\TH:i:s\Z",$live_session['start_time']).'and end at '.gmdate("Y-m-d\TH:i:s\Z",$live_session['end_time']).'<br>Please click to appear for session <br><a href='.$data['admin_url'].'>Session link</a>';
                            $mail_subject = 'Teacher Live Session';
                          
                             $this->email_model->send_mail_for_live_session_confirmation($instructor[0]['email'], $mail_subject,$mail_body);   
                        
                        }     
                                           
				$mail_body = 'Dear Student, <br>Your Session is going to start at '.gmdate("Y-m-d\TH:i:s\Z",$live_session['start_time']).'and end at '.gmdate("Y-m-d\TH:i:s\Z",$live_session['end_time']).'<br>Please click to appear for session <br><a href='.$data['student_urls'][$i].'>Session link</a>';
                        $mail_subject = 'Student Live Session';
                        $this->email_model->send_mail_for_live_session_confirmation($class_students[$i]['email'], $mail_subject , $mail_body);
                        $this->Appointment_model->mark_continue($live_session['id']);                                        
                      
                }
            }
          

        }
        else{
            echo 'no record of live session found';
            // stop ec2 server
            // if (empty($live_continued_sessions)){
            //     $ec2_server->switch_ec2_off_server(); 
            // }
        }
   }
   function destroy_appointments(){
        $live_continued_sessions = $this->Appointment_model->get_ended_appointments();
        foreach($live_continued_sessions as $live_session){
            $this->crud_model->destroy_live_session_by_id($live_session['live_session_id'], $live_session['checksum']);
        }

   }
   function update_continued_appointments($live_continued_sessions){
        $current_time = strtotime("now");
        foreach($live_continued_sessions as $live_session){
            if ($live_session['end_time'] <=  $current_time){
                $this->Appointment_model->mark_end($live_session['id']);   
            }
        }   
                 
   }
}

<?php
// defined('BASEPATH') OR exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

require APPPATH.'third_party/phpmailer/src/Exception.php';
require APPPATH.'third_party/phpmailer/src/PHPMailer.php';
require APPPATH.'third_party/phpmailer/src/SMTP.php';
class Employee extends CI_Controller {

	function __construct() {
		parent::__construct();
	    $this->load->helper('url');
	    $this->load->model('common_model');
	    $this->load->model('HR/employee_model', 'employee_model');
	}
  public function onboardingForm($doubleEncodedParams)
	{ 
     	 $encryptedData = urldecode(urldecode(urldecode($doubleEncodedParams)));
         $decryptedData = json_decode($this->encryption->decrypt($encryptedData), true);
         
         $tenantIdentifier =  $decryptedData['tenantIdentifier'];
      
         $empId =  $decryptedData['empId'];
         $location_id =  $decryptedData['location_id'];
         $this->session->set_userdata('tenantIdentifier',$tenantIdentifier);
         $this->session->set_userdata('mail_from',$decryptedData['mail_from']);
         $this->session->set_userdata('mail_protocol',$decryptedData['mail_protocol']);
         $this->session->set_userdata('decryptedData',$decryptedData);
     	 initializeTenantDbConfig($tenantIdentifier);
         
         
         $conditions = array('emp_id' =>$empId);
         $conditionsTwo = array('location' => $location_id, 'configureFor' => 'documents');
         $fieldsToFetch = ['data','metaData'];
        $employeeData = $this->common_model->fetchRecordsDynamically('HR_employee','',$conditions);
        $data['uploadedFiles'] = $this->common_model->fetchRecordsDynamically('HR_configuration', $fieldsToFetch,$conditionsTwo);

        // Onboarding form tab customization (configured from HR > Settings > Onboarding Form Customize)
        $onboardingTabsCfg = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], array('location' => $location_id, 'configureFor' => 'onboarding_tabs'));
        $tabsCfg = (isset($onboardingTabsCfg[0]['data']) && $onboardingTabsCfg[0]['data'] != '') ? json_decode($onboardingTabsCfg[0]['data'], true) : [];
        $data['onboardingTabsConfig'] = is_array($tabsCfg) ? $tabsCfg : [];

        // Face verification reuses the "Enable face verification for timesheet clockIn"
        // toggle (HR > Settings > General Settings). When it is off, the onboarding
        // form must not show the Face Verification step.
        $faceVerifyCfg = $this->common_model->fetchRecordsDynamically('HR_configuration', ['data'], array('location' => $location_id, 'configureFor' => 'feature_toggle'));
        $faceVerifyVal = (isset($faceVerifyCfg[0]['data']) && $faceVerifyCfg[0]['data'] != '') ? json_decode($faceVerifyCfg[0]['data'], true) : [];
        $data['faceVerificationEnabled'] = (isset($faceVerifyVal['value']) && $faceVerifyVal['value'] === '1');

        if(isset($employeeData[0]['onboarding_status']) && $employeeData[0]['onboarding_status'] == 4){
         echo "Onboarding has been already completed,Please check your email for the login credentials";  exit; 
        }
        
          $statusUpdate = array('onboarding_status' => 2); // Status 2 viewed email
          $this->employee_model->update_employee($statusUpdate, $empId);
                     
     	$data['employee'] = $employeeData[0]; 
     	$this->session->set_userdata('employeeEmail',$employeeData[0]['email']);
     	$data['headerTitle'] = 'Onboarding Form';
    //  	echo "<pre>"; print_r($data['employee']); exit;
    //  	$this->load->view('general/header',$data);
	   $this->load->view('HR/Employee/onboardingForm',$data);
	   // $this->load->view('general/footer');

    }
    
  
    
  public function submit_onboarding_process(){
      
   	       initializeTenantDbConfig($this->session->userdata('tenantIdentifier'));
            $empId = $this->input->post('emp_id');
            
        if (!empty($_FILES['userfile']['name'][0])) {

    $filename = $this->uploadAttachment($this->session->userdata('tenantIdentifier'));

    if ($filename === false) {
        echo json_encode([
            'status' => 'fail',
            'message' => 'File upload failed. Please try again.'
        ]);
        return;
    }

    $data_user['police_certificate'] = $filename;
}

           
                $posted_data = $this->input->post();
                foreach($posted_data as $key=> $value){
                 if( $key != 'email' && $key != 'emp_id' && $key != 'final_submit'){
                   ($value !='' ? $data_user[$key] = $value : '');   
                 }
                 }

		      //   if($this->input->post('check_tfn_type') == 'tfn_number'){
		      //       $data_user['tfn_type'] = '';
		      //   }
		      //   else{
		      //      $data_user['tfn_number'] = '';
		      //   }
		       
				// dont remove these, as these are dynamically created from upload code above
				if(isset($tax_declaration) && $tax_declaration !=''){
				   $data_user['tax_declaration'] = $tax_declaration;
				}
				if(isset($completed_super_annu) && $completed_super_annu !=''){
				   $data_user['completed_super_annu'] = $completed_super_annu;
				}
				if(isset($advice_of_tax_file) && $advice_of_tax_file !=''){
				   $data_user['advice_of_tax_file'] = $advice_of_tax_file;
				}
				if(isset($quality_assurance) && $quality_assurance !=''){
				   $data_user['quality_assurance'] = $quality_assurance;
				}
				if(isset($vaccination_certificate) && $vaccination_certificate !=''){
				   $data_user['vaccination_certificate'] = $vaccination_certificate;
				}
				
    
              
        //   echo "<pre>"; print_r($data_user); exit;
			$updateEmployee = $this->employee_model->update_employee($data_user,$empId);
		
			if($updateEmployee){
			
			$statusUpdate = array('onboarding_status' => 3); // Status 2 In Progress
          $this->employee_model->update_employee($statusUpdate, $empId);    
			   
			if($this->input->post('agree_terms_one') == '1' || $this->input->post('final_submit') == '1'){
			    
			     // Generate 4-digit PIN for employee clock in and clock out
                    $pin = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                    
                    
			     // Get employee details for email
			     $conditions = array('emp_id' => $empId);
     	         $fields = array('first_name','email','pin');
                 $empData = $this->common_model->fetchRecordsDynamically('HR_employee',$fields,$conditions);   
			     
			     // Try to send the PIN + welcome emails, but never let a mail/SMTP failure
			     // crash the request or block the employee from completing onboarding.
			     try {
			         // Set up SMTP
			         $this->setSmtpSettings($this->session->userdata('decryptedData'));   
			        
			         // Prepare PIN email data
			         $mailData['empName'] =  $empData[0]['first_name'];
			         $mailData['empEmail'] =  $empData[0]['email'];
			         $mailData['empPin'] = $pin; // Use the plain text PIN for email
			         $mailData['portalUrl'] = base_url().''.$this->session->userdata('tenantIdentifier');
			         
			         // Load PIN email template
			         $pinEmailContent = $this->load->view('HR/Email/employeePin',$mailData,TRUE); 
			         
			         // Send PIN email
			         $mail_from = $this->session->userdata('mail_from');
			         $mail_protocol = $this->session->userdata('mail_protocol');
			         $this->sendEmail($empData[0]['email'],'Your Employee PIN - Bizadmin',$pinEmailContent,$mail_from,'','Bizadmin HR Team',$mail_protocol);
			         
			         // Also send the original welcome email
			         $welcomeMailData['empName'] =  $empData[0]['first_name'];
			         $welcomeMailData['empEmail'] =  $empData[0]['email'];
			         $welcomeMailData['portalUrl'] = base_url().''.$this->session->userdata('tenantIdentifier');
			         // Branding: use the actual organisation name (falls back to Bizadmin).
			         $decryptedForOrg = $this->session->userdata('decryptedData');
			         $welcomeMailData['orgName'] = (!empty($decryptedForOrg['orz_name'])) ? $decryptedForOrg['orz_name'] : 'Bizadmin';
			         // Password reset link must carry the tenant so the session/DB is set
			         // even if the user never visited https://bizadmin.com.au/{tenant_id}.
			         $welcomeMailData['resetUrl'] = base_url('auth/forgot_password').'?tenant='.urlencode($this->session->userdata('tenantIdentifier'));
			         $welcomeEmailContent = $this->load->view('HR/Email/employeeCred',$welcomeMailData,TRUE); 
			         $this->sendEmail($empData[0]['email'],'BizAdmin - Welcome to HR management',$welcomeEmailContent,$mail_from,'','Bizadmin HR Team',$mail_protocol);
			     } catch (\Exception $e) {
		         // Log and continue - onboarding should still complete even if the mail server fails.
		         // Also record WHICH SMTP credentials were used (password masked) so the
		         // failure can be traced to a specific mail config / tenant.
		         $smtpCfg = $this->session->userdata('decryptedData');
		         $smtpPass = isset($smtpCfg['smtp_pass']) ? $smtpCfg['smtp_pass'] : '';
		         log_message(
		             'error',
		             'Onboarding email failed for emp_id '.$empId.': '.$e->getMessage()
		             .' | smtp_host='.(isset($smtpCfg['smtp_host']) ? $smtpCfg['smtp_host'] : '(none)')
		             .' | smtp_port='.(isset($smtpCfg['smtp_port']) ? $smtpCfg['smtp_port'] : '(none)')
		             .' | smtp_username='.(isset($smtpCfg['smtp_username']) ? $smtpCfg['smtp_username'] : '(none)')
		             .' | smtp_pass_len='.strlen($smtpPass)
		             .' | smtp_pass_set='.($smtpPass !== '' ? 'yes' : 'no')
		             .' | mail_from='.$this->session->userdata('mail_from')
		             .' | mail_protocol='.$this->session->userdata('mail_protocol')
		             .' | phpmailer_error='.(isset($this->phpmailer) ? $this->phpmailer->ErrorInfo : '')
		         );
			     }
			     
			     // Mark onboarding as completed regardless of email outcome so the
			     // employee is not stuck if SMTP is misconfigured. PIN is saved so it
			     // can be resent/retrieved by an admin later.
			     $statusUpdate['date_modified'] = date("Y-m-d");
			     $statusUpdate['status'] = 1;
			     $statusUpdate['onboarding_status'] = 4; 
			     $statusUpdate['pin'] = $pin;
			     $this->employee_model->update_employee($statusUpdate, $empId);
        	          
			    }
		    $response['status'] = 'success';
			$response['message'] = 'success';
			echo json_encode($response);
			    
			}else{
			$response['status'] = 'fail';
			$response['message'] = 'Error updating record.Please contact admin';
			echo json_encode($response);
			}
			
	
	}   
	
  function updateLeaveStatus($doubleEncodedParams,$statusName,$message){
      
	    $encryptedData = urldecode(urldecode(urldecode($doubleEncodedParams)));
         $decryptedData = json_decode($this->encryption->decrypt($encryptedData), true);
         
         $tenantIdentifier =  $decryptedData['tenantIdentifier'];
         $leaveId =  $decryptedData['id'];
         $location_id =  $decryptedData['location_id'];
         $empId =  $decryptedData['emp_id'];
         $this->session->set_userdata('tenantIdentifier',$tenantIdentifier);
         $this->session->set_userdata('mail_from',$decryptedData['mail_from']);
         $this->session->set_userdata('mail_protocol',$decryptedData['mail_protocol']);
         $this->session->set_userdata('decryptedData',$decryptedData);
     	 initializeTenantDbConfig($tenantIdentifier);

        $dataToUpdate['leave_status'] =2;
        $this->common_model->commonRecordUpdate('HR_leave_management','id',$leaveId,$dataToUpdate);
        
        $this->setSmtpSettings($decryptedData);  
       
         $conditions = array('emp_id' =>$empId);
         $fieldsToFetch = ['email','first_name'];
         $empData = $this->common_model->fetchRecordsDynamically('HR_employee',$fieldsToFetch,$conditions);
       
        
        $mailData['empName'] =  $empData[0]['first_name'];
		$mailData['empEmail'] =  $empData[0]['email'];
		$mailData['statusname'] = $statusName;
		
        $mailContent = $this->load->view('HR/Email/empLeaveStatusUpdate',$mailData,TRUE); 
        $mail_from = $decryptedData['mail_from'];
        $mail_protocol = $decryptedData['mail_protocol'];
        $res = $this->sendEmail($empData[0]['email'],'BizAdmin - Leave Status Update',$mailContent,$mail_from,'','',$mail_protocol);
        if($res){
          echo $message;
          exit;   
        }  
	}
  public function approveLeave($doubleEncodedParams)
	{ 
	$message = "Thank you, Leave request is approved, same has been informed to employee";
     $this->updateLeaveStatus($doubleEncodedParams,'approved',$message);
    }
    
    function rejectLeave($doubleEncodedParams){
      $message = "Thank you, Leave request is rejected, same has been informed to employee";
      $this->updateLeaveStatus($doubleEncodedParams,'rejected',$message);  
    }
  
 public function uploadAttachment($orgName)
{
    // ---------- Safety ----------
    if (
        empty($_FILES['userfile']) ||
        !isset($_FILES['userfile']['name']) ||
        !is_array($_FILES['userfile']['name'])
    ) {
        return false;
    }

    $uploaded_files = [];

    // ---------- Upload path ----------
    $uploadPath = FCPATH . 'uploaded_files/' .
                  $this->session->userdata('tenantIdentifier') .
                  '/HR/OnboardingFiles/';

    // ---------- Ensure directory exists ----------
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    // ---------- Base config ----------
    $config = [
        'upload_path'   => $uploadPath,
        'allowed_types' => 'jpg|jpeg|png|gif|pdf',
        'max_size'      => 10240, // 10MB
        'overwrite'     => false
    ];

    $this->load->library('upload');

    $countFiles = count($_FILES['userfile']['name']);

    for ($i = 0; $i < $countFiles; $i++) {

        if (empty($_FILES['userfile']['name'][$i])) {
            continue;
        }

        // ---------- Validate tmp file (mobile fix) ----------
        if (
            empty($_FILES['userfile']['tmp_name'][$i]) ||
            !is_uploaded_file($_FILES['userfile']['tmp_name'][$i])
        ) {
            continue;
        }

        // ---------- Build $_FILES for CI ----------
        $_FILES['file'] = [
            'name'     => $_FILES['userfile']['name'][$i],
            'type'     => $_FILES['userfile']['type'][$i],
            'tmp_name' => $_FILES['userfile']['tmp_name'][$i],
            'error'    => $_FILES['userfile']['error'][$i],
            'size'     => $_FILES['userfile']['size'][$i]
        ];

        // ---------- Unique filename (MUST for mobile) ----------
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $config['file_name'] = 'police_' . time() . '_' . mt_rand(1000, 9999) . '.' . strtolower($ext);

        // ---------- Reinitialize upload ----------
        $this->upload->initialize($config);

        if ($this->upload->do_upload('file')) {
            $uploadData = $this->upload->data();
            $uploaded_files[] = $uploadData['file_name'];
        } else {
            // Log error instead of silent fail
            log_message('error', 'Upload error: ' . $this->upload->display_errors('', ''));
        }
    }

    // ---------- Final validation ----------
    if (empty($uploaded_files)) {
        return false;
    }

    return serialize($uploaded_files);
}


 public function setSmtpSettings($decryptedData){
        $this->phpmailer = new PHPMailer(true);
       
        $this->phpmailer->isSMTP();
        $this->phpmailer->SMTPDebug = 0; // Set to 2 for debugging
        $this->phpmailer->Host = $decryptedData['smtp_host'];
        $this->phpmailer->Port = $decryptedData['smtp_port'];
        $this->phpmailer->SMTPAuth = true;
        $this->phpmailer->Username = $decryptedData['smtp_username'];
        $this->phpmailer->Password = $decryptedData['smtp_pass'];
        $this->phpmailer->SMTPSecure = 'tls';
        $this->phpmailer->CharSet = 'UTF-8';  
        
    } 
    
    public function sendEmail($to, $subject, $message,$from='',$cc='',$fromName='Bizadmin',$mail_protocol) {
    
        if ($mail_protocol == 'smtp') {
            // Receipent
            if (is_array($to)) {
               
             foreach ($to as $recipient) {
                $this->phpmailer->addAddress($recipient);
             }
              } else {
            
            $mailTo = explode(",",$to);
            if (is_array($mailTo)) {
             foreach ($mailTo as $recipient) {
                $this->phpmailer->addAddress($recipient);
             }
            }

           }
           
           //CC
           
           if($cc !=''){
             if (is_array($cc)) {
             foreach ($cc as $CCrecipient) {
                $this->phpmailer->addCC($CCrecipient);
             }
              } else {
            $this->phpmailer->addCC($cc);
           }
             
           }
           
            // $this->phpmailer->setFrom($from);
            $this->phpmailer->setFrom($from, $fromName);
            $this->phpmailer->isHTML(true); 
            $this->phpmailer->Subject = $subject;
            $this->phpmailer->Body = $message;

            if ($this->phpmailer->send()) {
                // echo "success mail sent"; exit;
                return true; // Email sent successfully
            } else {
                // Log which SMTP credentials were used (password masked) plus the error.
                log_message(
                    'error',
                    'SMTP send failed | to='.(is_array($to) ? implode(',', $to) : $to)
                    .' | host='.$this->phpmailer->Host
                    .' | port='.$this->phpmailer->Port
                    .' | username='.$this->phpmailer->Username
                    .' | pass_len='.strlen((string)$this->phpmailer->Password)
                    .' | secure='.$this->phpmailer->SMTPSecure
                    .' | error='.$this->phpmailer->ErrorInfo
                );
                return false; // Email sending failed
            }
        } else {
            // Fallback to CodeIgniter's Email library for mail protocol
            $this->load->library('email');

            // $this->email->from('your-email@example.com', 'Your Name');
            if (is_array($to)) {
            foreach ($to as $recipient) {
                $this->email->to($recipient);
            }
             } else {
            $this->email->to($to);
           }
            $this->email->subject($subject);
            $this->email->message($message);

            if ($this->email->send()) {
                return true; // Email sent successfully
            } else {
                return false; // Email sending failed
            }
        }
    }	
    
     public function uploadFaceImage() {
          initializeTenantDbConfig($this->session->userdata('tenantIdentifier'));
        $input = json_decode(file_get_contents("php://input"), true);
        $emp_id = $input['emp_id'];
        $imageData = $input['image'];

        if (!$emp_id || !$imageData) {
            echo json_encode(['status' => 'error', 'message' => 'Missing data']);
            return;
        }

        // Decode image
        $img = str_replace('data:image/jpeg;base64,', '', $imageData);
        $img = str_replace(' ', '+', $img);
        $imgData = base64_decode($img);

        // Generate filename
        $filename = 'employee_faces/' . $emp_id . '.jpg';

        // Upload to S3
        try {
            require_once FCPATH . 'vendor/autoload.php'; 
            $s3 = new S3Client([
                'region' => 'ap-southeast-2',
                'version' => 'latest',
                'credentials' => [
                    'key' => 'AKIAXZ3XPGYZLXYPII56',
                    'secret' => '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',
                ]
            ]);

            $result = $s3->putObject([
                'Bucket' => 'bizadmin-hr-employee-images',
                'Key'    => $filename,
                'Body'   => $imgData,
                'ContentType' => 'image/jpeg'
                
            ]);

            // Store image URL in database
            $data_user['face_image_url'] = $result['ObjectURL'];
            $updateEmployee = $this->employee_model->update_employee($data_user,$emp_id);
            

            echo json_encode(['status' => 'success', 'url' => $result['ObjectURL']]);
        } catch (AwsException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}

?>
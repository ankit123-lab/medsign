<?php
/**
 * 
 * This controller use for Clinic related activity
 */
class Clinic extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("Clinic_model",'clinic');
        $this->load->model("User_model");
    }

    /**
     * Description :- This function is used to add the clinic details
     * 
     * 
     */
    public function add_clinic_post() {
        try {
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : '';
            $clinic_name = !empty($this->post_data['clinic_name']) ? $this->post_data['clinic_name'] : '';
            $clinic_description = !empty($this->post_data['clinic_description']) ? $this->post_data['clinic_description'] : '';
            $clinic_website = !empty($this->post_data['clinic_website']) ? $this->post_data['clinic_website'] : '';
            $clinic_gstin = !empty($this->post_data['clinic_gstin']) ? $this->post_data['clinic_gstin'] : '';
            $clinic_number = !empty($this->post_data['clinic_number']) ? $this->post_data['clinic_number'] : '';
            $clinic_address = !empty($this->post_data['clinic_address']) ? $this->post_data['clinic_address'] : '';
            $clinic_address1 = !empty($this->post_data['clinic_address1']) ? $this->post_data['clinic_address1'] : '';
            $clinic_address_latlong = !empty($this->post_data['clinic_address_latlong']) ? $this->post_data['clinic_address_latlong'] : '';
            $clinic_email = !empty($this->post_data['clinic_email']) ? $this->post_data['clinic_email'] : '';
            $clinic_locality = !empty($this->post_data['clinic_locality']) ? $this->post_data['clinic_locality'] : '';
            $clinic_city = !empty($this->post_data['clinic_city']) ? $this->post_data['clinic_city'] : '';
            $clinic_zipcode = !empty($this->post_data['clinic_zipcode']) ? $this->post_data['clinic_zipcode'] : '';
            $clinic_state = !empty($this->post_data['clinic_state']) ? $this->post_data['clinic_state'] : '';
            $clinic_country = !empty($this->post_data['clinic_country']) ? $this->post_data['clinic_country'] : '';
            $clinic_services = !empty($this->post_data['clinic_services']) ? $this->post_data['clinic_services'] : '';
            $clinic_consultation_duration = !empty($this->post_data['doctor_clinic_mapping_duration']) ? $this->post_data['doctor_clinic_mapping_duration'] : '';
            $clinic_consultation_charges = !empty($this->post_data['clinic_consultation_charges']) ? $this->post_data['clinic_consultation_charges'] : '';
             $clinic_tele_consultation_charges = !empty($this->post_data['clinic_tele_consultation_charges']) ? $this->post_data['clinic_tele_consultation_charges'] : '';
            $clinic_session_time_1 = !empty($this->post_data['clinic_session_time_1']) ? $this->post_data['clinic_session_time_1'] : '';
            $clinic_session_time_2 = !empty($this->post_data['clinic_session_time_2']) ? $this->post_data['clinic_session_time_2'] : '';
            $specialization_id = !empty($this->post_data['specialization_id']) ? $this->post_data['specialization_id'] : '';
            $clinic_availability_type = !empty($this->post_data['clinic_availability_type']) ? $this->post_data['clinic_availability_type'] : '';
            $session_time_1 = explode(',', $clinic_session_time_1);
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 11,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (
                    !empty($doctor_id) &&
                    !empty($clinic_name) &&
                    !empty($clinic_city) &&
                    !empty($clinic_zipcode) &&
                    !empty($clinic_state) &&
                    !empty($clinic_country) &&
                    !empty($clinic_services) &&
                    !empty($clinic_consultation_duration) &&
                    !empty($clinic_session_time_1) &&
                    !empty($session_time_1[0]) &&
                    !empty($session_time_1[1])
            ) {
                
                if (!empty($clinic_email) && validate_email($clinic_email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_email");
                    $this->send_response();
                }

                if (!empty($clinic_zipcode) && validate_pincode($clinic_zipcode)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_pincode");
                    $this->send_response();
                }

                if (!empty($clinic_number) && validate_clinic_number($clinic_number)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_phone_number");
                    $this->send_response();
                }


                $latlong = explode(',', $clinic_address_latlong);

                if (!empty($session_time_1[0]) && validate_time($session_time_1[0])) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_time");
                    $this->send_response();
                }

                if (!empty($session_time_1[1]) && validate_time($session_time_1[1])) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_time");
                    $this->send_response();
                }

                if (strtotime($session_time_1[0]) > strtotime($session_time_1[1])) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_time");
                    $this->send_response();
                }

                if (!empty($clinic_email)) {
                    $check_email_sql = "SELECT 
                                        clinic_email
                                 FROM 
                                    " . TBL_CLINICS . " 
                                 WHERE 
                                      clinic_email = '" . $clinic_email . "' 
                                 AND         
                                      clinic_status != 9
                                ";
                    $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);
                    if (!empty($check_email)) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("user_register_email_exist");
                        $this->send_response();
                    }
                }

                if (!empty($clinic_session_time_2)) {
                    $session_time_2 = explode(',', $clinic_session_time_2);
                    if (!empty($session_time_2[0]) && validate_time($session_time_2[0])) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("invalid_time");
                        $this->send_response();
                    }

                    if (!empty($session_time_2[1]) && validate_time($session_time_2[1])) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("invalid_time");
                        $this->send_response();
                    }

                    if (!empty($session_time_2[0]) &&
                            !empty($session_time_2[1]) &&
                            strtotime($session_time_2[0]) > strtotime($session_time_2[1])
                    ) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("invalid_time");
                        $this->send_response();
                    }
                }

                $this->db->trans_start();
                $sql_where = ['doctor_clinic_mapping_user_id' => $doctor_id, 'doctor_clinic_mapping_role_id' => 1, 'doctor_clinic_mapping_status' => 1];
                $clinic_rows = $this->Common_model->get_all_rows(TBL_DOCTOR_CLINIC_MAPPING, 'doctor_clinic_mapping_id', $sql_where);
                $total_clinic = count($clinic_rows);
                $insert_array = array(
                    'clinic_name' => $clinic_name,
                    'clinic_description' => $clinic_description,
                    'clinic_contact_number' => $clinic_number,
                    'clinic_email' => $clinic_email,
                    'clinic_website' => $clinic_website,
                    'clinic_services' => trim($clinic_services,','),
                    'clinic_specialization_id' => $specialization_id,
                    'clinic_gstin' => $clinic_gstin,
                    'clinic_availability_type' => $clinic_availability_type,
                    'clinic_created_by' => $doctor_id,
                    "clinic_session1_start_time" => $session_time_1[0],
                    "clinic_session1_end_time" => $session_time_1[1],
                    "clinic_session2_start_time" => isset($session_time_2[0]) ? $session_time_2[0] : '',
                    "clinic_session2_end_time" => isset($session_time_2[1]) ? $session_time_2[1] : '',
                    'clinic_created_at' => $this->utc_time_formated,
                    'clinic_color_code' => CLINIC_COLOR_CODE[$total_clinic]
                );

                $clinic_id = $this->clinic->add_clinic($insert_array);

                if ($clinic_id > 0) {
                    $clinic_address_array = array(
                        'address_user_id' => $clinic_id,
                        'address_type' => '2',
                        'address_name' => $clinic_address,
                        'address_name_one' => $clinic_address1,
                        'address_city_id' => $clinic_city,
                        'address_state_id' => $clinic_state,
                        'address_country_id' => $clinic_country,
                        'address_pincode' => $clinic_zipcode,
                        'address_latitude' => $latlong[0],
                        'address_longitude' => $latlong[1],
                        'address_locality' => $clinic_locality,
                        'address_created_at' => $this->utc_time_formated
                    );
					
                    $clinic_address_id = $this->clinic->add_clinic_address($clinic_address_array);
                    
					$upload_path = UPLOAD_REL_PATH . "/" . CLINIC_FOLDER . "/" . $clinic_id;
                    $upload_folder = CLINIC_FOLDER . "/" . $clinic_id;
                    $insert_other_batch_img = array();
                    if (isset($_FILES) && isset($_FILES['clinic_logo_image']) && isset($_FILES['clinic_logo_image']['name']) && !empty($_FILES['clinic_logo_image']['name']) && $_FILES['clinic_logo_image']['error'] == 0) {
                        $clinic_logo_image = do_upload_multiple($upload_path, array('clinic_logo_image' => $_FILES['clinic_logo_image']), $upload_folder);
                    }
					
                    $update_clinic_array = array();
                    if (isset($clinic_logo_image) && !empty($clinic_logo_image['clinic_logo_image'])) {
                        $update_clinic_array['clinic_image'] = $clinic_logo_image['clinic_logo_image'];
                        $update_clinic_array['clinic_filepath'] = get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_logo_image['clinic_logo_image']);
						$this->clinic->update(TBL_CLINICS, $update_clinic_array, array("clinic_id" => $clinic_id));
					}
					
                    if (isset($_FILES) && isset($_FILES['clinic_address_image']) && isset($_FILES['clinic_address_image']['name']) && !empty($_FILES['clinic_address_image']['name']) && $_FILES['clinic_address_image']['error'] == 0) {
                        $clinic_address_image = do_upload_multiple($upload_path, array('clinic_address_image' => $_FILES['clinic_address_image']), $upload_folder);
                        if (!empty($clinic_address_image['clinic_address_image'])) {
                            $insert_other_batch_img[] = array(
                                "clinic_photo_clinic_id" => $clinic_id,
                                "clinic_photo_type" => 4,
                                "clinic_photo_image" => $clinic_address_image['clinic_address_image'],
                                "clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_address_image['clinic_address_image']),
                                "clinic_photo_created_at" => $this->utc_time_formated,
                            );
                        }
                    }

                    if (isset($_FILES) && isset($_FILES['clinic_out_side_area_image']) && isset($_FILES['clinic_out_side_area_image']['name']) && !empty($_FILES['clinic_out_side_area_image']['name']) && $_FILES['clinic_out_side_area_image']['error'] == 0) {
                        $clinic_out_side_area_image = do_upload_multiple($upload_path, array('clinic_out_side_area_image' => $_FILES['clinic_out_side_area_image']), $upload_folder);
                        if (!empty($clinic_out_side_area_image['clinic_out_side_area_image'])) {
                            $insert_other_batch_img[] = array(
                                "clinic_photo_clinic_id" => $clinic_id,
                                "clinic_photo_type" => 1,
                                "clinic_photo_image" => $clinic_out_side_area_image['clinic_out_side_area_image'],
                                "clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_out_side_area_image['clinic_out_side_area_image']),
                                "clinic_photo_created_at" => $this->utc_time_formated,
                            );
                        }
                    }

                    if (isset($_FILES) && isset($_FILES['clinic_waiting_area_image']) && isset($_FILES['clinic_waiting_area_image']['name']) && !empty($_FILES['clinic_waiting_area_image']['name']) && $_FILES['clinic_waiting_area_image']['error'] == 0) {
                        $clinic_waiting_area_image = do_upload_multiple($upload_path, array('clinic_waiting_area_image' => $_FILES['clinic_waiting_area_image']), $upload_folder);
                        if (!empty($clinic_waiting_area_image['clinic_waiting_area_image'])) {
                            $insert_other_batch_img[] = array(
                                "clinic_photo_clinic_id" => $clinic_id,
                                "clinic_photo_type" => 2,
                                "clinic_photo_image" => $clinic_waiting_area_image['clinic_waiting_area_image'],
                                "clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_waiting_area_image['clinic_waiting_area_image']),
                                "clinic_photo_created_at" => $this->utc_time_formated,
                            );
                        }
                    }

                    if (isset($_FILES) && isset($_FILES['clinic_reception_area_image']) && isset($_FILES['clinic_reception_area_image']['name']) && !empty($_FILES['clinic_reception_area_image']['name']) && $_FILES['clinic_reception_area_image']['error'] == 0) {
                        $clinic_reception_area_image = do_upload_multiple($upload_path, array('clinic_reception_area_image' => $_FILES['clinic_reception_area_image']), $upload_folder);
                        if (!empty($clinic_reception_area_image['clinic_reception_area_image'])) {
                            $insert_other_batch_img[] = array(
                                "clinic_photo_clinic_id" => $clinic_id,
                                "clinic_photo_type" => 3,
                                "clinic_photo_image" => $clinic_reception_area_image['clinic_reception_area_image'],
                                "clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_reception_area_image['clinic_reception_area_image']),
                                "clinic_photo_created_at" => $this->utc_time_formated,
                            );
                        }
                    }
					if(!empty($insert_other_batch_img))
						$this->clinic->insert_multiple(TBL_CLINIC_IMAGES, $insert_other_batch_img);
					
                    /* insert into mapping table */
                    $get_clinic_mapping_data = $this->Common_model->get_single_row(TBL_DOCTOR_CLINIC_MAPPING, 'doctor_clinic_mapping_user_id', ['doctor_clinic_mapping_user_id' => $doctor_id, 'doctor_clinic_mapping_is_primary' => 1, 'doctor_clinic_mapping_status' => 1]);
                    $mapping_insert_array = array(
                        "doctor_clinic_mapping_user_id" => $doctor_id,
                        "doctor_clinic_mapping_clinic_id" => $clinic_id,
                        "doctor_clinic_mapping_fees" => $clinic_consultation_charges,
                        "doctor_clinic_mapping_tele_fees" => $clinic_tele_consultation_charges,
                        "doctor_clinic_doctor_session_1_start_time" => $session_time_1[0],
                        "doctor_clinic_doctor_session_1_end_time" => $session_time_1[1],
                        "doctor_clinic_doctor_session_2_start_time" => isset($session_time_2[0]) ? $session_time_2[0] : NULL,
                        "doctor_clinic_doctor_session_2_end_time" => isset($session_time_2[1]) ? $session_time_2[1] : NULL,
                        "doctor_clinic_mapping_role_id" => 1,
                        "doctor_clinic_mapping_created_at" => $this->utc_time_formated,
                        "doctor_clinic_mapping_status" => 1,
                        "doctor_clinic_mapping_duration" => $clinic_consultation_duration,
                    );
                    if(empty($get_clinic_mapping_data))
                        $mapping_insert_array['doctor_clinic_mapping_is_primary'] = 1;
                    $this->clinic->insert(TBL_DOCTOR_CLINIC_MAPPING, $mapping_insert_array);

                    //store doctor time in availability by default doctor is available for the doctor visit
                    //for all the days
                    $set_avialability_array = array();
                    for ($i = 1; $i <= 7; $i++) {
                        $set_avialability_array[] = array(
                            'doctor_availability_clinic_id' => $clinic_id,
                            'doctor_availability_user_id' => $doctor_id,
                            'doctor_availability_week_day' => $i,
                            'doctor_availability_session_1_start_time' => $session_time_1[0],
                            'doctor_availability_session_1_end_time' => $session_time_1[1],
                            'doctor_availability_session_2_start_time' => isset($session_time_2[0]) ? $session_time_2[0] : NULL,
                            'doctor_availability_session_2_end_time' => isset($session_time_2[1]) ? $session_time_2[1] : NULL,
                            'doctor_availability_created_at' => $this->utc_time_formated,
                            'doctor_availability_appointment_type' => 1 // Doctor Visit
                        );
                        $set_avialability_array[] = array(
                            'doctor_availability_clinic_id' => $clinic_id,
                            'doctor_availability_user_id' => $doctor_id,
                            'doctor_availability_week_day' => $i,
                            'doctor_availability_session_1_start_time' => $session_time_1[0],
                            'doctor_availability_session_1_end_time' => $session_time_1[1],
                            'doctor_availability_session_2_start_time' => isset($session_time_2[0]) ? $session_time_2[0] : NULL,
                            'doctor_availability_session_2_end_time' => isset($session_time_2[1]) ? $session_time_2[1] : NULL,
                            'doctor_availability_created_at' => $this->utc_time_formated,
                            'doctor_availability_appointment_type' => 5 // Tele consultation
                        );

                        $set_clinic_avialability_array[] = array(
                            'clinic_availability_clinic_id' => $clinic_id,
                            'clinic_availability_week_day' => $i,
                            'clinic_availability_session_1_start_time' => $session_time_1[0],
                            'clinic_availability_session_1_end_time' => $session_time_1[1],
                            'clinic_availability_session_2_start_time' => isset($session_time_2[0]) ? $session_time_2[0] : NULL,
                            'clinic_availability_session_2_end_time' => isset($session_time_2[1]) ? $session_time_2[1] : NULL,
                            'clinic_availability_created_at' => $this->utc_time_formated,
                        );
                    }

                    $this->Common_model->insert_multiple(TBL_DOCTOR_AVAILABILITY, $set_avialability_array);
                    $this->Common_model->insert_multiple(TBL_CLINIC_AVAILABILITY, $set_clinic_avialability_array);

                    $clinic_data = $this->clinic->get_clinic_detail($clinic_id);

                    //insert the payment mode
                    $get_payment_mode_data = $this->Common_model->get_single_row(TBL_PAYMENT_MODE, 'payment_mode_id', ['payment_mode_doctor_id' => $doctor_id]);
                    if(empty($get_payment_mode_data)) {
                        $payment_mode_array = array(
                            'payment_mode_name' => 'Cash',
                            'payment_mode_doctor_id' => $doctor_id,
                            'payment_mode_vendor_fee' => '0',
                            'payment_mode_payment_type_id' => 1,
                            'payment_mode_created_at' => $this->utc_time_formated
                        );
                        $this->Common_model->insert(TBL_PAYMENT_MODE, $payment_mode_array);
                    }
                    $get_tax_data = $this->Common_model->get_single_row(TBL_TAXES, 'tax_id', ['tax_doctor_id' => $doctor_id]);
                    if(!empty($get_tax_data)) {
                        $inserted_tax_id = $get_tax_data['tax_id'];
                    } else {
                        $tax_array = array(
                            'tax_name' => 'Cash',
                            'tax_value' => '0',
                            'tax_doctor_id' => $doctor_id,
                            'tax_created_at' => $this->utc_time_formated
                        );
                        $inserted_tax_id = $this->Common_model->insert(TBL_TAXES, $tax_array);
                    }
                    //insert the fee structure
                    $fee_structure_array = array(
                        'pricing_catalog_name' => 'Consultation Charges',
                        'pricing_catalog_cost' => $clinic_consultation_charges,
                        'pricing_catalog_tax_id' => $inserted_tax_id,
                        'pricing_catalog_doctor_id' => $doctor_id,
                        'pricing_catalog_clinic_id' => $clinic_id,
                        'pricing_catalog_created_at' => $this->utc_time_formated
                    );
                    $this->Common_model->insert(TBL_PRICING_CATALOG, $fee_structure_array);
					
					/* [START] By default all the sharing options are enabled for doctor whenn he created his clinic entry */
					$setting_array = [
						['id'=>1,'name'=>'Vital','status'=>'1'],
						['id'=>2,'name'=>'Clinical Notes','status'=>'1'],
                        ['id'=>8,'name'=>'Only Diagnosis','status'=>'2'],
						['id'=>3,'name'=>'Rx','status'=>'1'],
                        ['id'=>9,'name'=>'Generic','status'=>'2'],
						['id'=>4,'name'=>'Investigations','status'=>'1'],
						['id'=>5,'name'=>'Procedures','status'=>'1'],
						['id'=>6,'name'=>'Invoice','status'=>'1'],
						['id'=>7,'name'=>'Report','status'=>'1']
					];
					$insert_setting_array[] = array(
						'setting_user_id' => $doctor_id,
						'setting_clinic_id' => $clinic_id,
						'setting_data' => json_encode($setting_array),
						'setting_type' => 1,
						'setting_data_type' => 1,
						'setting_created_at' => $this->utc_time_formated
					);
					/* [START] By default all the Alert options are enabled for doctor when he created his clinic entry */
					$setting_array = [
						["id"=>1,  "name"=>"app_sms_status", 				"status"=>"2"],
						["id"=>2,  "name"=>"app_email_status", 				"status"=>"1"],
						["id"=>3,  "name"=>"can_sms_status", 				"status"=>"2"],
						["id"=>4,  "name"=>"can_email_status", 				"status"=>"1"],
						["id"=>5,  "name"=>"instant_status", 				"status"=>"1"],
						["id"=>6,  "name"=>"call_btn_status", 				"status"=>"1"],
						["id"=>7,  "name"=>"reschedule_sms_status", 		"status"=>"2"],
						["id"=>8,  "name"=>"reschedule_email_status", 		"status"=>"1"],
						["id"=>9,  "name"=>"app_notification_status", 		"status"=>"1"],
						["id"=>10, "name"=>"cancel_notification_status",	"status"=>"1"],
						["id"=>11, "name"=>"reschedule_notification_status","status"=>"1"],
						["id"=>12, "name"=>"payment_sms_status", 			"status"=>"1"],
						["id"=>13, "name"=>"payment_email_status", 			"status"=>"1"],
						["id"=>14, "name"=>"payment_notification_status", 	"status"=>"1"],
                        ["id"=>15, "name"=>"patient_app_sms_status",        "status"=>"1"],
                        ["id"=>16, "name"=>"patient_can_sms_status",        "status"=>"1"],
                        ["id"=>17, "name"=>"patient_reschedule_sms_status", "status"=>"1"],
                        ["id"=>18, "name"=>"patient_app_email_status",      "status"=>"1"],
                        ["id"=>19, "name"=>"patient_can_email_status",      "status"=>"1"],
                        ["id"=>20, "name"=>"patient_reschedule_email_status","status"=>"1"],
                        ["id"=>21, "name"=>"patient_app_whatsapp_status","status"=>"1"],
                        ["id"=>22, "name"=>"patient_can_whatsapp_status","status"=>"1"],
                        ["id"=>23, "name"=>"patient_reschedule_whatsapp_status","status"=>"1"],
                        ["id"=>24, "name"=>"google_sync_status","status"=>"1"],
                        ["id"=>25, "name"=>"patient_book_notification_status","status"=>"1"],
                        ["id"=>26, "name"=>"patient_cancel_notification_status","status"=>"1"],
                        ["id"=>27, "name"=>"patient_reschedule_notification_status","status"=>"1"]
					];
					$insert_setting_array[] = array(
						'setting_user_id' => $doctor_id,
						'setting_clinic_id' => $clinic_id,
						'setting_data' => json_encode($setting_array),
						'setting_type' => 3,
						'setting_data_type' => 1,
						'setting_created_at' => $this->utc_time_formated
					);
					$this->Common_model->insert_multiple(TBL_SETTING, $insert_setting_array);
                    $registration_share_link = [
                        'registration_share_doctor_id' => $doctor_id,
                        'registration_share_clinic_id' => $clinic_id,
                        'registration_share_created_at' => $this->utc_time_formated,
                        'registration_share_created_by' => $doctor_id,
                        'registration_share_user_type' => 2
                    ];
                    $this->Common_model->insert('me_registration_share_link', $registration_share_link);
					/* [END] */

                    if ($this->db->trans_status() !== FALSE) {
                        $this->db->trans_commit();
                        $this->my_response['status'] = true;
                        $this->my_response['message'] = lang('clinic_added_successfully');
                        $this->my_response['data'] = $clinic_data;
                    } else {
                        $this->db->trans_rollback();
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('failure');
                    }
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("clinic_unable_to_add_clinic"),
                        "data" => ''
                    );
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("mendatory_filled_missing"),
                    "data" => ''
                );
            }
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to edit the clinic details
     * 
     * 
     */
    public function edit_clinic_post() {
        try {
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->post_data['clinic_id'] : '';
            $clinic_name = !empty($this->post_data['clinic_name']) ? $this->post_data['clinic_name'] : '';
            $clinic_description = !empty($this->post_data['clinic_description']) ? $this->post_data['clinic_description'] : '';
            $clinic_website = !empty($this->post_data['clinic_website']) ? $this->post_data['clinic_website'] : '';
            $clinic_gstin = !empty($this->post_data['clinic_gstin']) ? $this->post_data['clinic_gstin'] : '';
            $clinic_number = !empty($this->post_data['clinic_number']) ? $this->post_data['clinic_number'] : '';
            $clinic_address_id = !empty($this->post_data['clinic_address_id']) ? $this->post_data['clinic_address_id'] : '';
            $clinic_address = !empty($this->post_data['clinic_address']) ? $this->post_data['clinic_address'] : '';
            $clinic_address1 = !empty($this->post_data['clinic_address1']) ? $this->post_data['clinic_address1'] : '';
            $clinic_address_latlong = !empty($this->post_data['clinic_address_latlong']) ? $this->post_data['clinic_address_latlong'] : '';
            $clinic_email = !empty($this->post_data['clinic_email']) ? $this->post_data['clinic_email'] : '';
            $clinic_locality = !empty($this->post_data['clinic_locality']) ? $this->post_data['clinic_locality'] : '';
            $clinic_city = !empty($this->post_data['clinic_city']) ? $this->post_data['clinic_city'] : '';
            $clinic_zipcode = !empty($this->post_data['clinic_zipcode']) ? $this->post_data['clinic_zipcode'] : '';
            $clinic_state = !empty($this->post_data['clinic_state']) ? $this->post_data['clinic_state'] : '';
            $clinic_country = !empty($this->post_data['clinic_country']) ? $this->post_data['clinic_country'] : '';
            $clinic_services = !empty($this->post_data['clinic_services']) ? $this->post_data['clinic_services'] : '';
            $clinic_consultation_duration = !empty($this->post_data['doctor_clinic_mapping_duration']) ? $this->post_data['doctor_clinic_mapping_duration'] : '';
            $clinic_consultation_charges = !empty($this->post_data['clinic_consultation_charges']) ? $this->post_data['clinic_consultation_charges'] : '';
            $clinic_tele_consultation_charges = !empty($this->post_data['clinic_tele_consultation_charges']) ? $this->post_data['clinic_tele_consultation_charges'] : '';
            $clinic_session_time_1 = !empty($this->post_data['clinic_session_time_1']) ? $this->post_data['clinic_session_time_1'] : '';
            $clinic_session_time_2 = !empty($this->post_data['clinic_session_time_2']) ? $this->post_data['clinic_session_time_2'] : '';
            $specialization_id = !empty($this->post_data['specialization_id']) ? $this->post_data['specialization_id'] : '';
            $clinic_availability_type = !empty($this->post_data['clinic_availability_type']) ? $this->post_data['clinic_availability_type'] : '';

            $clinic_out_side_area_image_id = !empty($this->post_data['clinic_out_side_area_image_id']) ? $this->post_data['clinic_out_side_area_image_id'] : '';
            $clinic_address_image_id = !empty($this->post_data['clinic_address_image_id']) ? $this->post_data['clinic_address_image_id'] : '';
            $clinic_waiting_area_image_id = !empty($this->post_data['clinic_waiting_area_image_id']) ? $this->post_data['clinic_waiting_area_image_id'] : '';
            $clinic_reception_area_image_id = !empty($this->post_data['clinic_reception_area_image_id']) ? $this->post_data['clinic_reception_area_image_id'] : '';

            $session_time_1 = explode(',', $clinic_session_time_1);

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 11,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (
                    !empty($clinic_id) &&
                    !empty($clinic_name) &&
                    !empty($clinic_city) &&
                    !empty($clinic_zipcode) &&
                    !empty($clinic_state) &&
                    !empty($clinic_country) &&
                    !empty($clinic_services) &&
                    !empty($clinic_consultation_duration) &&
                    !empty($clinic_session_time_1) &&
                    !empty($session_time_1[0]) &&
                    !empty($session_time_1[1])
            ) {

                if (!empty($clinic_email) && validate_email($clinic_email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_email");
                    $this->send_response();
                }

                if (!empty($clinic_zipcode) && validate_pincode($clinic_zipcode)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_pincode");
                    $this->send_response();
                }

                if (!empty($clinic_number) && validate_clinic_number($clinic_number)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_phone_number");
                    $this->send_response();
                }
				
                if (!empty($clinic_email)) {
                    $check_email_sql = "SELECT 
                                        clinic_email
                                 FROM 
                                    " . TBL_CLINICS . " 
                                 WHERE 
                                      clinic_email = '" . $clinic_email . "' 
                                 AND         
                                      clinic_status != 9
                                 AND
                                      clinic_id != '" . $clinic_id . "'
                                ";
                    $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);
                    if (!empty($check_email)) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("user_register_email_exist");
                        $this->send_response();
                    }
                }
                $latlong = explode(',', $clinic_address_latlong);
                if (!empty($session_time_1[0]) && validate_time($session_time_1[0])) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_time");
                    $this->send_response();
                }

                if (!empty($session_time_1[1]) && validate_time($session_time_1[1])) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_time");
                    $this->send_response();
                }

                if (strtotime($session_time_1[0]) > strtotime($session_time_1[1])) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("invalid_time");
                    $this->send_response();
                }

                if (!empty($clinic_session_time_2)) {
                    $session_time_2 = explode(',', $clinic_session_time_2);

                    if (!empty($session_time_2[0]) && validate_time($session_time_2[0])) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("invalid_time");
                        $this->send_response();
                    }

                    if (!empty($session_time_2[1]) && validate_time($session_time_2[1])) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("invalid_time");
                        $this->send_response();
                    }

                    if (!empty($session_time_2[0]) &&
                            !empty($session_time_2[1]) &&
                            strtotime($session_time_2[0]) > strtotime($session_time_2[1])
                    ) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("invalid_time");
                        $this->send_response();
                    }
                }
				
                $this->db->trans_start();
                $update_array = array(
                    'clinic_name' => $clinic_name,
                    'clinic_description' => $clinic_description,
                    'clinic_email' => $clinic_email,
                    'clinic_website' => $clinic_website,
                    'clinic_services' => trim($clinic_services,','),
                    'clinic_specialization_id' => $specialization_id,
                    'clinic_gstin' => $clinic_gstin,
                    'clinic_availability_type' => $clinic_availability_type,
                    'clinic_modified_at' => $this->utc_time_formated,
                    "clinic_session1_start_time" => $session_time_1[0],
                    "clinic_session1_end_time" => $session_time_1[1],
                    "clinic_session2_start_time" => isset($session_time_2[0]) ? $session_time_2[0] : NULL,
                    "clinic_session2_end_time" => isset($session_time_2[1]) ? $session_time_2[1] : NULL,
                    "clinic_contact_number" => $clinic_number
                );
                $update_where = array(
                    'clinic_id' => $clinic_id
                );
                $update_clinic_id = $this->clinic->update_clinic($update_array, $update_where);
                if ($update_clinic_id > 0) {
                    $phone_number_updated = 2;
                    $email_updated = 2;
                    //get old email id and phonenumber
                    $getting_old_details = $this->Common_model->get_single_row(TBL_CLINICS, 'clinic_name,clinic_email,clinic_contact_number', array('clinic_id' => $clinic_id));

                    //if existing phone number and old number are not same then send the otp for verification
                    if (!empty($clinic_number) && ($getting_old_details['clinic_contact_number'] != $clinic_number)) {
                        $otp = getUniqueToken(6, 'numeric');
                        $message = sprintf(OTP_MESSAGE, $otp);
                        $send_otp = array(
                            'phone_number' => $clinic_number,
                            'message' => $message,
                            'whatsapp_sms_body' => sprintf(lang('wa_template_30_otp'), $otp)
                        );
                        $send_otp['doctor_id'] = $this->user_id;
                        $send_otp['is_sms_count'] = true;
                        $send_otp['is_check_sms_credit'] = $GLOBALS['ENV_VARS']['CHECK_SMS_CREDIT']['OTP_MESSAGE'];
                        $sening_sms = send_communication($send_otp);

                        //$sening_sms = TRUE;
                        if ($sening_sms) {

                            //check entry is in auth table or not
                            $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, 'auth_id', array(
                                'auth_is_clinic' => 1,
                                'auth_type' => 2,
                                'auth_user_id' => $clinic_id
                            ));

                            if (!empty($get_auth_details['auth_id'])) {
                                $auth_update_data = array(
                                    'auth_code' => $otp,
                                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                    'auth_phone_number' => $clinic_number
                                );
                                $auth_update_where = array(
                                    'auth_user_id' => $clinic_id,
                                    'auth_type' => 2,
                                    'auth_is_clinic' => 1
                                );
                                $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                            } else {
                                $insert_clinic_auth = array(
                                    'auth_user_id' => $clinic_id,
                                    'auth_type' => 2,
                                    'auth_code' => $otp,
                                    'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                    'auth_created_at' => $this->utc_time_formated,
                                    'auth_is_clinic' => 1,
                                    'auth_phone_number' => $clinic_number
                                );
                                $this->Common_model->insert(TBL_USER_AUTH, $insert_clinic_auth);
                            }
                            $phone_number_updated = 1;
                        }
                    }

                    if (!empty($clinic_email) && (strtolower($getting_old_details['clinic_email']) != strtolower($clinic_email))) {
                        $reset_token = str_rand_access_token(20);
                        //check entry is in auth table or not
                        $get_auth_details = $this->Common_model->get_single_row(TBL_USER_AUTH, 'auth_id', array(
                            'auth_is_clinic' => 1,
                            'auth_type' => 1,
                            'auth_user_id' => $clinic_id
                        ));

                        if (!empty($get_auth_details['auth_id'])) {
                            $auth_update_data = array(
                                'auth_code' => $reset_token,
                                'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                'auth_phone_number' => $clinic_email
                            );
                            $auth_update_where = array(
                                'auth_user_id' => $clinic_id,
                                'auth_type' => 1,
                                'auth_is_clinic' => 1
                            );
                            $is_update = $this->User_model->update_auth_details($auth_update_data, $auth_update_where);
                        } else {

                            $insert_clinic_auth = array(
                                'auth_user_id' => $clinic_id,
                                'auth_type' => 1,
                                'auth_code' => $reset_token,
                                'auth_otp_expiry_time' => date('Y-m-d H:i:s', strtotime(OTP_EXPIRE_TOKEN_TIME, strtotime($this->utc_time_formated))),
                                'auth_created_at' => $this->utc_time_formated,
                                'auth_is_clinic' => 1,
                                'auth_phone_number' => $clinic_email
                            );
                            $is_update = $this->Common_model->insert(TBL_USER_AUTH, $insert_clinic_auth);
                        }

                        if ($is_update > 0) {

                            $email_updated = 1;
                            $verify_link = DOMAIN_URL . "verifyaccount/" . $reset_token;

                            $view_data = array();
                            $view_data['verify_link'] = $verify_link;

                            if (!empty($getting_old_details['clinic_name'])) {
                                $user_name = $getting_old_details['clinic_name'];
                            } else {
                                $user_name = "User";
                            }

                            
                            $this->load->model('Emailsetting_model');
                            $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(12);
                            $parse_arr = array(
                                '{UserName}' => $user_name,
                                '{VerificationLink}' => '<a href="' . $verify_link . '">' . $verify_link . '</a>',
                                '{WebUrl}' => '<a href="' . DOMAIN_URL . '">' . DOMAIN_URL . '</a>',
                                '{AppName}' => APP_NAME,
                                '{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
                                '{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
                                '{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
                                '{CopyRightsYear}' => date('Y')
                            );
                            $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                            $subject = $email_template_data['email_template_subject'];
                            
                            //this is use for get view and store data in variable
                            //this function help you to send mail to single ot multiple users
                            $this->send_email(array($clinic_email => $clinic_email), $subject, $message);
                        }
                    }

                    if (!empty($clinic_address_id)) {
                        $update_clinic_address_array = array(
                            'address_name' => $clinic_address,
                            'address_name_one' => $clinic_address1,
                            'address_city_id' => $clinic_city,
                            'address_state_id' => $clinic_state,
                            'address_country_id' => $clinic_country,
                            'address_pincode' => $clinic_zipcode,
                            'address_latitude' => $latlong[0],
                            'address_longitude' => $latlong[1],
                            'address_locality' => $clinic_locality,
                            'address_created_at' => $this->utc_time_formated
                        );

                        $update_clinic_address_where = array(
                            'address_id' => $clinic_address_id
                        );

                        $clinic_address_id = $this->clinic->update_clinic_address($update_clinic_address_array, $update_clinic_address_where);

                        if ($clinic_address_id == 0) {
                            $this->my_response = array(
                                "status" => false,
                                "message" => lang("clinic_unable_to_edit_clinic_address"),
                                "data" => array()
                            );
                            $this->send_response();
                        }
                    }
                    $upload_path = UPLOAD_REL_PATH . "/" . CLINIC_FOLDER . "/" . $clinic_id;
                    $upload_folder = CLINIC_FOLDER . "/" . $clinic_id;
                    
					if (isset($_FILES) && isset($_FILES['clinic_logo_image']) && isset($_FILES['clinic_logo_image']['name']) && !empty($_FILES['clinic_logo_image']['name']) && $_FILES['clinic_logo_image']['error'] == 0) {
                        $profile_clinic_name = do_upload_multiple($upload_path, array('clinic_logo_image' => $_FILES['clinic_logo_image']), $upload_folder);
                        $update_array = array(
                            "clinic_image" => $profile_clinic_name['clinic_logo_image'],
                            "clinic_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $profile_clinic_name['clinic_logo_image'])
                        );
                        $this->clinic->update(TBL_CLINICS, $update_array, array("clinic_id" => $clinic_id));
                    }
					
					$insert_other_batch_img = [];
                    if (isset($_FILES) && isset($_FILES['clinic_address_image']) && isset($_FILES['clinic_address_image']['name']) && !empty($_FILES['clinic_address_image']['name']) && $_FILES['clinic_address_image']['error'] == 0) {
						$clinic_address_image = do_upload_multiple($upload_path, array('clinic_address_image' => $_FILES['clinic_address_image']), $upload_folder);
						if(isset($clinic_address_image_id) && !empty($clinic_address_image_id)){
							$update_image_array = array(
								'clinic_photo_image' 	=> $clinic_address_image['clinic_address_image'],
								'clinic_photo_filepath' => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_address_image['clinic_address_image']),
								'clinic_photo_modified_at' => $this->utc_time_formated
							);
							$update_image_where = array( 'clinic_photo_id' => $clinic_address_image_id);
							$this->clinic->update_clinic_image($update_image_array, $update_image_where);
						}else{
								if (!empty($clinic_address_image['clinic_address_image'])) {
									$insert_other_batch_img[] = array(
										"clinic_photo_clinic_id" => $clinic_id,
										"clinic_photo_type" => 4,
										"clinic_photo_image" => $clinic_address_image['clinic_address_image'],
										"clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_address_image['clinic_address_image']),
										"clinic_photo_created_at" => $this->utc_time_formated,
									);
								}
						}
                    }

                    if (isset($_FILES) && isset($_FILES['clinic_out_side_area_image']) && !empty($_FILES['clinic_out_side_area_image']) && $_FILES['clinic_out_side_area_image']['name'] != '') {
                        $clinic_out_side_area_image = do_upload_multiple($upload_path, array('clinic_out_side_area_image' => $_FILES['clinic_out_side_area_image']), $upload_folder);
							if(isset($clinic_out_side_area_image_id) && !empty($clinic_out_side_area_image_id)){
								$update_image_array = array(
									'clinic_photo_image' => $clinic_out_side_area_image['clinic_out_side_area_image'],
									'clinic_photo_filepath' => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_out_side_area_image['clinic_out_side_area_image']),
									'clinic_photo_modified_at' => $this->utc_time_formated
								);
								$update_image_where = array('clinic_photo_id' => $clinic_out_side_area_image_id);
								$this->clinic->update_clinic_image($update_image_array, $update_image_where);
							}else{
								if (!empty($clinic_out_side_area_image['clinic_out_side_area_image'])) {
									$insert_other_batch_img[] = array(
										"clinic_photo_clinic_id" => $clinic_id,
										"clinic_photo_type" => 1,
										"clinic_photo_image" => $clinic_out_side_area_image['clinic_out_side_area_image'],
										"clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_out_side_area_image['clinic_out_side_area_image']),
										"clinic_photo_created_at" => $this->utc_time_formated,
									);
								}
							}
                    }

                    if (isset($_FILES) && isset($_FILES['clinic_waiting_area_image']) && !empty($_FILES['clinic_waiting_area_image']) && $_FILES['clinic_waiting_area_image']['name'] != '') {
                        $clinic_waiting_area_image = do_upload_multiple($upload_path, array('clinic_waiting_area_image' => $_FILES['clinic_waiting_area_image']), $upload_folder);
						
						if(isset($clinic_waiting_area_image_id) && !empty($clinic_waiting_area_image_id)){
							$update_image_array = array(
								'clinic_photo_image' => $clinic_waiting_area_image['clinic_waiting_area_image'],
								'clinic_photo_filepath' => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_waiting_area_image['clinic_waiting_area_image']),
								'clinic_photo_modified_at' => $this->utc_time_formated
							);
							$update_image_where = array('clinic_photo_id' => $clinic_waiting_area_image_id);
							$this->clinic->update_clinic_image($update_image_array, $update_image_where);
						}else{
							if (!empty($clinic_waiting_area_image['clinic_waiting_area_image'])) {
								$insert_other_batch_img[] = array(
									"clinic_photo_clinic_id" => $clinic_id,
									"clinic_photo_type" => 2,
									"clinic_photo_image" => $clinic_waiting_area_image['clinic_waiting_area_image'],
									"clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_waiting_area_image['clinic_waiting_area_image']),
									"clinic_photo_created_at" => $this->utc_time_formated,
								);
							}
						}
                    }

                    if (isset($_FILES) && isset($_FILES['clinic_reception_area_image']) && !empty($_FILES['clinic_reception_area_image']) && $_FILES['clinic_reception_area_image']['name'] != '') {
                        $clinic_reception_area_image = do_upload_multiple($upload_path, array('clinic_reception_area_image' => $_FILES['clinic_reception_area_image']), $upload_folder);
                       
						if(isset($clinic_reception_area_image_id) && !empty($clinic_reception_area_image_id)){
							$update_image_array = array(
								'clinic_photo_image' => $clinic_reception_area_image['clinic_reception_area_image'],
								'clinic_photo_filepath' => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_reception_area_image['clinic_reception_area_image']),
								'clinic_photo_modified_at' => $this->utc_time_formated
							);
							$update_image_where = array('clinic_photo_id' => $clinic_reception_area_image_id);
							$this->clinic->update_clinic_image($update_image_array, $update_image_where);
						}else{
								if (!empty($clinic_reception_area_image['clinic_reception_area_image'])) {
									$insert_other_batch_img[] = array(
										"clinic_photo_clinic_id" => $clinic_id,
										"clinic_photo_type" => 3,
										"clinic_photo_image" => $clinic_reception_area_image['clinic_reception_area_image'],
										"clinic_photo_filepath" => get_file_json_detail(CLINIC_FOLDER . "/" . $clinic_id . "/" . $clinic_reception_area_image['clinic_reception_area_image']),
										"clinic_photo_created_at" => $this->utc_time_formated,
									);
								}
						}	
                    }

					if(!empty($insert_other_batch_img)){
						$this->clinic->insert_multiple(TBL_CLINIC_IMAGES, $insert_other_batch_img);
					}
					
                    $mapping_update_array = array(
                        "doctor_clinic_mapping_fees" => $clinic_consultation_charges,
                        "doctor_clinic_mapping_tele_fees" => $clinic_tele_consultation_charges,
                        "doctor_clinic_doctor_session_1_start_time" => $session_time_1[0],
                        "doctor_clinic_doctor_session_1_end_time" => $session_time_1[1],
                        "doctor_clinic_doctor_session_2_start_time" => isset($session_time_2[0]) ? $session_time_2[0] : NULL,
                        "doctor_clinic_doctor_session_2_end_time" => isset($session_time_2[1]) ? $session_time_2[1] : NULL,
                        "doctor_clinic_mapping_modified_at" => $this->utc_time_formated,
                        "doctor_clinic_mapping_status" => 1,
                        "doctor_clinic_mapping_duration" => $clinic_consultation_duration,
                    );
                    $mapping_update_where_array = array(
                        'doctor_clinic_mapping_clinic_id' => $clinic_id
                    );
                    $this->clinic->update(TBL_DOCTOR_CLINIC_MAPPING, $mapping_update_array, $mapping_update_where_array);

                    $clinic_data = $this->clinic->get_clinic_detail($clinic_id);
                    //$clinic_data['phone_number_updated'] = $phone_number_updated;
                    $clinic_data['phone_number_updated'] = 2;


                    if ($this->db->trans_status() !== FALSE) {
                        $this->db->trans_commit();
                        $this->my_response['status'] = true;
                        $this->my_response['data'] = $clinic_data;

                        if ($email_updated == 1 && $phone_number_updated == 1) {
                            $this->my_response['message'] = lang("clinic_edit_successfully_phone_email");
                        } else if ($email_updated == 1) {
                            $this->my_response['message'] = lang("clinic_edit_successfully_email");
                        } else if ($phone_number_updated == 1) {
                            $this->my_response['message'] = lang("clinic_edit_successfully_phone");
                        } else {
                            $this->my_response['message'] = lang("clinic_edit_successfully");
                        }
                    } else {
                        $this->db->trans_rollback();
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('failure');
                    }
                } else {
                    $this->my_response = array(
                        "status" => false,
                        "message" => lang("clinic_unable_to_edit_clinic"),
                        "data" => array()
                    );
                }
            } else {
                $this->my_response = array(
                    "status" => false,
                    "message" => lang("mendatory_filled_missing"),
                    "data" => array()
                );
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Desscription :- This function is used to check the doctor belongs to how many clinics
     *                  and also the whole information of the clinic
     * 
     
     * 
     */
    public function get_doctors_clinics_post() {
        try {
            //get clinics data
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : '';

            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $clinic_data = $this->clinic->get_doctor_clinics($doctor_id);
            $doctor_global_setting = $this->clinic->get_doctor_global_setting($doctor_id);
            $is_add_clinic = true;
            if(count($doctor_global_setting) > 0) {
                $global_setting = array_column($doctor_global_setting, 'setting_value','setting_name');
                if(!empty($global_setting['no_of_clinic'])) {
                    $no_of_clinic = $global_setting['no_of_clinic'];
                    if(count($clinic_data) >= $no_of_clinic) {
                        $is_add_clinic = false;
                    }
                }
            }
            $this->my_response['status'] = true;
            $this->my_response['clinic_data'] = $clinic_data;
            $this->my_response['is_add_clinic'] = $is_add_clinic;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Desscription :- This function is used to get the clinic details post
     * 
     
     * 
     */
    public function get_clinic_detail_post() {
        try {
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $doctor_id = !empty($this->post_data['user_id']) ? trim($this->Common_model->escape_data($this->post_data['user_id'])) : "";

            if (empty($clinic_id)) {
                $this->bad_request();
                exit;
            }
            $clinic_data = $this->clinic->get_clinic_whole_detail($clinic_id, $doctor_id);
            if (!empty($clinic_data)) {
                $clinic_data['clinic_filepath'] = get_image_thumb($clinic_data['clinic_filepath']);
                //get image data
                $image_data = $this->clinic->get_clinic_images($clinic_id);
                foreach ($image_data as $key => $value) {
                    $image_data[$key]['clinic_photo_filepath'] = get_image_thumb($value['clinic_photo_filepath']);
                }
                $clinic_data['images'] = $image_data;

                $this->my_response['status'] = true;
                $this->my_response['data'] = $clinic_data;
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('common_detail_not_found');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to add the staff details
     * 
     
     */
    public function add_staff_post() {

        try {

            $first_name = !empty($this->post_data['first_name']) ? trim($this->Common_model->escape_data($this->post_data['first_name'])) : "";
            $last_name = !empty($this->post_data['last_name']) ? trim($this->Common_model->escape_data($this->post_data['last_name'])) : "";
            $email = !empty($this->post_data['email']) ? trim($this->Common_model->escape_data($this->post_data['email'])) : "";
            $phone_number = !empty($this->post_data['phone_number']) ? trim($this->Common_model->escape_data($this->post_data['phone_number'])) : "";
            $staff_type = !empty($this->post_data['staff_type']) ? trim($this->Common_model->escape_data($this->post_data['staff_type'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $added_doctor_id = !empty($this->post_data['added_doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['added_doctor_id'])) : "";
            $gender = !empty($this->post_data['gender']) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : "";
            $address = !empty($this->post_data['address']) ? trim($this->Common_model->escape_data($this->post_data['address'])) : "";
            $address1 = !empty($this->post_data['address1']) ? trim($this->Common_model->escape_data($this->post_data['address1'])) : "";
            $city_id = !empty($this->post_data['city_id']) ? trim($this->Common_model->escape_data($this->post_data['city_id'])) : "";
            $state_id = !empty($this->post_data['state_id']) ? trim($this->Common_model->escape_data($this->post_data['state_id'])) : "";
            $country_id = !empty($this->post_data['country_id']) ? trim($this->Common_model->escape_data($this->post_data['country_id'])) : "";
            $pincode = !empty($this->post_data['pincode']) ? trim($this->Common_model->escape_data($this->post_data['pincode'])) : "";
            $latitude = !empty($this->post_data['latitude']) ? trim($this->Common_model->escape_data($this->post_data['latitude'])) : "";
            $longitude = !empty($this->post_data['longitude']) ? trim($this->Common_model->escape_data($this->post_data['longitude'])) : "";
            $longitude = !empty($this->post_data['longitude']) ? trim($this->Common_model->escape_data($this->post_data['longitude'])) : "";
            $locality = !empty($this->post_data['locality']) ? trim($this->Common_model->escape_data($this->post_data['locality'])) : "";
            $tfa = !empty($this->post_data['tfa']) ? trim($this->Common_model->escape_data($this->post_data['tfa'])) : "";
            $edu_qualification = !empty($this->post_data['edu_qualification']) ? json_decode($this->post_data['edu_qualification'], true) : array();
            $registration_details = !empty($this->post_data['registration_details']) ? json_decode($this->post_data['registration_details'], true) : array();
            $assign_clinic_id = !empty($this->post_data['assign_clinic_id']) ? $this->post_data['assign_clinic_id'] : '';
            if (empty($assign_clinic_id)) {
                $this->bad_request();
            }
            $assign_clinic_id = explode(',', $assign_clinic_id);
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 12,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (in_array($staff_type, array(2, 3, 4)) &&
                    (
                    empty($first_name) ||
                    empty($last_name) ||
                    empty($phone_number)
                    )
            ) {
                $this->bad_request();
            }

            if ($staff_type == 1 && empty($added_doctor_id)) {
                $this->bad_request();
            }

            if (empty($staff_type) ||
                    empty($doctor_id) ||
                    empty($clinic_id)
            ) {
                $this->bad_request();
            }

            if (!in_array($staff_type, array(1, 2, 3, 4))) {
                $this->bad_request();
            }

            if (!empty($email) && validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if (!empty($phone_number) && validate_phone_number($phone_number)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }

            if (
                    (!empty($first_name) && validate_characters($first_name)) ||
                    (!empty($last_name) && validate_characters($last_name))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if (!empty($added_doctor_id) && $staff_type == 1) {
                $doctor_detail_already_added = $this->User_model->get_details_by_id($added_doctor_id);
                if (empty($doctor_detail_already_added)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("common_detail_not_found");
                    $this->send_response();
                }
                $insert_id = $added_doctor_id;
            } else {

                $check_number = $this->User_model->check_user_number_exists($phone_number, 2);

                if (!empty($check_number) && count($check_number) > 0) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_phone_number_exist");
                    $this->send_response();
                }
                if($email){
                    $check_email = $this->User_model->get_details_by_email($email, 2);
                    if (!empty($check_email) && count($check_email) > 0) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang("user_register_email_exist");
                        $this->send_response();
                    }
                }

                $password = $this->Common_model->generate_random_string(4, 1, 1, 1);

                $insert_user = array(
                    'user_email' => $email,
                    'user_first_name' => ucwords(strtolower($first_name)),
                    'user_last_name' => ucwords(strtolower($last_name)),
                    'user_phone_number' => $phone_number,
                    'user_gender' => $gender,
                    'user_created_at' => $this->utc_time_formated,
                    'user_unique_id' => $this->Common_model->escape_data(str_rand_access_token(8)),
                    'user_password' => password_hash(sha1($password), PASSWORD_BCRYPT),
                    'user_type' => 2
                );

                $insert_id = $this->User_model->register_user($insert_user);
            }

            if ($insert_id > 0) {
                //update the addrress
                $update_address_data = array(
                    'address_name'          => $address,
                    'address_name_one'      => $address1,
                    'address_city_id'       => $city_id,
                    'address_state_id'      => $state_id,
                    'address_country_id'    => $country_id,
                    'address_pincode'       => $pincode,
                    'address_latitude'      => $latitude,
                    'address_longitude'     => $longitude,
                    'address_locality'      => $locality,
                );
                
                $address_is_update = $this->User_model->update_address($insert_id, $update_address_data);
                $setting_array[] = array(
                    'id' => 1,
                    'name' => '2 Factor authentication',
                    'status' => $tfa
                );
                $this->clinic->update2FASetting($insert_id,$setting_array);
                $insert_role_array = [];
                foreach ($assign_clinic_id as $key => $value) {
                    $insert_role_array[] = array(
                        'doctor_clinic_mapping_role_id' => $staff_type,
                        'doctor_clinic_mapping_clinic_id' => $value,
                        'doctor_clinic_mapping_user_id' => $insert_id,
                        'doctor_clinic_mapping_doctor_id' => $doctor_id,
                        'doctor_clinic_mapping_created_at' => $this->utc_time_formated
                    );
                }
                if(!empty($insert_role_array)){
                    $this->Common_model->insert_multiple(TBL_DOCTOR_CLINIC_MAPPING, $insert_role_array);
                    $insert_role = 1;
                }
                if($staff_type == 2) {
                    $role_where = array(
                        'user_role_id' => $staff_type,
                        'user_role_status' => 1
                    );
                    $get_role_data = $this->Common_model->get_single_row(TBL_USER_ROLE, 'user_role_data', $role_where);
                    $insert_user_role_access = array(
                        'user_role_access_user_id' => $insert_id,
                        'user_role_access_role_id' => $staff_type,
                        'user_role_access_data' => $get_role_data['user_role_data'],
                        'user_role_access_created_at' => $this->utc_time_formated,
                        'user_role_access_created_by' => $this->user_id
                    );
                    $this->Common_model->insert(TBL_USER_ROLE_ACCESS, $insert_user_role_access);
                    if(is_array($edu_qualification) && count($edu_qualification) > 0) {
                        $insert_array = array();
                        if (isset($_FILES) && !empty($_FILES) && isset($_FILES['education_images'])) {
                            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_EDUCATIONS_FOLDER . "/" . $insert_id;
                            $upload_folder = DOCTOR_EDUCATIONS_FOLDER . "/" . $insert_id;
                            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $_FILES['education_images']), $upload_folder);
                        }
                        foreach ($edu_qualification as $key => $edu) {
                            $insert_array[] = array(
                                "doctor_qualification_user_id" => $insert_id,
                                "doctor_qualification_qualification_id" => $edu['doctor_qualification_qualification_id'],
                                "doctor_qualification_degree" => $edu['doctor_qualification_degree'],
                                "doctor_qualification_college" => $edu['doctor_qualification_college'],
                                "doctor_qualification_completion_year" => $edu['doctor_qualification_completion_year'],
                                "doctor_qualification_image" => (isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? $profile_image_name[$key] : '',
                                "doctor_qualification_image_full_path" => (isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? (get_file_json_detail(DOCTOR_EDUCATIONS_FOLDER."/".$insert_id."/".$profile_image_name[$key])) : '',
                                "doctor_qualification_created_at" => $this->utc_time_formated,
                                "doctor_qualification_status" => 1,
                            );
                        }
                        $this->db->insert_batch(TBL_DOCTOR_EDUCATIONS, $insert_array);
                    }

                    if(is_array($registration_details) && count($registration_details) > 0) {
                        $insert_array = array();
                        if (isset($_FILES) && isset($_FILES['registration_images'])) {
                            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_REGISTRATIONS_FOLDER . "/" . $insert_id;
                            $upload_folder = DOCTOR_REGISTRATIONS_FOLDER . "/" . $insert_id;
                            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $_FILES['registration_images']), $upload_folder);
                        }
                        foreach ($registration_details as $key => $reg) {
                            $insert_array[] = array(
                                "doctor_registration_user_id" => $insert_id,
                                "doctor_registration_council_id" => $reg['doctor_registration_council_id'],
                                "doctor_council_registration_number" => $reg['doctor_council_registration_number'],
                                "doctor_registration_year" => $reg['doctor_registration_year'],
                                "doctor_registration_image_filepath" => (isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? (get_file_json_detail(DOCTOR_REGISTRATIONS_FOLDER . "/" . $insert_id . "/" . $profile_image_name[$key])) : '',
                                "doctor_registration_image" =>(isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? $profile_image_name[$key] : '',
                                "doctor_registration_created_at" => $this->utc_time_formated,
                                "doctor_registration_status" => 1,
                            );
                        }
                        if (!empty($insert_array)) {
                            $this->db->insert_batch(TBL_DOCTOR_REGISTRATIONS, $insert_array);
                        }
                    }
                }
                if ($insert_role > 0) {
                    
                    $created_by_user_detail = $this->User_model->get_details_by_id($doctor_id);

                    $view_data = array();

                    if (!empty($added_doctor_id)) {
                        $doctor_detail_already_added = $this->User_model->get_details_by_id($added_doctor_id);

                        if (!empty($doctor_detail_already_added['user_first_name'])) {
                            $user_name = $doctor_detail_already_added['user_first_name'] . " " . $doctor_detail_already_added['user_last_name'];
                        } else {
                            $user_name = "";
                        }

                        $email = $doctor_detail_already_added['user_email'];
                    } else {
                        $user_name = ucwords(strtolower($first_name)) . " " . ucwords(strtolower($last_name));
                        $view_data['email'] = $email;
                        $view_data['password'] = $password;
                    }
                    
                    $doctor_detail = $this->User_model->get_details_by_id($doctor_id);
                    if($doctor_detail){
                        if (!empty($doctor_detail['user_first_name'])) {
                            $user_name = DOCTOR . ' ' .$doctor_detail['user_first_name'] . " " . $doctor_detail['user_last_name'];
                        } else {
                            $user_name = "";
                        }

                        $email = $doctor_detail['user_email'];
                        $view_data['first_name'] = $user_name;
                        $created_by = $created_by_user_detail['user_first_name'] . " " . $created_by_user_detail['user_last_name'];
                        $inserted_staff_detail = $this->User_model->get_details_by_id($insert_id);
                        
                        $this->load->model('Emailsetting_model');
                        if($staff_type == 2) { 
                            $template_id = 37; //Assistant email template ID
                        } else {
                            $template_id = 15; //Receptionist email template ID
                        }
                        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id($template_id);
                        $parse_arr = array(
                            '{FirstName}' => $user_name,
                            '{CreatedBy}' => $created_by,
                            '{Email}' => $email,
                            '{UniqueId}' => $inserted_staff_detail['user_unique_id'],
                            '{Password}' => $password,
                            '{UniquePasswordImage}' => 1,
                            '{AppName}' => APP_NAME,
                            '{WebUrl}' => DOMAIN_URL,
                            '{MailContactNumber}' => isset($email_template_data['email_static_data']['contact_number']) ? $email_template_data['email_static_data']['contact_number'] : '',
                            '{MailEmailAddress}' => isset($email_template_data['email_static_data']['email_id']) ? $email_template_data['email_static_data']['email_id'] : '',
                            '{MailCompanyName}' => isset($email_template_data['email_static_data']['company_name']) ? $email_template_data['email_static_data']['company_name'] : '',
                            '{CopyRightsYear}' => date('Y')
                        );
                        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
                        $subject = $email_template_data['email_template_subject'];
                        
                        $this->send_email(array($email => $email), $subject, $message);
                    }
                    $this->my_response['status'] = true;
                    $this->my_response['message'] = lang('staff_added');
                    $this->my_response['staff_id'] = $insert_id;
                } else {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('failure');
                }
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to update the staff the detail
     * 
     
     * 
     */
    public function update_staff_post() {

        try {

            $first_name = !empty($this->post_data['first_name']) ? trim($this->Common_model->escape_data($this->post_data['first_name'])) : "";
            $last_name = !empty($this->post_data['last_name']) ? trim($this->Common_model->escape_data($this->post_data['last_name'])) : "";
            $email = !empty($this->post_data['email']) ? trim($this->Common_model->escape_data($this->post_data['email'])) : "";
            $phone_number = !empty($this->post_data['phone_number']) ? trim($this->Common_model->escape_data($this->post_data['phone_number'])) : "";
            $other_user_id = !empty($this->post_data['other_user_id']) ? trim($this->Common_model->escape_data($this->post_data['other_user_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";
            $gender = !empty($this->post_data['gender']) ? trim($this->Common_model->escape_data($this->post_data['gender'])) : "";
            $address = !empty($this->post_data['address']) ? trim($this->Common_model->escape_data($this->post_data['address'])) : "";
            $address1 = !empty($this->post_data['address1']) ? trim($this->Common_model->escape_data($this->post_data['address1'])) : "";
            $city_id = !empty($this->post_data['city_id']) ? trim($this->Common_model->escape_data($this->post_data['city_id'])) : "";
            $state_id = !empty($this->post_data['state_id']) ? trim($this->Common_model->escape_data($this->post_data['state_id'])) : "";
            $country_id = !empty($this->post_data['country_id']) ? trim($this->Common_model->escape_data($this->post_data['country_id'])) : "";
            $pincode = !empty($this->post_data['pincode']) ? trim($this->Common_model->escape_data($this->post_data['pincode'])) : "";
            $latitude = !empty($this->post_data['latitude']) ? trim($this->Common_model->escape_data($this->post_data['latitude'])) : "";
            $longitude = !empty($this->post_data['longitude']) ? trim($this->Common_model->escape_data($this->post_data['longitude'])) : "";
            $longitude = !empty($this->post_data['longitude']) ? trim($this->Common_model->escape_data($this->post_data['longitude'])) : "";
            $locality = !empty($this->post_data['locality']) ? trim($this->Common_model->escape_data($this->post_data['locality'])) : "";
            $tfa = !empty($this->post_data['tfa']) ? trim($this->Common_model->escape_data($this->post_data['tfa'])) : "";
            $staff_type = !empty($this->post_data['staff_type']) ? trim($this->Common_model->escape_data($this->post_data['staff_type'])) : "";
            $edu_qualification = !empty($this->post_data['edu_qualification']) ? json_decode($this->post_data['edu_qualification'], true) : array();
            $registration_details = !empty($this->post_data['registration_details']) ? json_decode($this->post_data['registration_details'], true) : array();
            $assign_clinic_id = !empty($this->post_data['assign_clinic_id']) ? $this->post_data['assign_clinic_id'] : '';
            $assign_old_clinic_id = !empty($this->post_data['assign_old_clinic_id']) ? $this->post_data['assign_old_clinic_id'] : '';
            if (empty($assign_clinic_id)) {
                $this->bad_request();
            }
            $assign_clinic_id = explode(',', $assign_clinic_id);
            $assign_old_clinic_id = explode(',',$assign_old_clinic_id);
            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 12,
                    'key' => 2
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }

            if (
                    (!empty($first_name) && validate_characters($first_name)) ||
                    (!empty($last_name) && validate_characters($last_name))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("mycontroller_invalid_request");
                $this->send_response();
            }

            if (!empty($email) && validate_email($email)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_email");
                $this->send_response();
            }

            if (
                    (!empty($phone_number) && validate_phone_number($phone_number))
            ) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("invalid_phone_number");
                $this->send_response();
            }


            //check email id exists or not
            if (!empty($email)) {
                $check_email_sql = "SELECT 
                                        user_email, user_email_verified
                                 FROM 
                                    " . TBL_USERS . " 
                                 WHERE 
                                      user_email = '" . $email . "' 
                                 AND         
                                      user_status != 9
                                 AND
                                      user_id != '" . $other_user_id . "'
                                 AND 
                                      user_type = 2 ";

                $check_email = $this->Common_model->get_single_row_by_query($check_email_sql);

                if (!empty($check_email)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_email_exist");
                    $this->send_response();
                }
            }

            //check phone number exists or not
            if (!empty($phone_number)) {
                $check_number_sql = "SELECT 
                                        user_phone_number
                                 FROM 
                                    " . TBL_USERS . " 
                                 WHERE 
                                      user_phone_number = '" . $phone_number . "' 
                                 AND         
                                      user_status != 9
                                 AND
                                      user_id != '" . $other_user_id . "'
                                 AND 
                                      user_type = 2 ";

                $check_number = $this->Common_model->get_single_row_by_query($check_number_sql);

                if (!empty($check_number)) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang("user_register_phone_number_exist");
                    $this->send_response();
                }
            }

            if (!empty($first_name)) {
                $update_data['user_first_name'] = ucwords(strtolower($first_name));
            }

            if (!empty($last_name)) {
                $update_data['user_last_name'] = ucwords(strtolower($last_name));
            }

            if (!empty($email)) {
                $update_data['user_email'] = $email;
            }

            if (!empty($phone_number)) {
                $update_data['user_phone_number'] = $phone_number;
            }
            $update_data['user_gender'] = $gender;
            $update_data['user_modified_at'] = $this->utc_time_formated;
            $user_is_updated = $this->User_model->update_profile($other_user_id, $update_data);
            $diff_clinic_ids = array_diff($assign_old_clinic_id, $assign_clinic_id);
            $insert_clinic_mapping = [];
            foreach ($assign_clinic_id as $key => $value) {
                if(!in_array($value, $assign_old_clinic_id)) {
                    $insert_clinic_mapping[] = [
                        'doctor_clinic_mapping_role_id' => $staff_type,
                        'doctor_clinic_mapping_clinic_id' => $value,
                        'doctor_clinic_mapping_user_id' => $other_user_id,
                        'doctor_clinic_mapping_doctor_id' => $doctor_id,
                        'doctor_clinic_mapping_created_at' => $this->utc_time_formated
                    ];
                }
            }
            if(!empty($diff_clinic_ids) && count($diff_clinic_ids) > 0) {
                foreach ($diff_clinic_ids as $key => $value) {
                    $delete_clinic_mapping = [
                        'doctor_clinic_mapping_clinic_id' => $value,
                        'doctor_clinic_mapping_user_id' => $other_user_id,
                    ];
                    $update_array = array(
                        'doctor_clinic_mapping_status' => 9,
                        "doctor_clinic_mapping_modified_at" => $this->utc_time_formated
                    );
                    $this->Common_model->update(TBL_DOCTOR_CLINIC_MAPPING, $update_array, $delete_clinic_mapping);
                }
            }
            if(count($insert_clinic_mapping) > 0)
                $this->Common_model->insert_multiple(TBL_DOCTOR_CLINIC_MAPPING, $insert_clinic_mapping);
            if ($user_is_updated > 0) {
                //update the addrress
                $update_address_data = array(
                    'address_name'          => $address,
                    'address_name_one'      => $address1,
                    'address_city_id'       => $city_id,
                    'address_state_id'      => $state_id,
                    'address_country_id'    => $country_id,
                    'address_pincode'       => $pincode,
                    'address_latitude'      => $latitude,
                    'address_longitude'     => $longitude,
                    'address_locality'      => $locality,
                );
                
                $address_is_update = $this->User_model->update_address($other_user_id, $update_address_data);
                $setting_array[] = array(
                    'id' => 1,
                    'name' => '2 Factor authentication',
                    'status' => $tfa
                );
                $this->clinic->update2FASetting($other_user_id,$setting_array);
                if($staff_type == 2) {
                    $doctor_where = array(
                        "doctor_qualification_user_id" => $other_user_id,
                        "doctor_qualification_status" => 1
                    );
                    $update_array = array(
                        'doctor_qualification_status' => 9,
                        "doctor_qualification_modified_at" => $this->utc_time_formated
                    );
                    $this->Common_model->update(TBL_DOCTOR_EDUCATIONS, $update_array, $doctor_where);
                    if(is_array($edu_qualification) && count($edu_qualification) > 0) {
                        $insert_array = array();
                        if (isset($_FILES) && !empty($_FILES) && isset($_FILES['education_images'])) {
                            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_EDUCATIONS_FOLDER . "/" . $other_user_id;
                            $upload_folder = DOCTOR_EDUCATIONS_FOLDER . "/" . $other_user_id;
                            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $_FILES['education_images']), $upload_folder);
                        }
                        foreach ($edu_qualification as $key => $edu) {
                            if (empty($edu['doctor_qualification_id'])) {
                                $insert_array[] = array(
                                    "doctor_qualification_user_id" => $other_user_id,
                                    "doctor_qualification_qualification_id" => $edu['doctor_qualification_qualification_id'],
                                    "doctor_qualification_degree" => $edu['doctor_qualification_degree'],
                                    "doctor_qualification_college" => $edu['doctor_qualification_college'],
                                    "doctor_qualification_completion_year" => $edu['doctor_qualification_completion_year'],
                                    "doctor_qualification_image" => (isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? $profile_image_name[$key] : '',
                                    "doctor_qualification_image_full_path" => (isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? (get_file_json_detail(DOCTOR_EDUCATIONS_FOLDER."/".$other_user_id."/".$profile_image_name[$key])) : '',
                                    "doctor_qualification_created_at" => $this->utc_time_formated,
                                    "doctor_qualification_status" => 1,
                                );
                            } else {
                                $update_array = array(
                                    "doctor_qualification_degree" => $edu['doctor_qualification_degree'],
                                    "doctor_qualification_college" => $edu['doctor_qualification_college'],
                                    "doctor_qualification_completion_year" => $edu['doctor_qualification_completion_year'],
                                    "doctor_qualification_modified_at" => $this->utc_time_formated,
                                    "doctor_qualification_status" => 1
                                );
                                if (isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) {
                                    $update_array['doctor_qualification_image_full_path'] = get_file_json_detail(DOCTOR_EDUCATIONS_FOLDER . "/" . $other_user_id . "/" . $profile_image_name[$key]);
                                    $update_array['doctor_qualification_image'] = $profile_image_name[$key];
                                }
                                $where = array(
                                    "doctor_qualification_id" => $edu['doctor_qualification_id']
                                );
                                $this->Common_model->update(TBL_DOCTOR_EDUCATIONS, $update_array, $where);
                            }
                        }
                        if (!empty($insert_array) && count($insert_array) > 0) {
                            $this->db->insert_batch(TBL_DOCTOR_EDUCATIONS, $insert_array);
                        }
                    }
                    $doctor_where = array(
                        "doctor_registration_user_id" => $other_user_id,
                        "doctor_registration_status" => 1
                    );
                    $update_array = array(
                        'doctor_registration_status' => 9,
                        "doctor_registration_modified_at" => $this->utc_time_formated
                    );
                    $this->Common_model->update(TBL_DOCTOR_REGISTRATIONS, $update_array, $doctor_where);
                    if(is_array($registration_details) && count($registration_details) > 0) {
                        $insert_array = array();
                        if (isset($_FILES) && isset($_FILES['registration_images'])) {
                            $upload_path = UPLOAD_REL_PATH . "/" . DOCTOR_REGISTRATIONS_FOLDER . "/" . $other_user_id;
                            $upload_folder = DOCTOR_REGISTRATIONS_FOLDER . "/" . $other_user_id;
                            $profile_image_name = do_upload_multiple3($upload_path, array('photo' => $_FILES['registration_images']), $upload_folder);
                        }
                        foreach ($registration_details as $key => $reg) {
                            if (empty($reg['doctor_registration_id'])) {
                                $insert_array[] = array(
                                    "doctor_registration_user_id" => $other_user_id,
                                    "doctor_registration_council_id" => $reg['doctor_registration_council_id'],
                                    "doctor_council_registration_number" => $reg['doctor_council_registration_number'],
                                    "doctor_registration_year" => $reg['doctor_registration_year'],
                                    "doctor_registration_image_filepath" => (isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? (get_file_json_detail(DOCTOR_REGISTRATIONS_FOLDER . "/" . $other_user_id . "/" . $profile_image_name[$key])) : '',
                                    "doctor_registration_image" =>(isset($profile_image_name) && isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) ? $profile_image_name[$key] : '',
                                    "doctor_registration_created_at" => $this->utc_time_formated,
                                    "doctor_registration_status" => 1,
                                );
                            } else {
                                $update_array = array(
                                    "doctor_registration_council_id" => $reg['doctor_registration_council_id'],
                                    "doctor_council_registration_number" => $reg['doctor_council_registration_number'],
                                    "doctor_registration_year" => $reg['doctor_registration_year'],
                                    "doctor_registration_modified_at" => $this->utc_time_formated,
                                    "doctor_registration_status" => 1,
                                );
                                if (isset($profile_image_name[$key]) && !empty($profile_image_name[$key])) {
                                    $update_array['doctor_registration_image_filepath'] = get_file_json_detail(DOCTOR_REGISTRATIONS_FOLDER . "/" . $other_user_id . "/" . $profile_image_name[$key]);
                                    $update_array['doctor_registration_image'] = $profile_image_name[$key];
                                }
                                $where = array(
                                    "doctor_registration_id" => $reg['doctor_registration_id']
                                );
                                $this->Common_model->update(TBL_DOCTOR_REGISTRATIONS, $update_array, $where);
                            }
                        }
                        if (!empty($insert_array) && count($insert_array) > 0) {
                            $this->db->insert_batch(TBL_DOCTOR_REGISTRATIONS, $insert_array);
                        }
                    }
                }
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang("user_profile_update_success");
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang("failure");
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the staff details
     * 
     
     */
    public function get_staff_post() {

        try {

            $clinic_id = !empty($this->post_data['clinic_id']) ? trim($this->Common_model->escape_data($this->post_data['clinic_id'])) : "";
            $doctor_id = !empty($this->post_data['doctor_id']) ? trim($this->Common_model->escape_data($this->post_data['doctor_id'])) : "";

            if (empty($clinic_id)) {
                $this->bad_request();
            }

            $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 12,
                    'key' => 3
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            }
            if(empty($doctor_id)) {
                $doctor_id = $this->user_id;
            }
            $request_data = array(
                'doctor_id' => $doctor_id
            );

            $get_staff_details = $this->clinic->get_staff_details($request_data);
            $doctor_global_setting = $this->clinic->get_doctor_global_setting($doctor_id);
            $no_of_assistant = DOCTOR_GLOBAL_SETTING['ASSISTANT_LIMIT'];
            $no_of_receptionist = DOCTOR_GLOBAL_SETTING['RECEPTIONIST_LIMIT'];
            if(count($doctor_global_setting) > 0) {
                $global_setting = array_column($doctor_global_setting, 'setting_value','setting_name');
                if(!empty($global_setting['no_of_assistant']))
                    $no_of_assistant = $global_setting['no_of_assistant'];
                if(!empty($global_setting['no_of_receptionist']))
                    $no_of_receptionist = $global_setting['no_of_receptionist'];
            }
            $total_assistant = 0;
            $total_receptionist = 0;
            foreach ($get_staff_details as $key => $value) {
                if($value['user_role'] == 2)
                    $total_assistant++;
                if($value['user_role'] == 3)
                    $total_receptionist++;
                $get_staff_details[$key]['total_clinic'] = count(explode(',', $value['clinic_name']));
            }

            if (!empty($get_staff_details)) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['data'] = $get_staff_details;
                if($total_assistant >= $no_of_assistant)
                    $this->my_response['is_add_assistant'] = false;
                else
                    $this->my_response['is_add_assistant'] = true;

                if($total_receptionist >= $no_of_receptionist)
                    $this->my_response['is_add_receptionist'] = false;
                else
                    $this->my_response['is_add_receptionist'] = true;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
                $this->my_response['data'] = $get_staff_details;
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to get the staff details
     * 
     
     * 
     */
    public function get_staff_detail_post() {

        try {

            $staff_id = !empty($this->post_data['staff_id']) ? trim($this->Common_model->escape_data($this->post_data['staff_id'])) : "";

            if (empty($staff_id)) {
                $this->bad_request();
            }

            $get_staff_details = $this->clinic->getStaffWholeDetail(array('user_id' => $staff_id));
            if (!empty($get_staff_details)) {
                
                $where_array = array(
                    'setting_user_id' => $get_staff_details->user_id,
                    'setting_type' => 2,
                    'setting_status' => 1
                );
                $enable_2_way_authentication = 2;
                //[{"id":1,"name":"2 Factor authentication","status":$scope.reception.tfa}]
                $get_setting_data = $this->Common_model->get_single_row(TBL_SETTING, 'setting_data', $where_array);
                if (!empty($get_setting_data)) {
                    $get_setting_data = json_decode($get_setting_data['setting_data'], true);
                    if (!empty($get_setting_data)) {
                        foreach ($get_setting_data as $data) {
                            $enable_2_way_authentication = $data['status'];
                        }
                    }
                }
                $get_staff_details->user_photo_filepath = get_image_thumb($get_staff_details->user_photo_filepath);
                $get_staff_details->tfa = $enable_2_way_authentication;
                $this->load->model("Doctor_model", "doctor");
                $doctor_edu_data = $this->doctor->get_doctor_edu_detail($staff_id);
                $doctor_reg_data = $this->doctor->get_doctor_reg_detail($staff_id);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_found');
                $this->my_response['user_data'] = $get_staff_details;
                $this->my_response['doctor_edu_data'] = $doctor_edu_data;
                $this->my_response['doctor_reg_data'] = $doctor_reg_data;
            } else {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_not_found');
            }

            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /*** Description :- This function is used to set the availablity of the clinic day wise*/
    public function set_clinic_availability_post() {
        try {
            $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
            $set_availability = !empty($this->post_data['set_availability']) ? $this->post_data['set_availability'] : '';
            $set_availability = json_decode($set_availability, true);

            if (empty($clinic_id) ||
                    empty($set_availability) ||
                    empty($doctor_id)
            ) {
                $this->bad_request();
            }

            $set_availability_array = array();
            $sesstion_1_start_time_arr = $sesstion_1_end_time_arr = $sesstion_2_start_time_arr = $sesstion_2_end_time_arr = [];
            foreach ($set_availability as $avilability) {
                $session_1_start_time   = $avilability['session_1_start_time'];
                $session_1_end_time     = $avilability['session_1_end_time'];
                $session_2_start_time   = !empty($avilability['session_2_start_time']) ? $avilability['session_2_start_time'] : NULL;
                $session_2_end_time     = !empty($avilability['session_2_end_time']) ? $avilability['session_2_end_time'] : NULL;

                $set_availability_array[] = array(
                    'clinic_availability_clinic_id' => $clinic_id,
                    'clinic_availability_week_day' => $avilability['day'],
                    'clinic_availability_session_1_start_time' => $session_1_start_time,
                    'clinic_availability_session_1_end_time' => $session_1_end_time,
                    'clinic_availability_session_2_start_time' => $session_2_start_time,
                    'clinic_availability_session_2_end_time' => $session_2_end_time,
                    'clinic_availability_created_at' => $this->utc_time_formated
                );

                if (validate_time($session_1_start_time) ||
                        validate_time($session_1_end_time)
                ) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('invalid_time');
                    $this->send_response();
                }

                if (
                        (!empty($session_2_start_time) && validate_time($session_2_start_time)) ||
                        (!empty($session_2_end_time) && validate_time($session_2_end_time))
                ) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('invalid_time');
                    $this->send_response();
                }

                $start_1_timestamp = strtotime($session_1_start_time . ":00");
                $end_1_timestamp = strtotime($session_1_end_time . ":00");

                if (!empty($session_2_start_time) && !empty($session_2_end_time)) {

                    $start_2_timestamp = strtotime($session_2_start_time . ":00");
                    $end_2_timestamp = strtotime($session_2_end_time . ":00");

                    if ($start_2_timestamp >= $end_2_timestamp) {
                        $this->my_response['status'] = false;
                        $this->my_response['message'] = lang('invalid_time');
                        $this->send_response();
                    }
                
                    $sesstion_2_start_time_arr[] = $start_2_timestamp;
                    $sesstion_2_end_time_arr[]   = $end_2_timestamp;
                }

                if ($start_1_timestamp >= $end_1_timestamp) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('invalid_time');
                    $this->send_response();
                }
                $sesstion_1_start_time_arr[] = $start_1_timestamp;
                $sesstion_1_end_time_arr[]   = $end_1_timestamp;
            }

            $update_clinic_availablity = array(
                'clinic_availability_status' => 9,
                'clinic_availability_modified_at' => $this->utc_time_formated
            );
            $update_clinic_availablity_where = array(
                'clinic_availability_clinic_id' => $clinic_id,
            );
            $this->Common_model->update(TBL_CLINIC_AVAILABILITY, $update_clinic_availablity, $update_clinic_availablity_where);
            $this->Common_model->insert_multiple(TBL_CLINIC_AVAILABILITY, $set_availability_array);

            /*[START] UPDATE DOCTOR_CLINIC_MAPPING SESSTIONS TIME*/
                $update_sesstion_timings = [];
                if (isset($sesstion_1_start_time_arr) && count($sesstion_1_start_time_arr)>0 && isset($sesstion_1_end_time_arr) && count($sesstion_1_end_time_arr)>0) {
                    $doctor_clinic_doctor_session_1_start_time = min($sesstion_1_start_time_arr);
                    $doctor_clinic_doctor_session_1_end_time = max($sesstion_1_end_time_arr);
                    $update_sesstion_timings['doctor_clinic_doctor_session_1_start_time'] = date('H:i:s',$doctor_clinic_doctor_session_1_start_time);
                    $update_sesstion_timings['doctor_clinic_doctor_session_1_end_time'] = date('H:i:s',$doctor_clinic_doctor_session_1_end_time);
                }
                if (isset($sesstion_2_start_time_arr) && count($sesstion_2_start_time_arr)>0 && isset($sesstion_2_end_time_arr) && count($sesstion_2_end_time_arr)>0) {
                    $doctor_clinic_doctor_session_2_start_time = min($sesstion_2_start_time_arr);
                    $doctor_clinic_doctor_session_2_end_time = max(array_merge($sesstion_2_end_time_arr,$sesstion_1_end_time_arr));   

                    $update_sesstion_timings['doctor_clinic_doctor_session_2_start_time'] = date('H:i:s',$doctor_clinic_doctor_session_2_start_time);
                    $update_sesstion_timings['doctor_clinic_doctor_session_2_end_time'] = date('H:i:s',$doctor_clinic_doctor_session_2_end_time);
                } else {
                    $update_sesstion_timings['doctor_clinic_doctor_session_2_start_time'] = NULL;
                    $update_sesstion_timings['doctor_clinic_doctor_session_2_end_time'] = NULL;
                }
                if(count($update_sesstion_timings)>0){
                    $update_sesstion_timings['doctor_clinic_mapping_modified_at'] = $this->utc_time_formated;
                    $update_where = array(
                        'doctor_clinic_mapping_user_id' => $doctor_id,
                        'doctor_clinic_mapping_clinic_id' => $clinic_id
                    );
                    $this->Common_model->update(TBL_DOCTOR_CLINIC_MAPPING, $update_sesstion_timings, $update_where);
                }
            /*[END] UPDATE DOCTOR_CLINIC_MAPPING SESSTIONS TIME*/
            $this->my_response['status'] = true;
            $this->my_response['message'] = lang('availability_set');
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * Description :- This function is used to update the duration of the doctor for that clinic
     * 
     
     * 
     */
    public function update_clinic_duration_post() {

        $clinic_id = !empty($this->post_data['clinic_id']) ? $this->Common_model->escape_data($this->post_data['clinic_id']) : '';
        $doctor_id = !empty($this->post_data['doctor_id']) ? $this->Common_model->escape_data($this->post_data['doctor_id']) : '';
        $duration = !empty($this->post_data['duration']) ? $this->Common_model->escape_data($this->post_data['duration']) : '';
        $clinic_id_arr = !empty($this->post_data['clinic_id_arr']) ? $this->post_data['clinic_id_arr'] : '';

        try {
            if (empty($clinic_id) ||
                    empty($doctor_id) ||
                    empty($duration)
            ) {
                $this->bad_request();
            }

            if (!is_numeric($duration)) {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('mycontroller_invalid_request');
                $this->send_response();
            }

            $update_duration = array(
                'doctor_clinic_mapping_duration' => $duration,
                'doctor_clinic_mapping_modified_at' => $this->utc_time_formated
            );

            $update_where = array(
                'doctor_clinic_mapping_user_id' => $doctor_id,
                'doctor_clinic_mapping_clinic_id' => $clinic_id
            );

            $is_update = $this->Common_model->update(TBL_DOCTOR_CLINIC_MAPPING, $update_duration, $update_where);
            if ($is_update) {
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('common_detail_update');
            } else {
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('failure');
            }
            $this->send_response();
        } catch (ErrorException $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /*** Desscription :- This function is used to get the list of the doctor clinics*/
    public function get_clinic_list_post() {
        try {
            //get clinics data
            $doctor_id = !empty($this->post_data['doctor_id']) ? $this->post_data['doctor_id'] : '';
            if (empty($doctor_id)) {
                $this->bad_request();
                exit;
            }
            $clinic_data = $this->clinic->get_clinic_list($doctor_id);
            if(isset($clinic_data) && !empty($clinic_data) && count($clinic_data) > 0){
                $minTimeArr = [];
                $maxTimeArr = [];
                $durationArr = [];
                foreach ($clinic_data as $key => $value) {
                    $durationArr[] = $value['doctor_clinic_mapping_duration'];
                    $minTimeArr[] = strtotime($value['doctor_clinic_doctor_session_1_start_time']);
                    $maxTimeArr[] = strtotime($value['doctor_clinic_doctor_session_1_end_time']);
                    if(!empty($value['doctor_clinic_doctor_session_2_end_time']))
                        $maxTimeArr[] = strtotime($value['doctor_clinic_doctor_session_2_end_time']);
                    $clinic_data[$key]['online_in_clinic'] = "1";
                    $clinic_data[$key]['online_tele_clinic'] = "1";
                    if(!empty($value['setting_data'])) {
                        $booking_arr = json_decode($value['setting_data']);
                        if(!empty($booking_arr->tele_consultation))
                            $clinic_data[$key]['online_tele_clinic'] = $booking_arr->tele_consultation;
                        if(!empty($booking_arr->tele_consultation))
                            $clinic_data[$key]['online_in_clinic'] = $booking_arr->doctor_visit;
                    }
                }
                $minTime = date("H:i:s", min($minTimeArr));
                $maxTime = date("H:i:s", max($maxTimeArr));
                $minDuration = min($durationArr);

				$clinic_id = array_column($clinic_data, 'clinic_id');
				$get_clinic_availability_list = $this->clinic->get_clinic_availability($clinic_id);	
                $clinic_availability_list = array();
                $slots = get_time_slots($get_clinic_availability_list, $minTime, $maxTime, $minDuration);
                foreach ($get_clinic_availability_list as $key => $value) {
                    $clinic_availability_list[$value['clinic_availability_clinic_id']][] = $value;
                }
				$this->my_response['clinic_availability'] = $clinic_availability_list;
                $this->my_response['other_data'] = [
                    'minTime' => $minTime,
                    'maxTime' => $maxTime,
                    'minDuration' => $minDuration,
                    'timeSlots' => $slots,
                ];
			}
			$this->my_response['status'] = true;
            $this->my_response['clinic_data'] = $clinic_data;
            $this->send_response();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /*** delete a staff user*/
    public function delete_staff_user_post(){
        try {
            $staff_user_id = $this->post_data['staff_user_id'];
            
            // temp delete
            /* $get_role_details = $this->Common_model->get_the_role($this->user_id);
            if (!empty($get_role_details['user_role_data'])) {
                $permission_data = array(
                    'role_data' => $get_role_details['user_role_data'],
                    'module' => 11,
                    'key' => 1
                );
                $check_module_permission = $this->check_module_permission($permission_data);
                if ($check_module_permission == 2) {
                    $this->my_response['status'] = false;
                    $this->my_response['message'] = lang('permission_error');
                    $this->send_response();
                }
            } */
            if (empty($staff_user_id)) {
                $this->bad_request();
            }

            $result = $this->db->update(TBL_USERS,array('user_status' => 9),array('user_id' => $staff_user_id));
            if($result){
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('clinic_staff_delete_success');
            }else{
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('clinic_staff_delete_error');
            }
            $this->send_response();
        }catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    /**
     * change staff user status
     * @author Mehul Jethloja
     */
    public function change_status_staff_user_post(){
        try{
            $staff_user_id = $this->post_data['staff_user_id'];
            $status = $this->post_data['status'];

            if (empty($staff_user_id) || empty($status)) {
                $this->bad_request();
            }
            // TODO : need to check implement in permission
            $result = $this->db->update(TBL_USERS,array('user_status' => $status),array('user_id' => $staff_user_id));
            if($result){
                $where = array(
                    "udt_u_id" => $staff_user_id
                );
        
                $this->Common_model->delete_data(TBL_USER_DEVICE_TOKENS, $where);
                $this->my_response['status'] = true;
                $this->my_response['message'] = lang('clinic_staff_status_success');
            }else{
                $this->my_response['status'] = false;
                $this->my_response['message'] = lang('clinic_staff_status_error');
            }
            $this->send_response();
        }catch(Exception $ex){
            $this->error = $ex->getMessage();
            $this->store_error();
        }
    }

    public function testmail_post(){
        
        $this->load->model('Emailsetting_model');
        $email_template_data = $this->Emailsetting_model->get_emailtemplate_by_id(15);
        $email = "medeasysagar@gmail.com";
        $parse_arr = array(
            '{FirstName}' => "test",
            '{CreatedBy}' => 'test cre',
            '{Email}' => $email,
            '{UniqueId}' => 'QUAAUEJW',
            '{Password}' => "Test@124",
            '{UniquePasswordImage}' => 1,
            '{AppName}' => APP_NAME,
            //'{MailContactNumber}' => $email_template_data['email_static_data']['contact_number'],
            //'{MailEmailAddress}' => $email_template_data['email_static_data']['email_id'],
            //'{MailCompanyName}' => $email_template_data['email_static_data']['company_name'],
            //'{CopyRightsYear}' => date('Y')
        );
        $message = replace_values_in_string($parse_arr, $email_template_data['email_template_message']);
        $subject = $email_template_data['email_template_subject'];
        
        
        $this->send_email(array($email => $email), $subject, $message);
        echo '<pre>';
        print_r("dd");
        exit();
        $this->my_response['status'] = true;
                $this->my_response['message'] = lang('clinic_staff_status_success');
        $this->send_response();
    }
}

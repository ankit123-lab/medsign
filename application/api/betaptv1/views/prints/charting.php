<html>
    <head>
        <style>
            body{
                font-family: "gotham_book";
            }
            .gotham_light{
                font-family: 'gotham_light';
            }
            .gotham_book{
                font-family: 'gotham_book';
            }
            .gotham_medium{
                font-family: 'gotham_medium';
            }
            .title{
                font-family: "gotham_medium";
                font-size: 24px;
            }
            .sub_title{
                font-family: "gotham_medium";
                font-size: 20px;
                margin: 25px 0px 0px;
            }
            .text_center{
                text-align: center;
            }
            .text_right{
                text-align: right;
            }
            .text_left{
                text-align: left;
            }
            .pull_left{
                float: left;    
            }
            .pull_right{
                float: right;
            }
            .width_100{
                width: 100%;
            }
            .width_50{
                width: 50%;
            }
            .width_20{
                width: 20%;
            }
            .border_top_bottom{
                border-top: 1px solid #d1d1d1;
                border-bottom: 1px solid #d1d1d1;
            }
            .border_bottom{
                border-bottom: 1px solid #d1d1d1;
            }
            .p_15_top_bottom{
                padding: 15px 0px;
            }
            .p_15_left_right{
                padding: 0xp 15px;
            }
            .patient_info p{
                margin: 0px;
            }
            .custom_table tr td {
                border-bottom:1pt solid #d1d1d1;
                padding: 10px 0px;
            }
            .pre_custom_table tr td {
                padding: 10px 0px;
            }
            .custom_table, .pre_custom_table { 
                border-collapse: collapse; 
                width: 100%
            }
            .custom_td_width .heading td {
                width: 20%;
                word-break: break-all;
                word-wrap: break-word;
                hyphens: auto;
            }
            .text_upper{
                text-transform: uppercase;
            }
            .border-bottom-0{
                border-bottom:0px !important;
            }
            .no_margin{
                margin: 0px;
            }
        </style>
    </head>
    <body>
        <?php if(isset($teleConsultationMsg)){ ?>
        <div class="border_bottom p_15_top_bottom">
            <div class="text_center">
                <b><?php echo $teleConsultationMsg; ?></b>
            </div>
        </div>
        <?php } ?>
        <div class="border_bottom p_15_top_bottom">
            <div class="pull_left width_50">
                For: <?= $patient_data['user_first_name'] . " " . $patient_data['user_last_name'] ?>
                <?php
                if (!empty($patient_data['user_gender'])) {
                    if ($patient_data['user_gender'] == 'male')
                        $gender = 'M';
                    else if ($patient_data['user_gender'] == 'female')
                        $gender = 'F';
                    else if ($patient_data['user_gender'] == 'other')
                        $gender = 'O';
                    else if ($patient_data['user_gender'] == 'undisclosed')
                        $gender = '';

                    echo ',' . $gender;
                }

                if (!empty($patient_data['user_details_dob'])) {
					$objCurAge = date_diff(date_create($patient_data['user_details_dob']), date_create('today'));
					$currAgeYR = $objCurAge->y;
					$currAgeM = $objCurAge->m;
					$currAgeD = $objCurAge->d;
					$ageString = '';
					if (($currAgeYR > 0) && ($currAgeM > 0) && ($currAgeD > 0))
						$ageString = $currAgeYR.' yr - '.$currAgeM.' m';
					else if (($currAgeYR == 0) && ($currAgeM == 0) && ($currAgeD > 0))
						$ageString = $currAgeD.' d';
					else if (($currAgeYR > 0) && ($currAgeM == 0) && ($currAgeD == 0))
						$ageString = $currAgeYR.' yr';
					else if (($currAgeYR > 0) && ($currAgeM > 0) && ($currAgeD == 0))
						$ageString = $currAgeYR.' yr';
					else if (($currAgeYR == 0) && ($currAgeM > 0) && ($currAgeD > 0))
						$ageString = $currAgeM.' m';
					else if (($currAgeYR > 0) && ($currAgeM == 0) && ($currAgeD > 0))
						$ageString = $currAgeYR.' yr';
					else if (($currAgeYR == 0) && ($currAgeM > 0) && ($currAgeD == 0))
						$ageString = $currAgeM.' m';
					else
						$ageString = "";
					
					if(!empty($ageString))
						echo ' - '.$ageString;
                    //echo "-" . date_diff(date_create($patient_data['user_details_dob']), date_create('today'))->y;
                }
                ?>
                <?php if(!empty($patient_data['user_phone_number'])){ ?>
                    <br/>Mobile: <?=$patient_data['user_phone_number']?>
                <?php } ?>
            </div>
            <div class="text_right">
                Appointment On : <?= date("d/m/Y", strtotime($doctor_data['appointment_date'])); ?>
            </div>        
        </div>
        <?php
        if (!empty($vitalsign_data)) {
            ?>
            <div>
                <p class="sub_title">Vital Signs</p>  
                <table class="custom_table custom_td_width">
                    <tr class="heading">
                        <td>WEIGHT (kg)</td>
                        <td>B.P (mmHg)</td>
                        <td>Pulse (Heart beats/min)</td>
                        <td>Temperature (<?= $vitalsign_data['vital_report_temperature_type'] == 1 ? "F" : "C" ?>)</td>
                        <td>RESP.RATE (Breaths/min)</td>
                    </tr>
                    <tr>
                        <td><?= number_format(($vitalsign_data['vital_report_weight'] / 2.20462), 2, ".", "") ?></td>
                        <td><?= $vitalsign_data['vital_report_bloodpressure_systolic'] . "/" . $vitalsign_data['vital_report_bloodpressure_diastolic'] ?></td>
                        <td><?= $vitalsign_data['vital_report_pulse'] ?></td>
                        <td><?= $vitalsign_data['vital_report_temperature'] ?></td>
                        <td><?= $vitalsign_data['vital_report_resp_rate'] ?></td>
                    </tr>
                </table>
            </div>
            <?php
        }

        if (!empty($clinicnote_data)) {
            ?>
            <div>
                <p class="sub_title">Clinical notes</p>  
                <table class="custom_table custom_td_width">
                    <?php
                    $clinical_notes_reports_kco = json_decode($clinicnote_data['clinical_notes_reports_kco'], true);
                    if (!empty($clinical_notes_reports_kco)) {
                        ?>
                        <tr class="heading">
                            <td>KCO</td>
                            <td>
                                <?= implode(", ", $clinical_notes_reports_kco) ?>
                            </td>
                        </tr>
                        <?php
                    }
                    $clinical_notes_reports_complaints = json_decode($clinicnote_data['clinical_notes_reports_complaints'], true);
                    if (!empty($clinical_notes_reports_complaints)) {
                        ?>
                        <tr class="heading">
                            <td>Complaints</td>
                            <td>
                                <?= implode(", ", $clinical_notes_reports_complaints) ?>
                            </td>
                        </tr>
                        <?php
                    }
                    $clinical_notes_reports_observation = json_decode($clinicnote_data['clinical_notes_reports_observation'], true);
                    if (!empty($clinical_notes_reports_observation)) {
                        ?>
                        <tr class="heading">
                            <td>Observation</td>
                            <td>
                                <?= implode(", ", $clinical_notes_reports_observation) ?>
                            </td>
                        </tr>
                        <?php
                    }
                    $clinical_notes_reports_diagnoses = json_decode($clinicnote_data['clinical_notes_reports_diagnoses'], true);
                    if (!empty($clinical_notes_reports_diagnoses)) {
                        ?>
                        <tr class="heading">
                            <td>Diagnoses</td>
                            <td>
                                <?= implode(", ", $clinical_notes_reports_diagnoses) ?>
                            </td>
                        </tr>
                        <?php
                    }
                    $clinical_notes_reports_add_notes = json_decode($clinicnote_data['clinical_notes_reports_add_notes'], true);
                    if (!empty($clinical_notes_reports_add_notes)) {
                        ?>
                        <tr class="heading">
                            <td>Notes</td>
                            <td>
                                <?= implode(", ", $clinical_notes_reports_add_notes) ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        if (!empty($prescription_data)) {
            ?>
            <div>
                <p class="sub_title">Rx</p>  
                <table class="pre_custom_table custom_td_width">
                    <tr class="heading">
                        <td class="text_upper" style="width: 50%;padding-right: 5px">Drug name</td>
                        <td class="text_upper" style="width: 20%">Frequency</td>
                        <td class="text_upper" style="width: 20%">Duration</td>
                        <td class="text_upper" style="width: 10%">QTY</td>
                    </tr>
                    <?php
                            foreach ($prescription_data as $data) {
                            ?>
                            <tr>
                                <td colspan="4" style="border-bottom:0px !important;border-top:1px solid #d1d1d1">
                                    <?= $data['drug_unit_medicine_type'] . ' ' . $data['prescription_drug_name'] . ' ' . $data['prescription_dosage'] . ' ' . $data['drug_unit_name'] ?>
                                </td>
                            </tr>
                            <tr>
                                <?php if (!empty($data['drug_generic_title'])) { ?>
                                            <td>Generic Name : <?= ucfirst(strtolower($data['drug_generic_title'])) ?></td>
                                <?php }else{ ?>
                                            <td></td>
                                <?php } ?>
                                <td><?= ucwords($data['freq']) ?></td>
                                <td>
                                    <?php
                                    echo $data['prescription_duration_value'];
                                    //if($data['prescription_frequency_id'] != 9 && $data['prescription_frequency_id'] != 10)
                                    {
                                        switch ($data['prescription_duration']) {
                                            case 2:
                                                echo " Week(s)";
                                                break;
                                            case 3:
                                                echo " Month(s)";
                                                break;
                                            default :
                                                echo " Day(s)";
                                                break;
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $duration = $data['prescription_duration_value'];
                                    $dosage = $data['prescription_dosage'];
                                    $duration_type = 1;
                                    $freq_id = $data['prescription_frequency_id'];
                                    if ($data['prescription_duration'] == 2)
                                        $duration_type = 7;
                                    else if ($data['prescription_duration'] == 3)
                                        $duration_type = 30;

                                    $qty = '-';
                                    if ($data['drug_unit_is_qty_calculate'] == 1) {
                                        if ($freq_id == 7) {
                                            if ($duration_type == 1) {
                                                $qty = ceil((1 * $dosage * ($duration / 7 )));
                                            } elseif ($duration_type == 7) {
                                                $qty = (1 * $dosage * $duration);
                                            } elseif ($duration_type == 30) {
                                                $qty = ceil((1 * $dosage * (($duration * 30) / 7 )));
                                            }
                                        } elseif ($freq_id == 8) {
                                            if ($duration_type == 1) {
                                                $qty = ceil((1 * $dosage * ($duration / 30 )));
                                            } elseif ($duration_type == 7) {
                                                $qty = ceil((1 * $dosage * (($duration * 7) / 30 )));
                                            } elseif ($duration_type == 30) {
                                                $qty = (1 * $dosage * $duration);
                                            }
                                        } elseif ($freq_id == 1 || $freq_id == 2 || $freq_id == 3)
                                            $qty = (1 * $dosage * $duration_type * $duration);
                                        elseif ($freq_id == 4)
                                            $qty = (2 * $dosage * $duration_type * $duration);
                                        elseif ($freq_id == 5)
                                            $qty = (3 * $dosage * $duration_type * $duration);
                                        elseif ($freq_id == 6)
                                            $qty = (4 * $dosage * $duration_type * $duration);
                                    }
                                    ?>
                                    <?php echo $qty; ?>
                                    <?php //= $data['tablets']  ?>

                                </td>
                            </tr>

                            <?php
                            if (!empty($data['prescription_intake']) || !empty($data['prescription_intake_instruction']) || !empty($data['prescription_frequency_instruction'])) {
                                ?>
                                <tr>
                                    <td style="padding-top:0px; font-size:12px;" colspan="4">
                                        Instruction : 
                                        <?php
                                        if (!empty($data['prescription_intake'])) {
                                            switch ($data['prescription_intake']) {
                                                case 1:
                                                    echo "Before Food , ";
                                                    break;
                                                case 2:
                                                    echo "After Food , ";
                                                    break;
                                                case 3:
                                                    echo "Along With Food , ";
                                                    break;
                                                case 4:
                                                    echo "Empty Stomach , ";
                                                    break;
                                                default :
                                                    echo "";
                                                    break;
                                            }
                                        }
                                        if (!empty($data['prescription_intake_instruction'])) {
                                            echo $data['prescription_intake_instruction'] . ", ";
                                        }
                                        if (!empty($data['prescription_frequency_instruction'])) {
                                            echo $data['prescription_frequency_instruction'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>

                            <?php
                        }
                        ?>
                </table>
            </div>
            <?php
        }
        if (!empty($patient_lab_orders_data)) {
            ?>
            <div>
                <p class="sub_title">Lab Orders</p>  
                <table class="custom_table custom_td_width">
                    <tr class="heading">
                        <td class="text_upper">Test name</td>
                        <td class="text_upper">instructions</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    $lab_report_test_name = json_decode($patient_lab_orders_data['lab_report_test_name'], true);
                    foreach ($lab_report_test_name as $test_name => $desc) {
                        ?>
                        <tr>
                            <td><?= $test_name ?></td>
                            <td><?= $desc ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
            <?php
        }
        ?>
        <?php if (!empty($procedure_data)) { ?>
            <div>
                <p class="sub_title">Completed procedure</p>  
                <table class="custom_table custom_td_width">
                    <?php
                    $procedure = json_decode($procedure_data['procedure_report_procedure_text'], true);
                    $instruction = $procedure_data['procedure_report_note'];
                    if (!empty($procedure)) {
                        ?>
                        <tr class="heading">
                            <td>Procedure</td>
                            <td>
                                <?= implode(", ", $procedure) ?>
                            </td>                            
                        </tr>
                        <?php
                    }
                    if (!empty($instruction)) {
                        ?>
                        <tr class="heading">
                            <td>Instruction</td>
                            <td>
                                <?= $instruction ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        <?php } ?>
        <?php
        if (!empty($prescription_data[0]['follow_up_instruction'])) {
            ?>
            <div style="margin-top: 30px;">
                <p class="no_margin"><b>Diet Instruction: </b> <?php echo $prescription_data[0]['follow_up_instruction']; ?></p>  
            </div>
            <?php
        }
        if (!empty($prescription_data[0]['follow_up_followup_date'])) {
            ?>
            <div>
                <p class="no_margin"><b>Next followup: </b> <?php echo $prescription_data[0]['follow_up_followup_date']; ?></p>  
            </div>
            <?php
        }
        ?>

        <div style="margin-top: 100px;text-align: right">
            <?php if(!empty($doctor_data['user_sign_filepath'])) { ?>
                <div style="text-align: right">
                    <img style="float:right; border-left: 0px !important;border-right: 0px !important;" src="<?= get_thumb_filename($doctor_data['user_sign_filepath']); ?>">
                </div>  
            <?php } ?>
            <?php echo DOCTOR." ". $doctor_data['user_first_name'] . " " . $doctor_data['user_last_name']; ?>
        </div>
        <?php
        if(!empty($patient_tool_document) && count($patient_tool_document) > 0) {
            foreach ($patient_tool_document as $document) { 
            ?>
                <div style="margin-top: 30px;">
                    <a target="_blank" href="<?= DOMAIN_URL;?>patient/document/<?= encrypt_decrypt($document['id'],'encrypt'); ?>"><?= DOMAIN_URL;?>patient/document/<?= encrypt_decrypt($document['id'],'encrypt'); ?></a> 
                </div>
            <?php
            }
        }
        ?>
        <?php if (!empty($billing_data)) { ?>
            <div>
                <p class="sub_title">Treatment plans</p>  
                <table class="custom_table custom_td_width">
                    <tr class="heading">
                        <td class="text_upper">Treatment</td>
                        <td class="text_upper">Cost (inr)</td>
                        <td class="text_upper">Discount (inr)</td>
                        <td class="text_upper">Total (inr)</td>
                    </tr>
                    <?php
                    $basic_cost = 0;
                    foreach ($billing_data as $data) {
                        $basic_cost = $basic_cost + $data['billing_detail_basic_cost'];
                        $discount = $data['billing_discount'];
                        $grand_total = $data['billing_grand_total'];
                        $payable_amount = $data['billing_total_payable'];
                        ?>
                        <tr>
                            <td><?= ucfirst($data['billing_detail_name']); ?></td>
                            <td><?= $data['billing_detail_basic_cost']; ?></td>
                            <td><?= $data['billing_detail_discount']; ?></td>
                            <td><?= $data['billing_detail_total']; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Total</td>
                        <td><?= $basic_cost ?></td>
                        <td><?= $discount ?></td>
                        <td><?= $grand_total ?></td>
                    </tr>
                    <tr>
                        <td>Total payable amount</td>
                        <td></td>
                        <td></td>
                        <td><?= $payable_amount; ?></td>
                    </tr>
                </table>
            </div>
        <?php } ?>
    </body>
</html>

<?php

namespace local_sap;

class mail_helper {

    /*public static function fake_user($recipientusername) {
        $user = [
                'id' => rand(),
                'email' => $recipientusername . '@uni-muenster.de',
                'deleted' => false,
                ''
        ]
    }*/

    public static function send_course_request_mail($recipientusername, $course, $requestid) {
        global $USER;

        $email = username_to_mail($recipientusername);
        $user = get_or_create_user($recipientusername, $email); // TODO vielleicht nicht benutzen.
        $params = new \stdClass();
        $params->a = $USER->firstname . " " . $USER->lastname;
        $params->c = utf8_encode($course->titel);

        $data = array('recipientid' => $user->id, 'requesterid' => $USER->id, 'requesterfirstname' => $USER->firstname,
                'requesterlastname' => $USER->lastname, 'requestid' => $requestid, 'params' => $params);
        $sendemail = new \local_lsf_unification\task\send_mail_request_teacher_to_create_course();
        $sendemail->set_custom_data($data);
        \core\task\manager::queue_adhoc_task($sendemail);
    }

}
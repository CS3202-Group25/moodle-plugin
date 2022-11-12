<?php
    $capabilities = [
        'mod/workflow:addinstance' => [
            'riskbitmask' => RISK_XSS,
            'captype' => 'write',
            'contextlevel' => CONTEXT_COURSE,
            'archetypes' => [
                'editingteacher' => CAP_ALLOW,
                'manager' => CAP_ALLOW,
            ],
            'clonepermissionsfrom' => 'moodle/course:manageactivities',
        ],

        'mod/workflow:createrequest' => array(
            'captype' => 'write',
            'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'student' => CAP_ALLOW,
            ),
        ),

        'mod/workflow:forwardrequest' => array(
            'captype' => 'write',
            'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'teacher' => CAP_ALLOW,
            ),
        ),

        'mod/workflow:approverequest' => array(
            'captype' => 'write',
            'contextlevel' => CONTEXT_MODULE,
            'archetypes' => array(
                'editingteacher' => CAP_ALLOW,
            ),
        ),
];
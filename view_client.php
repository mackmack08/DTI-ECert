<?php
include("dbcon.php");

// Check if client ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Client ID is required']);
    exit;
}

$clientId = intval($_GET['id']);

// Fetch client data
$stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->bind_param("i", $clientId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Client not found']);
    exit;
}

$client = $result->fetch_assoc();

// Format the data for response
$response = [
    'client' => [   
        'id' => $client['id'],
        'reference_id' => $client['reference_id'],
        'timestamp' => $client['timestamp'],
        'client_name' => $client['client_name'],
        'client_type' => $client['client_type'],
        'sex' => $client['sex'],
        'age' => $client['age'],
        'region' => $client['region'],
        'contact' => $client['contact'],
        'email' => $client['email'],
        'completion_date' => $client['completion_date'] ?? null,
    ],
    'feedback' => [
        'service_rating_objectives' => [
            'objectives_achieved' => $client['service_ro_objectives_achieved'] ?? '',
            'info_received' => $client['service_ro_info_received'] ?? '',
            'relevance_value' => $client['service_ro_relevance_value'] ?? '',
            'duration_sufficient' => $client['service_ro_duration_sufficient'] ?? ''
        ],
        'service_access_functionality' => [
            'sign_up_access' => $client['service_af_sign_up_access'] ?? '',
            'audio_video_sync' => $client['service_af_audio_video_sync'] ?? ''
        ],
        'resource_speaker' => [
            'quality' => [
                'knowledge' => $client['resource_speaker_rq_knowledge'] ?? '',
                'clarity' => $client['resource_speaker_rq_clarity'] ?? '',
                'engagement' => $client['resource_speaker_rq_engagement'] ?? '',
                'visual_relevance' => $client['resource_speaker_rq_visual_relevance'] ?? ''
            ],
            'interaction' => [
                'answer_questions' => $client['resource_speaker_ri_answer_questions'] ?? '',
                'chat_responsiveness' => $client['resource_speaker_ri_chat_responsiveness'] ?? ''
            ]
        ],
        'moderator' => [
            'manage_discussion' => $client['moderator_rr_manage_discussion'] ?? '',
            'monitor_raises_questions' => $client['moderator_rr_monitor_raises_questions'] ?? '',
            'manage_program' => $client['moderator_rr_manage_program'] ?? ''
        ],
        'host_secretariat' => [
            'technical_assistance' => $client['host_secretariat_rr_technical_assistance'] ?? '',
            'admittance_management' => $client['host_secretariat_rr_admittance_management'] ?? ''
        ],
        'overall' => [
            'satisfaction_rating' => $client['overall_satisfaction_rating'] ?? '',
            'dissatisfied_reasons' => $client['feedback_dissatisfied_reasons'] ?? '',
            'improvement_suggestions' => $client['feedback_improvement_suggestions'] ?? ''
        ]
    ],
    'file' => [
        'cert_type' => $client['cert_type'] ?? null,
        'file_id' => $client['file_id'] ?? null,
        'file_path' => $client['file_path'] ?? ''
    ]
];

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);
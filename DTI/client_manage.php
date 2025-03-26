<?php
include('dbcon.php'); // Include the database connection file

// Retrieve client data from the clients table
$sql = "SELECT id, timestamp, client_name, client_type, sex, age, region, contact, email, 
service_ro_objectives_achieved, service_ro_info_received, service_ro_relevance_value, service_ro_duration_sufficient,
service_af_sign_up_access, service_af_audio_video_sync, resource_speaker_rq_knowledge, resource_speaker_rq_clarity, 
resource_speaker_rq_engagement, resource_speaker_rq_visual_relevance, 
resource_speaker_ri_answer_questions, resource_speaker_ri_chat_responsiveness, moderator_rr_manage_discussion,
moderator_rr_monitor_raises_questions, moderator_rr_manage_program, host_secretariat_rr_technical_assistance,
host_secretariat_rr_admittance_management, overall_satisfaction_rating, feedback_dissatisfied_reasons, feedback_improvement_suggestions
FROM clients";
$result = $conn->query($sql);

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Client Management</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>Name of Client</th>
                        <th>Client type</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Region of residence</th>
                        <th>Contact Number</th>
                        <th>Email Address</th>
                        <th>SERVICE: Reliability and Outcome [Objectives of the webinar is achieved]</th>
                        <th>SERVICE: Reliability and Outcome [Information expected to be presented is received]</th>
                        <th>SERVICE: Reliability and Outcome [Relevance and value of the information to current needs]</th>
                        <th>SERVICE: Reliability and Outcome [Duration is sufficient for the topic/s covered]</th>
                        <th>SERVICE: Access and Facilities [Ease of signing-up, sign in and access to the virtual platform]</th>
                        <th>SERVICE: Access and Facilities [Audio and video are well-synchronized]</th>
                        <th>RESOURCE SPEAKER: Reliability, Communication and Quality [Knowledge and mastery of the topic presented]</th>
                        <th>RESOURCE SPEAKER: Reliability, Communication and Quality [Clarity and delivery of the presentation]</th>
                        <th>RESOURCE SPEAKER: Reliability, Communication and Quality [Ability to engage with the audience effectively]</th>
                        <th>RESOURCE SPEAKER: Reliability, Communication and Quality [Relevance of the visual presentation to topic set]</th>
                        <th>RESOURCE SPEAKER: Responsiveness and Integrity [Ability to answer relevant questions from attendees]</th>
                        <th>RESOURCE SPEAKER: Responsiveness and Integrity [Ability to respond to chat discussions]</th>
                        <th>MODERATOR: Reliability and Responsiveness [Ability to manage the discussion during the Open forum]</th>
                        <th>MODERATOR: Reliability and Responsiveness [Ability to monitor and raise questions from webinar's chat function and content]</th>
                        <th>MODERATOR: Reliability and Responsiveness [Ability to manage the overall program]</th>
                        <th>HOST/SECRETARIAT: Reliability and Responsiveness [Ability to provide technical assistance to speakers and participants]</th>
                        <th>HOST/SECRETARIAT: Reliability and Responsiveness [Ability to manage participant's admittance efficiently]</th>
                        <th>OVERALL SATISFACTION RATING  [Overall evaluation of this webinar]</th>
                        <th>Please write in the space below your reason/s for your "DISSATISFIED" or "VERY DISSATISFIED" rating so that we will know in which area/s we need to improve. </th>
                        <th>Please give comments/suggestions to help us improve our service/s:</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                            <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['client_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['sex']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['region']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_ro_objectives_achieved']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_ro_info_received']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_ro_relevance_value']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_ro_duration_sufficient']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_af_sign_up_access']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_af_audio_video_sync']); ?></td>
                            <td><?php echo htmlspecialchars($row['resource_speaker_rq_knowledge']); ?></td>
                            <td><?php echo htmlspecialchars($row['resource_speaker_rq_clarity']); ?></td>
                            <td><?php echo htmlspecialchars($row['resource_speaker_rq_engagement']); ?></td>
                            <td><?php echo htmlspecialchars($row['resource_speaker_rq_visual_relevance']); ?></td>
                            <td><?php echo htmlspecialchars($row['resource_speaker_ri_answer_questions']); ?></td>
                            <td><?php echo htmlspecialchars($row['resource_speaker_ri_chat_responsiveness']); ?></td>
                            <td><?php echo htmlspecialchars($row['moderator_rr_manage_discussion']); ?></td>
                            <td><?php echo htmlspecialchars($row['moderator_rr_monitor_raises_questions']); ?></td>
                            <td><?php echo htmlspecialchars($row['moderator_rr_manage_program']); ?></td>
                            <td><?php echo htmlspecialchars($row['host_secretariat_rr_technical_assistance']); ?></td>
                            <td><?php echo htmlspecialchars($row['host_secretariat_rr_admittance_management']); ?></td>
                            <td><?php echo htmlspecialchars($row['overall_satisfaction_rating']); ?></td>
                            <td><?php echo htmlspecialchars($row['feedback_dissatisfied_reasons']); ?></td>
                            <td><?php echo htmlspecialchars($row['feedback_improvement_suggestions']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No clients found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once '../database/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $patient_id = $_GET['patient_id'] ?? '';
    if (empty($patient_id)) {
        echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
        exit;
    }

    // Get patient basic info
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
        exit;
    }

    // Get surgical_details data
    $stmt = $pdo->prepare("SELECT * FROM surgical_details WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $surgical_details = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get surgical_skin_preparation data
    $stmt = $pdo->prepare("SELECT * FROM surgical_skin_preparation WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $surgical_skin_preparation = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get risk_factors data
    $stmt = $pdo->prepare("SELECT * FROM risk_factors WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $risk_factors = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get implanted_materials data
    $stmt = $pdo->prepare("SELECT * FROM implanted_materials WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $implanted_materials = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get drains data (can be multiple, fetchAll)
    $stmt = $pdo->prepare("SELECT * FROM drains WHERE patient_id = ? ORDER BY drain_number ASC");
    $stmt->execute([$patient_id]);
    $drains = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get antibiotic_usage data (can be multiple, fetchAll)
    $stmt = $pdo->prepare("SELECT * FROM antibiotic_usage WHERE patient_id = ? ORDER BY serial_no ASC");
    $stmt->execute([$patient_id]);
    $antibiotic_usage = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get post_operative_monitoring data (can be multiple, fetchAll)
    $stmt = $pdo->prepare("SELECT * FROM post_operative_monitoring WHERE patient_id = ? ORDER BY day ASC");
    $stmt->execute([$patient_id]);
    $post_operative_monitoring = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get cultural_dressing data
    $stmt = $pdo->prepare("SELECT * FROM cultural_dressing WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $cultural_dressing = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get wound_complications data
    $stmt = $pdo->prepare("SELECT * FROM wound_complications WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $wound_complications = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get review_sutures data
    $stmt = $pdo->prepare("SELECT * FROM review_sutures WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $review_sutures = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get review_phone data
    $stmt = $pdo->prepare("SELECT * FROM review_phone WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $review_phone = $stmt->fetch(PDO::FETCH_ASSOC);

    // Combine all data
    $patientData = [
        'patient' => $patient,
        'surgical_details' => $surgical_details,
        'surgical_skin_preparation' => $surgical_skin_preparation,
        'risk_factors' => $risk_factors,
        'implanted_materials' => $implanted_materials,
        'drains' => $drains,
        'antibiotic_usage' => $antibiotic_usage,
        'post_operative_monitoring' => $post_operative_monitoring,
        'cultural_dressing' => $cultural_dressing,
        'wound_complications' => $wound_complications,
        'review_sutures' => $review_sutures,
        'review_phone' => $review_phone
    ];

    // Check if this is an API request (from form.html) or direct browser access
    $isApiRequest = isset($_GET['format']) && $_GET['format'] === 'json';
    $isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    // Debug logging
    error_log("get_patient_data.php - patient_id: " . $patient_id);
    error_log("get_patient_data.php - isApiRequest: " . ($isApiRequest ? 'true' : 'false'));
    error_log("get_patient_data.php - isAjaxRequest: " . ($isAjaxRequest ? 'true' : 'false'));
    error_log("get_patient_data.php - HTTP_X_REQUESTED_WITH: " . ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'not set'));
    
    if ($isApiRequest || $isAjaxRequest) {
        // Return JSON for API requests
        error_log("get_patient_data.php - Returning JSON response");
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $patientData
        ]);
        exit;
    }

    // Display as HTML for direct browser access
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Patient Details - <?php echo htmlspecialchars($patient['name']); ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <style>
            :root {
                --bg-body: #f3f4f6;
                --bg-card: #ffffff;
                --text-primary: #111827;
                --text-secondary: #6b7280;
                --border-color: #e5e7eb;
                --accent-color: #dc2626;
            }
            
            body { 
                font-family: 'Inter', sans-serif; 
                background-color: var(--bg-body); 
                color: var(--text-primary); 
            }
            
            .detail-card {
                background-color: var(--bg-card);
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }
            
            .section-title {
                background: linear-gradient(135deg, var(--accent-color) 0%, #b91c1c 100%);
                color: white;
                padding: 1rem 1.5rem;
                font-weight: 600;
            }
            
            .data-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1rem;
            }
            
            .data-item {
                padding: 0.75rem;
                border-bottom: 1px solid var(--border-color);
            }
            
            .data-item:last-child {
                border-bottom: none;
            }
            
            .data-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: var(--text-secondary);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 0.25rem;
            }
            
            .data-value {
                font-size: 0.875rem;
                color: var(--text-primary);
                font-weight: 500;
            }
            
            .complication-badge {
                background-color: #fef2f2;
                color: #dc2626;
                padding: 0.25rem 0.5rem;
                border-radius: 0.375rem;
                font-size: 0.75rem;
                font-weight: 600;
                display: inline-block;
                margin: 0.125rem;
            }
            
            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
                display: inline-block;
            }
            
            .status-completed {
                background-color: #dcfce7;
                color: #166534;
            }
            
            .status-pending {
                background-color: #fef3c7;
                color: #92400e;
            }
            
            .status-progress {
                background-color: #dbeafe;
                color: #1e40af;
            }
        </style>
    </head>
    <body>
        <div class="min-h-screen py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Patient Details</h1>
                            <p class="text-gray-600 mt-2">UHID: <?php echo htmlspecialchars($patient['uhid']); ?></p>
                        </div>
                        <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>Close
                        </button>
                    </div>
                </div>

                <!-- Patient Basic Information -->
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-user mr-2"></i>Basic Information
                    </div>
                    <div class="p-6">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Name</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['name']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Age</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['age']); ?> years</div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Sex</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['sex']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Phone</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['phone']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Ward</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['bed_ward']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Address</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['address']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Surgical Details -->
                <?php if ($surgical_details): ?>
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-stethoscope mr-2"></i>Surgical Details
                    </div>
                    <div class="p-6">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Date of Admission</div>
                                <div class="data-value"><?php echo $surgical_details['doa'] ? date('d/m/Y', strtotime($surgical_details['doa'])) : '—'; ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Date of Surgery</div>
                                <div class="data-value"><?php echo $surgical_details['dos'] ? date('d/m/Y', strtotime($surgical_details['dos'])) : '—'; ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Date of Discharge</div>
                                <div class="data-value"><?php echo $surgical_details['dod'] ? date('d/m/Y', strtotime($surgical_details['dod'])) : '—'; ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Surgeon</div>
                                <div class="data-value"><?php echo htmlspecialchars($surgical_details['surgeon']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Operation Duration</div>
                                <div class="data-value"><?php echo htmlspecialchars($surgical_details['operation_duration']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Diagnosis and Procedure -->
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-notes-medical mr-2"></i>Diagnosis & Procedure
                    </div>
                    <div class="p-6">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Primary Diagnosis</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['primary_diagnosis']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Surgical Procedure</div>
                                <div class="data-value"><?php echo htmlspecialchars($patient['surgical_procedure']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wound Complications -->
                <?php if ($wound_complications): ?>
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Wound Complications
                    </div>
                    <div class="p-6">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Complication Date</div>
                                <div class="data-value"><?php echo $wound_complications['complication_date'] ? date('d/m/Y', strtotime($wound_complications['complication_date'])) : '—'; ?></div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="data-label mb-2">Complications Detected:</div>
                            <div class="flex flex-wrap gap-2">
                                <?php if ($wound_complications['wound_dehiscence']): ?>
                                    <span class="complication-badge">Wound Dehiscence</span>
                                <?php endif; ?>
                                <?php if ($wound_complications['allergic_reaction']): ?>
                                    <span class="complication-badge">Allergic Reaction</span>
                                <?php endif; ?>
                                <?php if ($wound_complications['bleeding_haemorrhage']): ?>
                                    <span class="complication-badge">Bleeding/Haemorrhage</span>
                                <?php endif; ?>
                                <?php if ($wound_complications['superficial_ssi']): ?>
                                    <span class="complication-badge">Superficial SSI</span>
                                <?php endif; ?>
                                <?php if ($wound_complications['deep_si']): ?>
                                    <span class="complication-badge">Deep SSI</span>
                                <?php endif; ?>
                                <?php if ($wound_complications['organ_space_ssi']): ?>
                                    <span class="complication-badge">Organ Space SSI</span>
                                <?php endif; ?>
                                <?php if ($wound_complications['other_complication']): ?>
                                    <span class="complication-badge">Other: <?php echo htmlspecialchars($wound_complications['other_specify']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Review Status -->
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-clipboard-check mr-2"></i>Review Status
                    </div>
                    <div class="p-6">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Review Date</div>
                                <div class="data-value"><?php echo $review_sutures['review_on'] ? date('d/m/Y', strtotime($review_sutures['review_on'])) : '—'; ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Sutures Removed</div>
                                <div class="data-value"><?php echo $review_sutures['sutures_removed_on'] ? date('d/m/Y', strtotime($review_sutures['sutures_removed_on'])) : '—'; ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Status</div>
                                <div class="data-value">
                                    <?php
                                    $status = 'Pending';
                                    $statusClass = 'status-pending';
                                    if ($review_sutures['review_on'] && $review_sutures['sutures_removed_on']) {
                                        $status = 'Completed';
                                        $statusClass = 'status-completed';
                                    } elseif ($review_sutures['review_on'] || $review_sutures['sutures_removed_on']) {
                                        $status = 'In Progress';
                                        $statusClass = 'status-progress';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Data Sections -->
                <?php if ($surgical_skin_preparation): ?>
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-shower mr-2"></i>Surgical Skin Preparation
                    </div>
                    <div class="p-6">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Pre-op Bath</div>
                                <div class="data-value"><?php echo htmlspecialchars($surgical_skin_preparation['pre_op_bath']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Hair Removal</div>
                                <div class="data-value"><?php echo htmlspecialchars($surgical_skin_preparation['hair_removal']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Hair Removal Location</div>
                                <div class="data-value"><?php echo htmlspecialchars($surgical_skin_preparation['hair_removal_location']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($implanted_materials): ?>
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-cog mr-2"></i>Implanted Materials
                    </div>
                    <div class="p-6">
                        <div class="data-grid">
                            <div class="data-item">
                                <div class="data-label">Implant Used</div>
                                <div class="data-value"><?php echo htmlspecialchars($implanted_materials['implanted_used']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Metal</div>
                                <div class="data-value"><?php echo htmlspecialchars($implanted_materials['metal']); ?></div>
                            </div>
                            <div class="data-item">
                                <div class="data-label">Graft</div>
                                <div class="data-value"><?php echo htmlspecialchars($implanted_materials['graft']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Antibiotic Usage -->
                <?php if (!empty($antibiotic_usage)): ?>
                <div class="detail-card mb-6">
                    <div class="section-title">
                        <i class="fas fa-pills mr-2"></i>Antibiotic Usage
                    </div>
                    <div class="p-6">
                        <?php foreach ($antibiotic_usage as $index => $antibiotic): ?>
                        <div class="mb-4 p-4 border rounded-lg" style="border-color: var(--border-color);">
                            <h4 class="font-semibold mb-2">Antibiotic <?php echo $index + 1; ?></h4>
                            <div class="data-grid">
                                <div class="data-item">
                                    <div class="data-label">Drug Name</div>
                                    <div class="data-value"><?php echo htmlspecialchars($antibiotic['drug_name']); ?></div>
                                </div>
                                <div class="data-item">
                                    <div class="data-label">Dosage</div>
                                    <div class="data-value"><?php echo htmlspecialchars($antibiotic['dosage_route_frequency']); ?></div>
                                </div>
                                <div class="data-item">
                                    <div class="data-label">Started On</div>
                                    <div class="data-value"><?php echo $antibiotic['started_on'] ? date('d/m/Y', strtotime($antibiotic['started_on'])) : '—'; ?></div>
                                </div>
                                <div class="data-item">
                                    <div class="data-label">Stopped On</div>
                                    <div class="data-value"><?php echo $antibiotic['stopped_on'] ? date('d/m/Y', strtotime($antibiotic['stopped_on'])) : '—'; ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </body>
    </html>
    <?php

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>

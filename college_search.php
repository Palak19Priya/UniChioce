<?php
// Suppress all notices and warnings to prevent them from breaking JSON output
error_reporting(0);

// Start output buffering to capture any unwanted output
ob_start();

session_start();
require_once 'db_config.php';

// Log errors to file instead of outputting to response
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log("Starting college search process");

// Debug mode - set to true to see detailed info
$debug_mode = true;

// Function to calculate match percentage between user preferences and college
function calculateMatchPercentage($college, $preferences) {
    $total_criteria = 0;
    $matched_criteria = 0;
    
    // Course match
    if (!empty($preferences['course']) && !empty($college['courses_offered'])) {
        $total_criteria++;
        if (strpos(strtolower($college['courses_offered']), strtolower($preferences['course'])) !== false) {
            $matched_criteria++;
        }
    }
    
    // Major match
    if (!empty($preferences['major']) && !empty($college['majors_available'])) {
        $total_criteria++;
        if (strpos(strtolower($college['majors_available']), strtolower($preferences['major'])) !== false) {
            $matched_criteria++;
        }
    }
    
    // Institution type match
    if (!empty($preferences['institution_type']) && !empty($college['type'])) {
        $total_criteria++;
        if (strtolower($college['type']) == strtolower($preferences['institution_type'])) {
            $matched_criteria++;
        }
    }
    
    // Mode of study match
    if (!empty($preferences['mode_of_study']) && !empty($college['mode_of_study'])) {
        $total_criteria++;
        if (strtolower($college['mode_of_study']) == strtolower($preferences['mode_of_study'])) {
            $matched_criteria++;
        }
    }
    
    // Country match
    if (!empty($preferences['country']) && !empty($college['country'])) {
        $total_criteria++;
        if (strtolower($college['country']) == strtolower($preferences['country'])) {
            $matched_criteria++;
        }
    }
    
    // Region match
    if (!empty($preferences['region']) && !empty($college['region'])) {
        $total_criteria++;
        if (strtolower($college['region']) == strtolower($preferences['region'])) {
            $matched_criteria++;
        }
    }
    
    // Campus setting match
    if (!empty($preferences['campus_setting']) && !empty($college['campus_setting'])) {
        $total_criteria++;
        if (strtolower($college['campus_setting']) == strtolower($preferences['campus_setting'])) {
            $matched_criteria++;
        }
    }
    
    // Academic criteria
    if (isset($preferences['tenth_percentage']) && isset($college['min_tenth_percentage'])) {
        $total_criteria++;
        if ($college['min_tenth_percentage'] <= $preferences['tenth_percentage']) {
            $matched_criteria++;
        }
    }
    
    if (isset($preferences['twelfth_percentage']) && isset($college['min_twelfth_percentage'])) {
        $total_criteria++;
        if ($college['min_twelfth_percentage'] <= $preferences['twelfth_percentage']) {
            $matched_criteria++;
        }
    }
    
    // Extra-curricular activities
    if (isset($preferences['sports']) && isset($college['has_sports_facilities'])) {
        $total_criteria++;
        if ($preferences['sports'] && $college['has_sports_facilities']) {
            $matched_criteria++;
        }
    }
    
    if (isset($preferences['painting']) && isset($college['has_arts_facilities'])) {
        $total_criteria++;
        if ($preferences['painting'] && $college['has_arts_facilities']) {
            $matched_criteria++;
        }
    }
    
    if (isset($preferences['dance_music']) && isset($college['has_performing_arts'])) {
        $total_criteria++;
        if ($preferences['dance_music'] && $college['has_performing_arts']) {
            $matched_criteria++;
        }
    }
    
    // Financial criteria
    if (isset($preferences['budget_range']) && isset($college['annual_fees'])) {
        $total_criteria++;
        // Convert budget range to actual number for comparison
        $max_budget = 0;
        switch($preferences['budget_range']) {
            case 'less_than_1_lakh': $max_budget = 100000; break;
            case '1_to_2_lakhs': $max_budget = 200000; break;
            case '2_to_5_lakhs': $max_budget = 500000; break;
            case 'above_5_lakhs': $max_budget = 1000000; break;
            default: $max_budget = 1000000; // Default to high budget if not specified
        }
        
        if ($college['annual_fees'] <= $max_budget) {
            $matched_criteria++;
        }
    }
    
    if ((isset($preferences['scholarship']) || isset($preferences['education_loan'])) && 
        (isset($college['offers_scholarships']) || isset($college['has_loan_facility']))) {
        $total_criteria++;
        if ((isset($preferences['scholarship']) && $preferences['scholarship'] && isset($college['offers_scholarships']) && $college['offers_scholarships']) || 
            (isset($preferences['education_loan']) && $preferences['education_loan'] && isset($college['has_loan_facility']) && $college['has_loan_facility'])) {
            $matched_criteria++;
        }
    }
    
    // Avoid division by zero
    if ($total_criteria == 0) {
        return 0;
    }
    
    return ($matched_criteria / $total_criteria) * 100;
}

try {
    // Verify we have a username to work with
    if (!isset($_SESSION['username'])) {
        throw new Exception('Please login to search colleges');
    }

    // Get raw input data and decode JSON
    $raw_input = file_get_contents('php://input');
    error_log("Raw input received: " . $raw_input);
    $data = json_decode($raw_input, true);
    
    if ($data === null) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }
    
    // Make sure we have valid data in all fields
    // Set default values for any missing data
    $defaultData = [
        'course' => '',
        'major' => '',
        'institution_type' => '',
        'mode_of_study' => '',
        'country' => '',
        'region' => '',
        'campus_setting' => '',
        'tenth_percentage' => 0,
        'twelfth_percentage' => 0,
        'exam_scores' => '',
        'admission_preference' => '',
        'sports' => 0,
        'painting' => 0,
        'dance_music' => 0,
        'other_activities' => '',
        'budget_range' => '',
        'scholarship' => 0,
        'education_loan' => 0
    ];
    
    // Merge with defaults
    $data = array_merge($defaultData, array_filter($data, function($value) {
        return $value !== null;
    }));
    
    error_log("Processed data: " . print_r($data, true));

    // Connect to database
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Store user search preferences - only log the search, don't stop the process if it fails
    try {
        $stmt = $conn->prepare("INSERT INTO user_searches (
            username, course, major, institution_type, mode_of_study, 
            country, region, campus_setting, tenth_percentage, 
            twelfth_percentage, exam_scores, admission_preference,
            sports, painting, dance_music, other_activities,
            budget_range, scholarship, education_loan
        ) VALUES (
            :username, :course, :major, :institution_type, :mode_of_study,
            :country, :region, :campus_setting, :tenth_percentage,
            :twelfth_percentage, :exam_scores, :admission_preference,
            :sports, :painting, :dance_music, :other_activities,
            :budget_range, :scholarship, :education_loan
        )");
        
        $stmt->execute([
            ':username' => $_SESSION['username'],
            ':course' => $data['course'],
            ':major' => $data['major'],
            ':institution_type' => $data['institution_type'],
            ':mode_of_study' => $data['mode_of_study'],
            ':country' => $data['country'],
            ':region' => $data['region'],
            ':campus_setting' => $data['campus_setting'],
            ':tenth_percentage' => $data['tenth_percentage'],
            ':twelfth_percentage' => $data['twelfth_percentage'],
            ':exam_scores' => $data['exam_scores'],
            ':admission_preference' => $data['admission_preference'],
            ':sports' => $data['sports'] ? 1 : 0,
            ':painting' => $data['painting'] ? 1 : 0,
            ':dance_music' => $data['dance_music'] ? 1 : 0,
            ':other_activities' => $data['other_activities'],
            ':budget_range' => $data['budget_range'],
            ':scholarship' => $data['scholarship'] ? 1 : 0,
            ':education_loan' => $data['education_loan'] ? 1 : 0
        ]);
        error_log("Successfully logged search preferences");
    } catch (Exception $e) {
        // Log error but continue with search
        error_log("Failed to log search preferences: " . $e->getMessage());
    }

    // Fetch all colleges
    $stmt = $conn->query("SELECT * FROM colleges");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Fetched " . count($colleges) . " colleges from database");
    
    // Calculate match percentage for each college
    $matching_colleges = [];
    foreach ($colleges as $college) {
        $match_percentage = calculateMatchPercentage($college, $data);
        error_log("College ID {$college['id']} match: $match_percentage%");
        
        // Only include colleges with at least 20% match (lowered threshold to show more results)
        if ($match_percentage >= 20) {
            $college['match_percentage'] = round($match_percentage, 2);
            $matching_colleges[] = $college;
        }
    }
    
    // Sort colleges by match percentage (highest first)
    usort($matching_colleges, function($a, $b) {
        return $b['match_percentage'] <=> $a['match_percentage'];
    });
    
    // Clear any output from buffer before sending JSON
    ob_end_clean();
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Output JSON data
    echo json_encode([
        'status' => 'success',
        'message' => count($matching_colleges) > 0 ? 
            'Found ' . count($matching_colleges) . ' matching colleges' : 
            'No matching colleges found',
        'colleges' => $matching_colleges,
        'debug_info' => $debug_mode ? [
            'username' => $_SESSION['username'],
            'session_id' => session_id(),
            'request_time' => date('Y-m-d H:i:s'),
            'search_data' => $data,
            'college_count' => count($colleges),
            'match_count' => count($matching_colleges)
        ] : null
    ]);

} catch(Exception $e) {
    error_log("Error in college search: " . $e->getMessage());
    
    // Clear any output from buffer before sending JSON
    ob_end_clean();
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Return error message
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage(),
        'debug_info' => $debug_mode ? [
            'error_details' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString()
        ] : null
    ]);
}
?> 
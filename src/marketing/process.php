<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

requireRole(['marketing', 'manager']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add_lead') {
        $full_name = clean($_POST['full_name']);
        $email = clean($_POST['email']);
        $phone = clean($_POST['phone']);
        $school = clean($_POST['school_origin']);
        $program = clean($_POST['program_choice']);
        $marketing_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("INSERT INTO students (full_name, email, phone, school_origin, program_choice, marketing_id, status) VALUES (?, ?, ?, ?, ?, ?, 'lead')");
        $stmt->bind_param("sssssi", $full_name, $email, $phone, $school, $program, $marketing_id);
        
        if ($stmt->execute()) {
            $student_id = $conn->insert_id;
            
            $upload_dir = '../../public/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $doc_types = ['doc_ktp' => 'ktp', 'doc_ijazah' => 'ijazah', 'doc_payment' => 'bukti_bayar'];
            
            foreach ($doc_types as $field => $type) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
                    $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                    $filename = $type . '_' . $student_id . '_' . time() . '.' . $ext;
                    $filepath = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $filepath)) {
                        $db_path = 'uploads/' . $filename;
                        $stmt_doc = $conn->prepare("INSERT INTO documents (student_id, type, file_path) VALUES (?, ?, ?)");
                        $stmt_doc->bind_param("iss", $student_id, $type, $db_path);
                        $stmt_doc->execute();
                    }
                }
            }
            
            if (!empty($_POST['payment_amount']) && isset($_FILES['doc_payment']) && $_FILES['doc_payment']['error'] == 0) {
                $amount = (float)$_POST['payment_amount'];
                $proof_result = $conn->query("SELECT file_path FROM documents WHERE student_id = $student_id AND type = 'bukti_bayar' ORDER BY id DESC LIMIT 1");
                $proof_row = $proof_result->fetch_assoc();
                $proof_path = $proof_row ? $proof_row['file_path'] : null;
                
                $stmt_pay = $conn->prepare("INSERT INTO payments (student_id, amount, payment_date, proof_file, status) VALUES (?, ?, CURDATE(), ?, 'pending')");
                $stmt_pay->bind_param("ids", $student_id, $amount, $proof_path);
                $stmt_pay->execute();
            }
            
            header("Location: dashboard_staff.php?msg=success_add");
        } else {
            header("Location: input_leads.php?error=insert_failed");
        }
    }
}
?>

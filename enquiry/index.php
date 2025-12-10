<?php
session_start();
require_once '../includes/config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $learner_name = trim($_POST['learner_name']);
        $parent_name = trim($_POST['parent_name']);
        $grade_level = $_POST['grade_level'];
        $current_school = trim($_POST['current_school']);
        $date_of_birth = $_POST['date_of_birth'];
        $hear_about_us = $_POST['hear_about_us'];
        $further_notes = trim($_POST['further_notes']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
        $truthful_data = isset($_POST['truthful_data']) ? 1 : 0;

        // Validate required fields
        if (empty($learner_name) || empty($parent_name) || empty($email) || empty($phone_number) || empty($date_of_birth)) {
            $error = 'Please fill in all required fields.';
        } elseif (!$truthful_data) {
            $error = 'Please acknowledge that the information provided is truthful.';
        } else {
            // Insert enquiry
            $sql = "INSERT INTO enquiries (learner_name, parent_name, grade_level, current_school, date_of_birth, hear_about_us, further_notes, email, phone_number, truthful_data)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$learner_name, $parent_name, $grade_level, $current_school, $date_of_birth, $hear_about_us, $further_notes, $email, $phone_number, $truthful_data]);

            $success = 'Thank you for your enquiry! We will get back to you soon.';

            // Clear form
            $_POST = array();
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Grade level options
$grade_levels = ['Playgroup', 'pp1', 'pp2', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

// Hear about us options
$hear_about_options = ['Friends or family', 'Social Media', 'Billboards', 'Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiry Form - St. Philip Neri School</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-red: #dc2626;
            --color-green: #16a34a;
            --color-blue: #3b82f6;
            --color-purple: #8b5cf6;
            --color-yellow: #f59e0b;
            --color-orange: #ea580c;
            --color-black: #000000;
            --color-white: #ffffff;
            --color-gray-light: #f8fafc;
            --color-gray: #64748b;
            --color-gray-dark: #1e293b;

            --gradient-primary: linear-gradient(135deg, var(--color-red), var(--color-green));
            --gradient-blue: linear-gradient(135deg, var(--color-blue), #6366f1);

            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.2);

            --border-radius: 8px;
            --border-radius-lg: 12px;

            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: var(--color-gray-dark);
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            height: 80px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            color: var(--color-gray);
            font-size: 1.1rem;
        }

        .enquiry-form {
            background: var(--color-white);
            padding: 40px;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--color-gray-dark);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .required::after {
            content: " *";
            color: var(--color-red);
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--color-white);
            font-family: inherit;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--color-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 25px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-top: 3px;
        }

        .checkbox-label {
            font-size: 0.9rem;
            color: var(--color-gray-dark);
        }

        .submit-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .alert {
            padding: 15px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .enquiry-form {
                padding: 25px;
            }

            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
      <?php include '../includes/header.php'; ?>
    <div class="container">
        <div class="header">
            <!-- Add your school logo here -->
            <!-- <img src="path/to/logo.png" alt="School Logo" class="logo"> -->
            <h1>Admission Enquiry</h1>
            <p>Please fill out the form below and we'll get back to you shortly</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="enquiry-form">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required" for="learner_name">Learner's Full Name</label>
                    <input type="text" id="learner_name" name="learner_name" class="form-input" required
                           value="<?php echo htmlspecialchars($_POST['learner_name'] ?? ''); ?>"
                           placeholder="Enter learner's full name">
                </div>

                <div class="form-group">
                    <label class="form-label required" for="parent_name">Parent's Full Name</label>
                    <input type="text" id="parent_name" name="parent_name" class="form-input" required
                           value="<?php echo htmlspecialchars($_POST['parent_name'] ?? ''); ?>"
                           placeholder="Enter parent's full name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required" for="grade_level">Grade/Level</label>
                    <select id="grade_level" name="grade_level" class="form-select" required>
                        <option value="">Select Grade/Level</option>
                        <?php foreach ($grade_levels as $grade): ?>
                            <option value="<?php echo $grade; ?>"
                                <?php echo ($_POST['grade_level'] ?? '') === $grade ? 'selected' : ''; ?>>
                                <?php echo $grade; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label required" for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-input" required
                           value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="current_school">Current School</label>
                <input type="text" id="current_school" name="current_school" class="form-input"
                       value="<?php echo htmlspecialchars($_POST['current_school'] ?? ''); ?>"
                       placeholder="Enter current school name">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label class="form-label required" for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" class="form-input" required
                           value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>"
                           placeholder="Enter phone number">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label required" for="hear_about_us">How did you hear about us?</label>
                <select id="hear_about_us" name="hear_about_us" class="form-select" required>
                    <option value="">Select an option</option>
                    <?php foreach ($hear_about_options as $option): ?>
                        <option value="<?php echo $option; ?>"
                            <?php echo ($_POST['hear_about_us'] ?? '') === $option ? 'selected' : ''; ?>>
                            <?php echo $option; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="further_notes">Further Notes</label>
                <textarea id="further_notes" name="further_notes" class="form-textarea"
                          placeholder="Please share any particular preferences or other relevant considerations..."><?php echo htmlspecialchars($_POST['further_notes'] ?? ''); ?></textarea>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="truthful_data" name="truthful_data" value="1"
                       <?php echo isset($_POST['truthful_data']) ? 'checked' : ''; ?>>
                <label class="checkbox-label" for="truthful_data">
                    I acknowledge that the information provided above is true and accurate to the best of my knowledge.
                </label>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Submit Enquiry
            </button>
        </form>
    </div>

    <script>
        // Set maximum date for date of birth (18 years ago)
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const maxDate = new Date(today.getFullYear() - 2, today.getMonth(), today.getDate()); // At least 2 years old
            const minDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate()); // Maximum 18 years old

            const dobInput = document.getElementById('date_of_birth');
            dobInput.max = maxDate.toISOString().split('T')[0];
            dobInput.min = minDate.toISOString().split('T')[0];
        });
    </script>
</body>
</html>

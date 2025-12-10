<?php
session_start();
require_once 'config.php';

// Fetch active application forms
try {
    $forms_sql = "SELECT * FROM itable_application_forms WHERE is_active = 1 ORDER BY display_order ASC";
    $forms_stmt = $pdo->query($forms_sql);
    $forms = $forms_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $forms = [];
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $learner_name = trim($_POST['learner_name'] ?? '');
    $admission_number = trim($_POST['admission_number'] ?? '');
    $form_id = $_POST['form_id'] ?? '';
    $application_data = $_POST['application_data'] ?? [];

    // Basic validation
    if (!empty($learner_name) && !empty($admission_number) && !empty($form_id)) {
        try {
            $insert_sql = "INSERT INTO itable_application_responses
                          (form_id, learner_name, admission_number, application_data, submitted_at)
                          VALUES (?, ?, ?, ?, NOW())";
            $insert_stmt = $pdo->prepare($insert_sql);

            // Convert application data to JSON for storage
            $application_json = json_encode($application_data);

            $insert_stmt->execute([$form_id, $learner_name, $admission_number, $application_json]);

            // Set success message
            $_SESSION['application_success'] = "Your application has been submitted successfully!";

            // Redirect to prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $_SESSION['application_error'] = "There was an error submitting your application. Please try again.";
        }
    } else {
        $_SESSION['application_error'] = "Please fill in all required fields.";
    }
}

// Check for success/error messages
$success_message = $_SESSION['application_success'] ?? '';
$error_message = $_SESSION['application_error'] ?? '';
unset($_SESSION['application_success'], $_SESSION['application_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Applications - St. Philip Neri School</title>
    <meta name="description" content="Apply for co-curricular activities, holiday camps, and other school programs at St. Philip Neri School.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #2d5a27; /* Dark Green */
            --color-secondary: #000000; /* Black */
            --color-accent: #e53e3e; /* Red */
            --color-accent-light: #fed7d7;
            --color-text: #2d3748;
            --color-text-light: #718096;
            --color-background: #ffffff;
            --color-surface: #f7fafc;
            --color-border: #e2e8f0;
            --color-success: #38a169;
            --color-success-light: #c6f6d5;

            --gradient-hero: linear-gradient(135deg, #2d5a27 0%, #1a365d 100%);
            --gradient-accent: linear-gradient(135deg, #e53e3e, #dd6b20);
            --gradient-subtle: linear-gradient(135deg, #f7fafc, #edf2f7);

            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.1);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.15);

            --border-radius: 12px;
            --border-radius-lg: 20px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--color-background);
            color: var(--color-text);
            line-height: 1.6;
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient-hero);
            color: white;
            padding: 120px 2rem 80px;
            text-align: center;
            margin-top: 80px;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Section Headers */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--color-accent);
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--color-text-light);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Forms Grid */
        .forms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .form-card {
            background: var(--color-background);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .form-header {
            background: var(--color-primary);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .form-description {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .form-content {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--color-secondary);
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--color-border);
            border-radius: var(--border-radius);
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(45, 90, 39, 0.1);
        }

        .form-submit {
            background: var(--color-accent);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            font-size: 1rem;
        }

        .form-submit:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        .form-required {
            color: var(--color-accent);
        }

        /* Messages */
        .message {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .message-success {
            background: var(--color-success-light);
            color: var(--color-success);
            border-left: 4px solid var(--color-success);
        }

        .message-error {
            background: var(--color-accent-light);
            color: var(--color-accent);
            border-left: 4px solid var(--color-accent);
        }

        /* Features Section */
        .features-section {
            margin-bottom: 4rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .feature-item {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            text-align: center;
            transition: var(--transition);
        }

        .feature-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }

        .feature-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-secondary);
        }

        .feature-description {
            color: var(--color-text-light);
            font-size: 0.9rem;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-hero);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: var(--border-radius-lg);
            margin-bottom: 4rem;
        }

        .cta-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-description {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            opacity: 0.9;
        }

        .cta-button {
            display: inline-block;
            background: var(--color-accent);
            color: white;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .cta-button:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .forms-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 1rem;
            }

            .hero-section {
                padding: 100px 1rem 60px;
            }

            .form-card {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">School Applications</h1>
            <p class="hero-subtitle">Apply for co-curricular activities, holiday camps, and other exciting school programs</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="message message-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="message message-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Application Forms -->
        <section class="forms-section">
            <div class="section-header">
                <h2 class="section-title">Available Applications</h2>
                <p class="section-subtitle">Select from our range of school activities and programs. Fill out the forms below to apply.</p>
            </div>

            <?php if (!empty($forms)): ?>
                <div class="forms-grid">
                    <?php foreach ($forms as $form): ?>
                        <div class="form-card">
                            <div class="form-header">
                                <h3 class="form-title"><?php echo htmlspecialchars($form['title']); ?></h3>
                                <p class="form-description"><?php echo htmlspecialchars($form['description']); ?></p>
                            </div>
                            <form method="POST" class="form-content">
                                <input type="hidden" name="form_id" value="<?php echo $form['id']; ?>">

                                <div class="form-group">
                                    <label for="learner_name_<?php echo $form['id']; ?>" class="form-label">
                                        Learner's Full Name <span class="form-required">*</span>
                                    </label>
                                    <input type="text" id="learner_name_<?php echo $form['id']; ?>" name="learner_name" class="form-input" required>
                                </div>

                                <div class="form-group">
                                    <label for="admission_number_<?php echo $form['id']; ?>" class="form-label">
                                        Admission Number <span class="form-required">*</span>
                                    </label>
                                    <input type="text" id="admission_number_<?php echo $form['id']; ?>" name="admission_number" class="form-input" required>
                                </div>

                                <?php
                                // Generate form fields based on form type
                                $form_type = $form['form_type'];
                                switch($form_type) {
                                    case 'co_curricular':
                                        echo '
                                        <div class="form-group">
                                            <label for="main_activity_'.$form['id'].'" class="form-label">Main Activity</label>
                                            <select id="main_activity_'.$form['id'].'" name="application_data[main_activity]" class="form-select" required>
                                                <option value="">Select Main Activity</option>
                                                <option value="sports">Sports</option>
                                                <option value="arts">Arts & Culture</option>
                                                <option value="academic">Academic Clubs</option>
                                                <option value="leadership">Leadership & Service</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="specific_activity_'.$form['id'].'" class="form-label">Specific Activity</label>
                                            <select id="specific_activity_'.$form['id'].'" name="application_data[specific_activity]" class="form-select" required>
                                                <option value="">Select Specific Activity</option>
                                                <!-- Options will be populated by JavaScript based on main activity selection -->
                                            </select>
                                        </div>';
                                        break;

                                    case 'holiday_camp':
                                        echo '
                                        <div class="form-group">
                                            <label for="camp_week_'.$form['id'].'" class="form-label">Preferred Week</label>
                                            <select id="camp_week_'.$form['id'].'" name="application_data[camp_week]" class="form-select" required>
                                                <option value="">Select Week</option>
                                                <option value="week1">Week 1: June 10-14</option>
                                                <option value="week2">Week 2: June 17-21</option>
                                                <option value="week3">Week 3: June 24-28</option>
                                                <option value="week4">Week 4: July 1-5</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="emergency_contact_'.$form['id'].'" class="form-label">Emergency Contact Number</label>
                                            <input type="tel" id="emergency_contact_'.$form['id'].'" name="application_data[emergency_contact]" class="form-input" required>
                                        </div>';
                                        break;

                                    case 'special_program':
                                        echo '
                                        <div class="form-group">
                                            <label for="program_type_'.$form['id'].'" class="form-label">Program Type</label>
                                            <select id="program_type_'.$form['id'].'" name="application_data[program_type]" class="form-select" required>
                                                <option value="">Select Program Type</option>
                                                <option value="enrichment">Academic Enrichment</option>
                                                <option value="remedial">Remedial Support</option>
                                                <option value="talent">Talent Development</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="preferred_days_'.$form['id'].'" class="form-label">Preferred Days</label>
                                            <div>
                                                <label><input type="checkbox" name="application_data[preferred_days][]" value="monday"> Monday</label>
                                                <label><input type="checkbox" name="application_data[preferred_days][]" value="tuesday"> Tuesday</label>
                                                <label><input type="checkbox" name="application_data[preferred_days][]" value="wednesday"> Wednesday</label>
                                                <label><input type="checkbox" name="application_data[preferred_days][]" value="thursday"> Thursday</label>
                                                <label><input type="checkbox" name="application_data[preferred_days][]" value="friday"> Friday</label>
                                            </div>
                                        </div>';
                                        break;
                                }
                                ?>

                                <button type="submit" class="form-submit">Submit Application</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; background: var(--color-surface); border-radius: var(--border-radius);">
                    <i class="fas fa-clipboard-list" style="font-size: 4rem; color: var(--color-text-light); margin-bottom: 1rem;"></i>
                    <h3>No Active Applications</h3>
                    <p>There are currently no active application forms. Please check back later or contact the school office.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="section-header">
                <h2 class="section-title">Why Apply Online?</h2>
                <p class="section-subtitle">Our digital application system makes it easy to register for school programs</p>
            </div>

            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="feature-title">Quick & Easy</h3>
                    <p class="feature-description">Complete applications in minutes with our streamlined forms</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="feature-title">Instant Confirmation</h3>
                    <p class="feature-description">Receive immediate confirmation of your application submission</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="feature-title">Track Applications</h3>
                    <p class="feature-description">Monitor the status of your applications through your student portal</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure & Private</h3>
                    <p class="feature-description">Your information is protected with industry-standard security</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <h2 class="cta-title">Need Help With Your Application?</h2>
            <p class="cta-description">Our administrative staff is ready to assist you with any questions about our application process.</p>
            <a href="contact.php" class="cta-button">Contact School Office</a>
        </section>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to form cards on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.form-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Add animation to feature items
            document.querySelectorAll('.feature-item').forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(item);
            });

            // Dynamic activity options for co-curricular forms
            const activityOptions = {
                sports: ['Football', 'Basketball', 'Swimming', 'Athletics', 'Tennis', 'Cricket'],
                arts: ['Drama Club', 'Choir', 'Orchestra', 'Art Club', 'Dance'],
                academic: ['Debate Club', 'Science Club', 'Math Olympiad', 'Robotics', 'Chess Club'],
                leadership: ['Student Council', 'Community Service', 'Peer Mentoring', 'Event Planning']
            };

            // Set up event listeners for main activity dropdowns
            document.querySelectorAll('select[name="application_data[main_activity]"]').forEach(dropdown => {
                dropdown.addEventListener('change', function() {
                    const formId = this.id.split('_')[2];
                    const specificActivityDropdown = document.getElementById(`specific_activity_${formId}`);

                    // Clear existing options
                    specificActivityDropdown.innerHTML = '<option value="">Select Specific Activity</option>';

                    // Add new options based on selection
                    if (this.value && activityOptions[this.value]) {
                        activityOptions[this.value].forEach(activity => {
                            const option = document.createElement('option');
                            option.value = activity.toLowerCase().replace(/\s+/g, '_');
                            option.textContent = activity;
                            specificActivityDropdown.appendChild(option);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>

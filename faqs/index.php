<?php
require_once '../includes/config.php';

// Set page meta for SEO
$page_title = "Frequently Asked Questions - St. Philip Neri School";
$meta_description = "Find answers to common questions about St. Philip Neri School admissions, programs, facilities, and more.";
$meta_keywords = "school FAQs, admissions questions, academic programs, school facilities, St. Philip Neri";

// Define default FAQs if database functions aren't available
$faq_categories = [
    [
        'slug' => 'admissions',
        'name' => 'Admissions',
        'faqs' => [
            [
                'question' => 'What is the admission process at St. Philip Neri School?',
                'answer' => "Our admission process is designed to ensure the best fit for both the student and our school community. The process includes:\n\n1. Submission of the online application form\n2. Student assessment and interview\n3. Parent interview\n4. Submission of required documents\n5. Admission decision notification\n\nThe entire process typically takes 2-3 weeks from application submission to decision."
            ],
            [
                'question' => 'What are the age requirements for enrollment?',
                'answer' => "Students must meet the following age requirements by September 1st of the academic year:\n\n• Playgroup: 2 years old\n• Kindergarten: 4 years old\n• Grade 1: 6 years old\n\nWe do consider individual circumstances and development for borderline cases."
            ],
            [
                'question' => 'Are there any entrance exams or assessments?',
                'answer' => "Yes, we conduct age-appropriate assessments for all applicants. For younger children (Playgroup to Kindergarten), this is an informal observation of social, emotional, and basic academic readiness. For older students (Grade 1 and above), we assess literacy, numeracy, and problem-solving skills appropriate to their grade level.\n\nThe assessment helps us understand how we can best support your child's educational journey."
            ]
        ]
    ],
    [
        'slug' => 'academics',
        'name' => 'Academics',
        'faqs' => [
            [
                'question' => 'What curriculum does St. Philip Neri School follow?',
                'answer' => "We follow an enhanced national curriculum that incorporates international best practices. Our curriculum is designed to develop critical thinking, creativity, and problem-solving skills while ensuring strong foundational knowledge in core subjects.\n\nWe also integrate technology, environmental education, and character development across all grade levels."
            ],
            [
                'question' => 'What is the average class size?',
                'answer' => "We maintain small class sizes to ensure personalized attention for every student:\n\n• Playgroup: 12 students maximum\n• Kindergarten: 16 students maximum\n• Primary Grades: 20 students maximum\n\nThese small class sizes allow our teachers to provide individualized instruction and build strong relationships with each student."
            ]
        ]
    ],
    [
        'slug' => 'facilities',
        'name' => 'Facilities',
        'faqs' => [
            [
                'question' => 'What sports facilities are available?',
                'answer' => "Our campus features state-of-the-art sports facilities including:\n\n• Olympic-sized swimming pool\n• Indoor sports complex with basketball and volleyball courts\n• Outdoor athletic track and field\n• Soccer field\n• Tennis courts\n• Dedicated spaces for gymnastics and martial arts\n\nAll facilities are maintained to the highest standards and are accessible to students during physical education classes and extracurricular activities."
            ]
        ]
    ],
    [
        'slug' => 'tuition',
        'name' => 'Tuition & Fees',
        'faqs' => [
            [
                'question' => 'What is included in the tuition fee?',
                'answer' => "Our tuition fee includes:\n\n• All academic instruction and materials\n• Use of all school facilities\n• Standard textbooks and workbooks\n• Basic school supplies\n• Access to our digital learning platforms\n• Participation in regular school events and activities\n\nAdditional fees may apply for specialized programs, field trips, uniforms, and certain extracurricular activities. A detailed fee schedule is provided during the enrollment process."
            ]
        ]
    ],
    [
        'slug' => 'extracurricular',
        'name' => 'Extracurricular Activities',
        'faqs' => [
            [
                'question' => 'What extracurricular activities are available?',
                'answer' => "We offer a wide range of extracurricular activities to complement our academic program. These include:\n\nSports: Soccer, basketball, swimming, tennis, gymnastics, martial arts\n\nArts: Visual arts, drama, choir, orchestra, band, dance\n\nAcademic Clubs: Math club, science club, debate, robotics, coding\n\nOther Activities: Student government, community service, environmental club\n\nActivities vary by grade level and season. Most activities are included in tuition, though some specialized programs may have additional fees."
            ]
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reuse the CSS variables and reset from homepage */
        :root {
            --color-red: #dc2626;
            --color-green: #16a34a;
            --color-black: #000000;
            --color-white: #ffffff;
            --color-gray-light: #f8fafc;
            --color-gray: #64748b;
            --color-gray-dark: #1e293b;
            --color-gold: #f59e0b;
            --color-purple: #8b5cf6;
            --color-blue: #3b82f6;

            --gradient-primary: linear-gradient(135deg, var(--color-red), var(--color-green));
            --gradient-secondary: linear-gradient(135deg, var(--color-purple), var(--color-blue));
            --gradient-dark: linear-gradient(135deg, var(--color-black), var(--color-gray-dark));
            --gradient-gold: linear-gradient(135deg, var(--color-gold), #fbbf24);

            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 8px 25px rgba(0,0,0,0.15);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.2);
            --shadow-xl: 0 25px 50px rgba(0,0,0,0.25);

            --border-radius: 12px;
            --border-radius-lg: 20px;
            --border-radius-xl: 30px;

            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --transition-slow: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.7;
            color: var(--color-gray-dark);
            background: var(--color-white);
            overflow-x: hidden;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* FAQ Hero Section */
        .faq-hero {
            background: var(--gradient-primary);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .faq-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .faq-hero-content {
            position: relative;
            z-index: 1;
        }

        .faq-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        .faq-hero p {
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.9;
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 20px 25px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            box-shadow: var(--shadow-lg);
            background: rgba(255, 255, 255, 0.9);
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            background: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }

        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gradient-primary);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.1);
        }

        /* FAQ Navigation */
        .faq-nav {
            background: var(--color-white);
            padding: 30px 0;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .faq-categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .category-btn {
            padding: 12px 25px;
            background: var(--color-gray-light);
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--color-gray-dark);
        }

        .category-btn.active,
        .category-btn:hover {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* FAQ Content */
        .faq-content {
            padding: 80px 0;
            background: var(--color-gray-light);
        }

        .faq-category-section {
            margin-bottom: 60px;
            background: var(--color-white);
            border-radius: var(--border-radius-lg);
            padding: 50px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
        }

        .faq-category-section:hover {
            box-shadow: var(--shadow-lg);
        }

        .category-title {
            font-size: 2.2rem;
            color: var(--color-black);
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--color-red);
            display: inline-block;
            font-weight: 700;
        }

        .faq-item {
            margin-bottom: 25px;
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: var(--transition);
        }

        .faq-item:hover {
            border-color: var(--color-red);
        }

        .faq-question {
            width: 100%;
            padding: 25px 30px;
            background: var(--color-white);
            border: none;
            text-align: left;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
            color: var(--color-black);
        }

        .faq-question:hover {
            background: rgba(220, 38, 38, 0.05);
        }

        .faq-question.active {
            background: rgba(220, 38, 38, 0.1);
            color: var(--color-red);
        }

        .faq-icon {
            transition: var(--transition);
            color: var(--color-red);
        }

        .faq-question.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 30px;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
            background: var(--color-white);
        }

        .faq-answer.active {
            padding: 30px;
            max-height: 1000px;
        }

        .faq-answer p {
            margin-bottom: 15px;
            color: var(--color-gray-dark);
            white-space: pre-line;
        }

        .faq-answer p:last-child {
            margin-bottom: 0;
        }

        /* Contact CTA Section */
        .contact-cta {
            background: var(--gradient-secondary);
            color: white;
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .contact-cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .contact-cta-content {
            position: relative;
            z-index: 1;
        }

        .contact-cta h2 {
            font-size: 2.8rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .contact-cta p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.9;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-buttons {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 16px 35px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* No Results Message */
        .no-results {
            text-align: center;
            padding: 60px 0;
            background: var(--color-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
        }

        .no-results i {
            font-size: 4rem;
            color: var(--color-gray);
            margin-bottom: 20px;
        }

        .no-results h3 {
            font-size: 1.8rem;
            color: var(--color-black);
            margin-bottom: 15px;
        }

        .no-results p {
            color: var(--color-gray);
            max-width: 500px;
            margin: 0 auto;
        }

        /* Animation Classes */
        .fade-in {
            opacity: 0;
            transform: translateY(50px);
            transition: var(--transition-slow);
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .stagger-item {
            opacity: 0;
            transform: translateY(50px);
            transition: var(--transition-slow);
        }

        .stagger-item.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .faq-hero h1 {
                font-size: 3rem;
            }
        }

        @media (max-width: 768px) {
            .faq-hero {
                padding: 100px 0 60px;
            }

            .faq-hero h1 {
                font-size: 2.5rem;
            }

            .faq-hero p {
                font-size: 1.1rem;
            }

            .faq-categories {
                gap: 10px;
            }

            .category-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }

            .faq-category-section {
                padding: 30px 25px;
            }

            .category-title {
                font-size: 1.8rem;
            }

            .faq-question {
                padding: 20px 25px;
                font-size: 1.1rem;
            }

            .faq-answer.active {
                padding: 25px;
            }

            .contact-cta h2 {
                font-size: 2.2rem;
            }

            .contact-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .faq-hero h1 {
                font-size: 2rem;
            }

            .search-input {
                padding: 16px 20px;
            }

            .category-title {
                font-size: 1.6rem;
            }

            .faq-question {
                padding: 18px 20px;
                font-size: 1rem;
            }

            .contact-cta h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Simple header if header.php doesn't exist -->
    <?php include '../includes/header.php'; ?>

    <!-- FAQ Hero Section -->
    <section class="faq-hero">
        <div class="container">
            <div class="faq-hero-content fade-in">
                <h1>Frequently Asked Questions</h1>
                <p>Find answers to the most common questions about St. Philip Neri School. If you can't find what you're looking for, don't hesitate to contact us.</p>
                <div class="search-container">
                    <input type="text" class="search-input" id="faqSearch" placeholder="Search for questions or topics...">
                    <button class="search-btn" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Navigation -->
    <section class="faq-nav">
        <div class="container">
            <div class="faq-categories">
                <button class="category-btn active" data-category="all">All Questions</button>
                <button class="category-btn" data-category="admissions">Admissions</button>
                <button class="category-btn" data-category="academics">Academics</button>
                <button class="category-btn" data-category="facilities">Facilities</button>
                <button class="category-btn" data-category="tuition">Tuition & Fees</button>
                <button class="category-btn" data-category="extracurricular">Extracurricular</button>
            </div>
        </div>
    </section>

    <!-- FAQ Content -->
    <section class="faq-content">
        <div class="container">
            <?php if (!empty($faq_categories)): ?>
                <?php foreach ($faq_categories as $category): ?>
                    <div class="faq-category-section fade-in" data-category="<?php echo htmlspecialchars($category['slug']); ?>">
                        <h2 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h2>

                        <?php if (!empty($category['faqs'])): ?>
                            <?php foreach ($category['faqs'] as $index => $faq): ?>
                                <div class="faq-item stagger-item">
                                    <button class="faq-question">
                                        <?php echo htmlspecialchars($faq['question']); ?>
                                        <i class="fas fa-chevron-down faq-icon"></i>
                                    </button>
                                    <div class="faq-answer">
                                        <p><?php echo $faq['answer']; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No FAQs available for this category.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- No Results Message (Hidden by Default) -->
            <div class="no-results" id="noResults" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>No Results Found</h3>
                <p>We couldn't find any FAQs matching your search. Try different keywords or browse the categories above.</p>
            </div>
        </div>
    </section>

    <!-- Contact CTA Section -->
    <section class="contact-cta">
        <div class="container">
            <div class="contact-cta-content fade-in">
                <h2>Still Have Questions?</h2>
                <p>Our admissions team is here to help you with any additional questions you may have about St. Philip Neri School.</p>
                <div class="contact-buttons">
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-envelope"></i>
                        Contact Us
                    </a>
                    <a href="apply.php" class="btn btn-secondary">
                        <i class="fas fa-user-graduate"></i>
                        Schedule a Tour
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple footer if footer.php doesn't exist -->
    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ Accordion Functionality
            const faqQuestions = document.querySelectorAll('.faq-question');

            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;
                    const isActive = question.classList.contains('active');

                    // Close all other FAQs
                    faqQuestions.forEach(q => {
                        q.classList.remove('active');
                        q.nextElementSibling.classList.remove('active');
                    });

                    // Toggle current FAQ
                    if (!isActive) {
                        question.classList.add('active');
                        answer.classList.add('active');
                    }
                });
            });

            // Category Filtering
            const categoryButtons = document.querySelectorAll('.category-btn');
            const faqSections = document.querySelectorAll('.faq-category-section');

            categoryButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const category = button.getAttribute('data-category');

                    // Update active button
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    // Show/hide sections based on category
                    if (category === 'all') {
                        faqSections.forEach(section => {
                            section.style.display = 'block';
                        });
                        document.getElementById('noResults').style.display = 'none';
                    } else {
                        let hasVisibleSections = false;

                        faqSections.forEach(section => {
                            if (section.getAttribute('data-category') === category) {
                                section.style.display = 'block';
                                hasVisibleSections = true;
                            } else {
                                section.style.display = 'none';
                            }
                        });

                        // Show no results message if no sections are visible
                        if (!hasVisibleSections) {
                            document.getElementById('noResults').style.display = 'block';
                        } else {
                            document.getElementById('noResults').style.display = 'none';
                        }
                    }

                    // Close all FAQs when switching categories
                    faqQuestions.forEach(q => {
                        q.classList.remove('active');
                        q.nextElementSibling.classList.remove('active');
                    });
                });
            });

            // Search Functionality
            const searchInput = document.getElementById('faqSearch');
            const searchBtn = document.getElementById('searchBtn');

            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();

                if (searchTerm === '') {
                    // If search is empty, show all sections
                    faqSections.forEach(section => {
                        section.style.display = 'block';
                    });
                    document.getElementById('noResults').style.display = 'none';

                    // Reset category buttons
                    categoryButtons.forEach(btn => {
                        if (btn.getAttribute('data-category') === 'all') {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    });
                    return;
                }

                let hasResults = false;

                // Show all sections for searching
                faqSections.forEach(section => {
                    section.style.display = 'block';
                });

                faqSections.forEach(section => {
                    const questions = section.querySelectorAll('.faq-question');
                    let sectionHasResults = false;

                    questions.forEach(question => {
                        const questionText = question.textContent.toLowerCase();
                        const answer = question.nextElementSibling;
                        const answerText = answer.textContent.toLowerCase();

                        if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                            question.parentElement.style.display = 'block';
                            sectionHasResults = true;
                            hasResults = true;

                            // Highlight search term
                            if (questionText.includes(searchTerm)) {
                                const regex = new RegExp(searchTerm, 'gi');
                                question.innerHTML = question.textContent.replace(regex, match => `<span style="background-color: #fef3c7;">${match}</span>`) + '<i class="fas fa-chevron-down faq-icon"></i>';
                            }
                        } else {
                            question.parentElement.style.display = 'none';
                        }
                    });

                    // Show section if it has results, hide otherwise
                    section.style.display = sectionHasResults ? 'block' : 'none';
                });

                // Show no results message if no matches found
                document.getElementById('noResults').style.display = hasResults ? 'none' : 'block';

                // Update category buttons to show search is active
                categoryButtons.forEach(btn => btn.classList.remove('active'));
            }

            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            // Scroll animations
            const fadeElements = document.querySelectorAll('.fade-in, .stagger-item');

            const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            fadeElements.forEach(element => {
                fadeObserver.observe(element);
            });

            // Stagger animation for FAQ items
            const staggerItems = document.querySelectorAll('.stagger-item');
            staggerItems.forEach((item, index) => {
                item.style.transitionDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>

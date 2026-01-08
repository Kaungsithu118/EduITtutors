<?php

include("profilecalling.php");
include("admin/connect.php");

// Initialize variables for form persistence
$name = $email = $message = '';
$formMessage = '';
$formMessageType = ''; // 'success' or 'error'

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  // Sanitize input
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
  $status = 'unread';

  // Validate input
  if (empty($name) || empty($email) || empty($message)) {
    $formMessage = 'Please fill in all required fields.';
    $formMessageType = 'error';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $formMessage = 'Please enter a valid email address.';
    $formMessageType = 'error';
  } else {
    try {
      // Check if database connection is working
      if (!$pdo) {
        throw new Exception('Database connection failed');
      }

      // Prepare and execute SQL
      $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, status) VALUES (?, ?, ?, ?)");
      $result = $stmt->execute([$name, $email, $message, $status]);

      if ($result) {
        $formMessage = 'Thank you! Your message has been sent successfully.';
        $formMessageType = 'success';
        // Clear form on successful submission
        $name = $email = $message = '';
      } else {
        throw new Exception('Database insert failed');
      }
    } catch (Exception $e) {
      $formMessage = 'Error: Unable to send your message. Please try again later.';
      $formMessageType = 'error';
      error_log('Contact form error: ' . $e->getMessage());
    }
  }
}

// Get 3 most recent courses
$latestCoursesQuery = $pdo->query("
    SELECT 
        c.*, 
        d.Department_Name, 
        t.Teacher_Name, 
        curr.Curriculum_Description
    FROM courses c
    JOIN departments d ON c.Department_ID = d.Department_ID
    JOIN teachers t ON c.Teacher_ID = t.Teacher_ID
    JOIN curriculum curr ON c.Curriculum_ID = curr.Curriculum_ID
    ORDER BY c.Created_At DESC 
    LIMIT 3
");
$latestCourses = $latestCoursesQuery->fetchAll(PDO::FETCH_ASSOC);

// Get 3 upcoming webinars (closest to current date)
$currentDate = date('Y-m-d');
$upcomingWebinarsQuery = $pdo->query("
    SELECT * FROM webinars 
    WHERE webinar_date >= '$currentDate'
    ORDER BY webinar_date ASC 
    LIMIT 3
");
$upcomingWebinars = $upcomingWebinarsQuery->fetchAll(PDO::FETCH_ASSOC);

// Get 3 latest blog posts
$latestBlogsQuery = $pdo->query("
    SELECT * FROM blogs 
    ORDER BY updated_time DESC 
    LIMIT 5
");
$latestBlogs = $latestBlogsQuery->fetchAll(PDO::FETCH_ASSOC);

// Get head of departments (main teachers)
$headTeachersQuery = $pdo->query("
    SELECT * FROM teachers 
    WHERE Teacher_Role = 'main'
    ORDER BY Teacher_ID ASC
");
$headTeachers = $headTeachersQuery->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page</title>
  <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">

  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/cartslidebar.css">
  <link rel="stylesheet" href="css/search.css">
  <link rel="stylesheet" href="css/chatbot.css">
  <link rel="stylesheet" href="css/event_add.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
  <style>
    /* ========= MODAL CONTAINER ========== */
    #facilityModal .modal-dialog {
      transition: all 0.4s ease-in-out;
      transform: translateY(10px);
    }

    #facilityModal.show .modal-dialog {
      transform: translateY(0);
    }

    #facilityModal .modal-content {
      border-radius: 16px;
      background: linear-gradient(180deg, #ffffff, #f1f3f5);
      box-shadow: 0 30px 60px rgba(0, 0, 0, 0.25);
      border: none;
      padding: 0;
    }

    /* ========= MODAL HEADER ========== */
    #facilityModal .modal-header {
      background: linear-gradient(135deg, #007bff, #0056b3);
      color: #fff;
      padding: 1rem 2rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      align-items: center;
    }

    #facilityModal .modal-title {
      font-size: 1.7rem;
      font-weight: 700;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    #facilityModal .btn-close {
      filter: invert(100%);
      opacity: 1;
      width: 1.2rem;
      height: 1.2rem;
      transition: background-color 0.2s ease;
    }

    #facilityModal .btn-close:hover {
      background-color: rgba(255, 255, 255, 0.3);
    }

    /* ========= MODAL BODY ========== */
    #facilityModal .modal-body {
      padding: 1.5rem 1.5rem;
      background: #f9fafb;
      max-height: 70vh;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #ced4da #e9ecef;
    }

    #facilityModal .modal-body::-webkit-scrollbar {
      width: 8px;
    }

    #facilityModal .modal-body::-webkit-scrollbar-thumb {
      background: #adb5bd;
      border-radius: 8px;
    }

    #facilityModal .modal-body::-webkit-scrollbar-track {
      background: #dee2e6;
    }

    /* ========= FACILITY CONTENT ========== */
    .facility-content {
      background: #fff;
      border-radius: 14px;
      padding: 1.25rem 1.25rem 1.75rem;
      margin-bottom: 1.75rem;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
      border-left: 5px solid #0d6efd;
      transition: all 0.3s ease;
    }

    .facility-content:hover {
      border-left-color: #6610f2;
    }

    .facility-content h3 {
      font-size: 1.4rem;
      font-weight: 600;
      color: #0d6efd;
      margin-bottom: 0.6rem;
    }

    .facility-content h3::after {
      content: '';
      display: block;
      width: 40px;
      height: 3px;
      background: #0d6efd;
      border-radius: 2px;
      margin-top: 0.4rem;
    }

    .facility-content p {
      font-size: 1rem;
      color: #495057;
      line-height: 1.6;
      margin-bottom: 0.8rem;
    }

    /* Bullet List Styling */
    .facility-content ul {
      list-style: none;
      padding-left: 0;
      color: #343a40;
    }

    .facility-content ul li {
      position: relative;
      padding-left: 1.6rem;
      margin-bottom: 0.5rem;
      font-size: 1rem;
    }

    .facility-content ul li::before {
      content: "\f00c";
      font-family: 'Font Awesome 5 Free';
      font-weight: 900;
      position: absolute;
      left: 0;
      top: 0.1rem;
      color: #28a745;
      font-size: 1rem;
    }

    /* Image Styling */
    .facility-content img {
      width: 100%;
      border-radius: 10px;
      object-fit: cover;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }

    .facility-content img:hover {
      transform: scale(1.03);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    /* Reduce image row spacing */
    .facility-content .row .col-md-6.mb-3 {
      margin-bottom: 1rem !important;
    }

    /* ========= MODAL FOOTER ========== */
    #facilityModal .modal-footer {
      padding: 1rem 1.5rem;
      background: #f1f3f5;
      border-top: 1px solid #dee2e6;
      display: flex;
      justify-content: flex-end;
      gap: 0.75rem;
    }

    #facilityModal .modal-footer .btn {
      padding: 0.55rem 1.3rem;
      font-size: 0.95rem;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.2s ease;
    }

    #facilityModal .modal-footer .btn-secondary {
      background-color: #6c757d;
      color: white;
      border: none;
    }

    #facilityModal .modal-footer .btn-secondary:hover {
      background-color: #5a6268;
    }

    /* ========= RESPONSIVE ========== */
    @media (max-width: 576px) {
      #facilityModal .modal-body {
        padding: 1rem;
      }

      .facility-content {
        padding: 1rem;
      }

      .facility-content h3 {
        font-size: 1.3rem;
      }

      .facility-content p,
      .facility-content ul li {
        font-size: 0.95rem;
      }
    }
  </style>
  <style>
    /* Form message styling */
    .form-message {
      padding: 12px;
      margin: 15px 0;
      border-radius: 4px;
      display: none;
      font-weight: bold;
    }

    .form-message.success {
      background-color: #d4edda;
      color: #155724;
      display: block;
    }

    .form-message.error {
      background-color: #f8d7da;
      color: #721c24;
      display: block;
    }

    .counter_form_content input,
    .counter_form_content textarea {
      margin-bottom: 15px;
      width: 100%;
      padding: 10px;
    }

    .counter_form_content textarea {
      min-height: 120px;
    }

    #contact-form {
      width: 100%;
      max-width: 500px;
      margin: 0 auto;
    }
  </style>
</head>

<body>
  <?php
  include("header.php");
  ?>

  <section id="slider">
    <div class="swiper slider position-relative">
      <div class="swiper-wrapper">
        <div class="swiper-slide d-flex"
          style="background-image: url(photo/Home/Home\ 1.jpg); background-size: cover; background-repeat: no-repeat; height: 90vh; background-position: center; height: 90vh;">
          <div class="banner-content text-center m-auto">
            <h2 class="text-white display-1 fw-bolder lh-1">Build Your Future Success</h2>
          </div>
        </div>
        <div class="swiper-slide d-flex"
          style="background-image: url(photo/Home/Home\ 2.jpg); background-size: cover; background-repeat: no-repeat; height: 90vh; background-position: center; height: 90vh;">
          <div class="banner-content text-center m-auto">
            <h2 class="text-white display-1 fw-bolder lh-1">Build Your Future Success</h2>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="z-1">
    <div class="container ">
      <h2 class="display-5 d-block d-lg-none text-center fw-semibold">Our Features</h2>

      <div class="row g-lg-0 mt-5 mt-lg-0">
        <div class="col-md-4 text-center">
          <img src="photo/Home/Feature 1.jpg" class="img-fluid" alt="img">
          <a href="#">
            <p class="text-white py-3 m-0" style="background-color: rgb(8, 35, 145);">Course Curriculum</p>
          </a>
        </div>
        <div class="col-md-4 text-center">
          <img src="photo/Home/Feature 2.jpg" class="img-fluid" alt="img">
          <a href="#">
            <p class="text-white py-3 m-0" style="background-color: rgb(0, 15, 73);">Knowledge for students</p>
          </a>
        </div>
        <div class="col-md-4 text-center">
          <img src="photo/Home/Feature 3.jpg" class="img-fluid" alt="img">
          <a href="#">
            <p class="text-white py-3 m-0" style="background-color: rgb(14, 52, 202);">Supportive Tutors</p>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="padding-medium mt-xl-5 pt-5 pb-5">
    <div class="container">
      <div class="row align-items-center mt-xl-5">
        <div class="offset-md-1 col-md-5">
          <img src="photo/Home/About.jpg" alt="img" class="img-fluid rounded-circle" style="width: 420px;">
        </div>
        <div class="col-md-5 mt-5 mt-md-0">
          <div class="mb-3">
            <p class="text-secondary ">Learn more about us</p>
            <h2 class="display-6 fw-semibold">About Us</h2>
          </div>
          <p>Welcome to EduITtutors, your trusted platform for online IT tutoring and skill development.
            We specialize in delivering high-quality, flexible, and affordable courses designed to empower learners
            in the ever-evolving field of Information Technology.!</p>
          <div class="d-flex mt-3">
            <i class="fa-solid fa-circle-check fa-2x" style="color: rgb(0, 15, 73);"></i>
            <p class="ps-4">EduITtutors is all about accessible learning, skilled tutoring, and career growth.</p>
          </div>
          <div class="d-flex mt-3">
            <i class="fa-solid fa-circle-check fa-2x" style="color: rgb(0, 15, 73);"></i>
            <p class="ps-4">Tailored IT tutoring, Real-world projects, Supportive mentorship that everything you need to build your skills and confidence.</p>
          </div>
          <div class="d-flex mt-3">
            <i class="fa-solid fa-circle-check fa-2x" style="color: rgb(0, 15, 73);"></i>
            <p class="ps-4">Learn at your own pace, on your own schedule, and get the support you need to thrive in today's digital world.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section 2 -->
  <section id="about" class="bg-background position-relative pt-5 pb-5">
    <div class="container">
      <div class="row align-items-center g-lg-5">
        <div class="col-md-6">
          <div class="feature d-flex">
            <div class="me-4">
              <i class="fa-solid fa-circle-check fa-5x" style="color: rgb(0, 15, 73);"></i>
            </div>
            <div>
              <h6>Specialized IT Programs</h6>
              <p class="mt-3">
                Explore our industry-focused IT programs designed to boost your career in tech. From coding bootcamps to
                data analytics, we offer practical courses that equip learners with real-world skills.
              </p>
            </div>
          </div>
          <div class="feature d-flex mt-5">
            <div class="me-4">
              <i class="fa-solid fa-circle-check fa-5x" style="color: rgb(0, 15, 73);"></i>
            </div>
            <div>
              <h6>Workshops & Webinars</h6>
              <p class="mt-3">
                Stay up-to-date with the latest in tech through our live events. Join expert-led webinars,
                coding challenges, and community workshops to sharpen your skills and network with IT professionals.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 mt-5">
          <div class="p-5" style="background-color: rgb(0, 15, 73);;">
            <h4 class="text-white mt-3">Welcome</h4>
            <p class="text-white my-4">Welcome to our IT tutoring platform — where innovation meets education.
              Whether you're starting out or upskilling, our courses are tailored to guide you through programming,
              networking, cybersecurity, and more. Join a learning experience that's hands-on, future-focused,
              and designed for the digital age.</p>
            <a href="webinar.php" class="btn join btn-light py-md-3 px-md-4 animated">Join Now</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section 3 -->
  <section id="feature">
    <div class="features">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section_title_container text-center">
              <h2 class="section_title">Welcome To EduITtutors</h2>
              <div class="section_subtitle mt-4">
                <p>
                  EduITtutors is your go-to platform for learning IT programs and courses. Whether you're new to tech or looking to upgrade your skills, we offer expert-led tutorials, hands-on projects, and tailored learning paths to help you grow in today's digital world.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="row features_row">
          <!-- Features Item -->
          <div class="col-lg-3 feature_col">
            <div class="feature text-center trans_400">
              <div class="feature_icon"><img src="photo/logo/icon_1.png" alt=""></div>
              <h3 class="feature_title">Industry Experts</h3>
              <div class="feature_text">
                <p>Learn from experienced IT professionals and certified instructors who bring real-world insights to every lesson.</p>
              </div>
            </div>
          </div>

          <!-- Features Item -->
          <div class="col-lg-3 feature_col">
            <div class="feature text-center trans_400">
              <div class="feature_icon"><img src="photo/logo/icon_2.png" alt=""></div>
              <h3 class="feature_title">Digital Resources</h3>
              <div class="feature_text">
                <p>Access a wide range of digital books, coding resources, and project files to support your learning journey.</p>
              </div>
            </div>
          </div>

          <!-- Features Item -->
          <div class="col-lg-3 feature_col">
            <div class="feature text-center trans_400">
              <div class="feature_icon"><img src="photo/logo/icon_3.png" alt=""></div>
              <h3 class="feature_title">Top-Rated Courses</h3>
              <div class="feature_text">
                <p>Master topics like programming, networking, databases, and cybersecurity with structured, beginner-friendly courses.</p>
              </div>
            </div>
          </div>

          <!-- Features Item -->
          <div class="col-lg-3 feature_col">
            <div class="feature text-center trans_400">
              <div class="feature_icon"><img src="photo/logo/icon_4.png" alt=""></div>
              <h3 class="feature_title">Achievements</h3>
              <div class="feature_text">
                <p>Earn digital certificates and badges to showcase your progress and accomplishments in IT skills.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Founder Section -->
  <section id="founder" class="padding-medium position-relative">
    <div class="container">
      <div class="row align-items-center">

        <div class="col-md-4 offset-md-2">
          <div class="imageblock me-4">
            <img class="img-fluid" src="photo/home/Founder.jpg" alt="img">
          </div>
        </div>
        <div class="col-md-4 mt-5">
          <p class="fs-2 lh-base">At EduITtutors, we prepare students with the digital skills, mindset, and confidence needed to succeed in the tech-driven world.</p>
          <div class="d-flex justify-content-between">
            <div class="mt-3">
              <p class="fw-bold m-0">Patricia Smith</p>
              <p>Principal</p>
            </div>
            <div class="mt-5">
              <img src="photo/Home/handwritten-email-signature.webp" alt="img" class="img-fluid" style="width: 200px;">
            </div>
          </div>
        </div>
      </div>
    </div>
    <img class="position-absolute top-50 end-0 translate-middle-y z-n1 img-fluid" src="photo/Home/pattern1.png" alt="img">
  </section>

  <!-- Counter Section -->
  <div class="counter">
    <div class="counter_background" style="background-image:url(photo/vecteezy_abstract-technology-background-hexagon-with-truncated-lines_.jpg)"></div>
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="counter_content">
            <h2 class="counter_title">Our Welcome</h2>
            <div class="counter_text">
              <p>Join thousands of learners on EduITtutors — your trusted platform for high-quality IT online courses. Whether you're a beginner or a professional, our expert-led programs will guide you to success in the tech industry.</p>
            </div>

            <!-- Milestones -->
            <div class="milestones d-flex flex-md-row flex-column align-items-center justify-content-between">
              <!-- Milestone -->
              <div class="milestone">
                <div class="milestone_counter" data-end-value="15">0</div>
                <div class="milestone_text">Experience</div>
              </div>

              <!-- Milestone -->
              <div class="milestone">
                <div class="milestone_counter" data-end-value="120" data-sign-after="k">0</div>
                <div class="milestone_text">Learners</div>
              </div>

              <!-- Milestone -->
              <div class="milestone">
                <div class="milestone_counter" data-end-value="670" data-sign-after="+">0</div>
                <div class="milestone_text">Courses</div>
              </div>

              <!-- Milestone -->
              <div class="milestone">
                <div class="milestone_counter" data-end-value="320">0</div>
                <div class="milestone_text">Instructors</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
        <div class="counter_form">
          <div class="row fill_height">
            <div class="col fill_height">
              <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#contact-form" class="counter_form_content d-flex flex-column align-items-center justify-content-center" id="contact-form">
                <div class="counter_form_title pt-4 mt-1">Contact Us</div>

                <?php if (!empty($formMessage)): ?>
                  <div class="form-message <?php echo $formMessageType; ?>" style="margin-top: -25px;">
                    <?php echo $formMessage; ?>
                  </div>
                <?php endif; ?>

                <input type="text" class="counter_input" name="name" placeholder="Your Name:" required value="<?php echo htmlspecialchars($name); ?>">
                <input type="email" class="counter_input" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
                <textarea class="counter_input counter_text_input" name="message" placeholder="Message:" required><?php echo htmlspecialchars($message); ?></textarea>
                <button type="submit" name="submit" class="counter_form_button">Submit Now</button>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Latest Courses Section -->
  <section class="position-relative">
    <div class="courses">
      <div class="section_background" style="background-image: url('photo/Home/courses_background.jpg'); background-size: cover; background-position: center;"></div>

      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section_title_container text-center">
              <h2 class="section_title">Our Latest Learning Courses</h2>
              <div class="section_subtitle mt-3">
                <p>Discover top-rated IT courses designed to boost your career. At EduITtutors, we provide practical, hands-on training in software development, networking, cloud computing, and more — all from the comfort of your home.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row courses_row">
          <?php foreach ($latestCourses as $course):
            $shortIntro = strip_tags($course['Curriculum_Description']) . '...'; // Full clean text
          ?>
            <div class="col-lg-4 course_col">
              <div class="course fixed-card">
                <div class="course_image">
                  <img src="admin/<?= htmlspecialchars($course['Course_Photo']) ?>" alt="<?= htmlspecialchars($course['Course_Name']) ?>" class="fixed-image">
                </div>
                <div class="course_body">
                  <h3 class="course_title one-line">
                    <a href="course_detail.php?id=<?= $course['Course_ID'] ?>"><?= htmlspecialchars($course['Course_Name']) ?></a>
                  </h3>
                  <div class="course_teacher my-3 fw-bold"><a href="teacherdetail.php?id=<?= $course['Teacher_ID'] ?>"><?= htmlspecialchars($course['Teacher_Name']) ?></a></div>
                  <div class="course_text three-lines">
                    <p><?= htmlspecialchars($shortIntro) ?></p>
                  </div>
                </div>
                <div class="course_footer">
                  <div class="course_footer_content d-flex flex-row align-items-center justify-content-start">
                    <div class="course_info">
                      <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                      <span class="mx-3"><?= htmlspecialchars($course['Department_Name']) ?></span>
                    </div>
                    <div class="course_price ml-auto">$<?= number_format($course['Course_Fees'], 2) ?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="row">
          <div class="text-center mt-5">
            <a href="course.php" class="btn px-5 py-3 more_course">View all courses</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Head of Department Section -->
  <section id="teacher" class="pt-5">
    <div class="container">
      <div class="text-center mb-5">
        <p class="text-secondary">Meet our Course Facilitators</p>
        <h2 class="display-6 fw-semibold">Head Mentors</h2>
      </div>

      <div class="d-flex flex-wrap justify-content-center gap-3">
        <?php foreach ($headTeachers as $teacher): ?>
          <!-- Mentor Card -->
          <div class="mentor-card mb-5">
            <div class="team-member position-relative card rounded-4 border-0 shadow-lg p-3 text-center">
              <div class="image-holder zoom-effect rounded-3">
                <img src="admin/<?= htmlspecialchars($teacher['Teacher_Photo']) ?>" class="img-fluid rounded-3" alt="<?= htmlspecialchars($teacher['Teacher_Name']) ?>" style="width: 100%; height: 100%;">
                <ul class="social-links list-unstyled position-absolute">
                  <li><a href="<?= htmlspecialchars($teacher['Facebook_Link']) ?>">
                      <div class="text-white text-center" style="background-color: #14bdee; border-radius: 25%;"><i class="fa-brands fa-facebook"></i></div>
                    </a></li>
                  <li><a href="<?= htmlspecialchars($teacher['Twitter_Link']) ?>">
                      <div class="text-white text-center" style="background-color: #14bdee; border-radius: 25%;"><i class="fa-brands fa-square-x-twitter"></i></div>
                    </a></li>
                  <li><a href="<?= htmlspecialchars($teacher['LinkedIn_Link']) ?>">
                      <div class="text-white text-center" style="background-color: #14bdee; border-radius: 25%;"><i class="fa-brands fa-linkedin"></i></div>
                    </a></li>
                  <li><a href="<?= htmlspecialchars($teacher['Instagram_Link']) ?>">
                      <div class="text-white text-center" style="background-color: #14bdee; border-radius: 25%;"><i class="fa-brands fa-instagram"></i></div>
                    </a></li>
                </ul>
              </div>
              <div class="card-body p-0">
                <div class="text-center mt-3">
                  <a href="teacherdetail.php?id=<?= $teacher['Teacher_ID'] ?>">
                    <p class="fw-bold m-0 text-dark"><?= htmlspecialchars($teacher['Teacher_Name']) ?></p>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <img class="position-absolute top-50 end-0 translate-middle-y z-n1 img-fluid" src="photo/Home/pattern3.png" alt="img">
  </section>

  <!-- Facilities Section -->
  <section id="facilities" class="position-relative padding-medium">
    <div class="container facility-block z-1">
      <div class="text-start p-5" style="background: rgb(0, 15, 73); color: white;">
        <h2 class="display-5 fw-semibold text-white mb-4">Our Facilities</h2>
        <p class="text-white">We provide all the essential digital tools and support needed for a successful online learning experience. From rich libraries to dedicated support, our platform is built for your success.</p>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row g-3 mt-3 mt-lg-0">
        <!-- Facility: Library -->
        <div class="col-md-6 text-center">
          <div class="product-item position-relative bg-black overflow-hidden">
            <img src="photo/facilities/Library.jpg" class="post-image img-fluid" alt="Library" style="width: 100%; height: 100%;">
            <div class="product-description position-absolute top-50 start-50 translate-middle p-3">
              <h3 class="mb-2 display-6 text-white">Library</h3>
              <p class="product-paragraph d-none d-lg-block m-0 text-white">Access thousands of eBooks, journals, and digital resources anytime.</p>
              <a href="#" class="more-info-btn" data-bs-toggle="modal" data-bs-target="#facilityModal" data-facility="library">
                <p class="text-decoration-underline text-white m-0 mt-2">More info</p>
              </a>
            </div>
          </div>
        </div>

        <!-- Facility: Resources -->
        <div class="col-md-6 text-center">
          <div class="product-item position-relative bg-black overflow-hidden">
            <img src="photo/facilities/Resources.jpg" class="post-image img-fluid" alt="Resources" style="width: 100%; height: 100%;">
            <div class="product-description position-absolute top-50 start-50 translate-middle p-3">
              <h3 class="mb-2 display-6 text-white">Resources</h3>
              <p class="product-paragraph d-none d-lg-block m-0 text-white">Get downloadable materials, practice exercises, and video lessons.</p>
              <a href="#" class="more-info-btn" data-bs-toggle="modal" data-bs-target="#facilityModal" data-facility="resources">
                <p class="text-decoration-underline text-white m-0 mt-2">More info</p>
              </a>
            </div>
          </div>
        </div>

        <!-- Facility: Support -->
        <div class="col-md-6 text-center">
          <div class="product-item position-relative bg-black overflow-hidden">
            <img src="photo/facilities/Support.jpg" class="post-image img-fluid" alt="Support" style="width: 100%; height: 100%;">
            <div class="product-description position-absolute top-50 start-50 translate-middle p-3">
              <h3 class="mb-2 display-6 text-white">Support</h3>
              <p class="product-paragraph d-none d-lg-block m-0 text-white">24/7 assistance from instructors and technical staff.</p>
              <a href="#" class="more-info-btn" data-bs-toggle="modal" data-bs-target="#facilityModal" data-facility="support">
                <p class="text-decoration-underline text-white m-0 mt-2">More info</p>
              </a>
            </div>
          </div>
        </div>

        <!-- Facility: Dashboard -->
        <div class="col-md-6 text-center">
          <div class="product-item position-relative bg-black overflow-hidden">
            <img src="photo/facilities/Dashboard.jpg" class="post-image img-fluid" alt="Dashboard" style="width: 100%; height: 100%;">
            <div class="product-description position-absolute top-50 start-50 translate-middle p-3">
              <h3 class="mb-2 display-6 text-white">Dashboard</h3>
              <p class="product-paragraph d-none d-lg-block m-0 text-white">Track your course progress, deadlines, and certifications in one place.</p>
              <a href="#" class="more-info-btn" data-bs-toggle="modal" data-bs-target="#facilityModal" data-facility="dashboard">
                <p class="text-decoration-underline text-white m-0 mt-2">More info</p>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Facility Modal -->
  <div class="modal fade" id="facilityModal" tabindex="-1" aria-labelledby="facilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h2 class="modal-title fs-3 text-white" id="facilityModalLabel">Facility Details</h2>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div id="library-content" class="facility-content">
            <h3 class="mb-3">Library</h3>
            <p>Our digital library contains over 50,000 eBooks, academic journals, and research papers across all disciplines. Features include:</p>
            <ul class="mb-4">
              <li>24/7 access from any device</li>
              <li>Advanced search functionality</li>
              <li>Citation tools and reference management</li>
              <li>Personalized reading lists</li>
            </ul>
            <div class="row">
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Library 1.jpg" class="img-fluid rounded" alt="Library Interface">
              </div>
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Library 2.jpg" class="img-fluid rounded" alt="Library Collections">
              </div>
            </div>
          </div>

          <div id="resources-content" class="facility-content d-none">
            <h3 class="mb-3">Learning Resources</h3>
            <p>Our comprehensive resource center offers:</p>
            <ul class="mb-4">
              <li>Interactive learning modules</li>
              <li>Downloadable worksheets and templates</li>
              <li>Video tutorials from expert instructors</li>
              <li>Practice exams with instant feedback</li>
              <li>Case studies and real-world examples</li>
            </ul>
            <div class="row">
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Resources 1.jpg" class="img-fluid rounded" alt="Learning Materials">
              </div>
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Resources 2.jpg" class="img-fluid rounded" alt="Video Lessons">
              </div>
            </div>
          </div>

          <div id="support-content" class="facility-content d-none">
            <h3 class="mb-3">Student Support Services</h3>
            <p>We offer comprehensive support to ensure your learning journey is smooth:</p>
            <ul class="mb-4">
              <li>24/7 live chat with academic advisors</li>
              <li>Dedicated technical support team</li>
              <li>Weekly virtual office hours with instructors</li>
              <li>Peer mentoring program</li>
              <li>Career counseling services</li>
            </ul>
            <div class="row">
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Support 1.jpg" class="img-fluid rounded" alt="Support Team">
              </div>
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Support 2.jpg" class="img-fluid rounded" alt="Online Help">
              </div>
            </div>
          </div>

          <div id="dashboard-content" class="facility-content d-none">
            <h3 class="mb-3">Student Dashboard Features</h3>
            <p>Your personalized dashboard provides:</p>
            <ul class="mb-4">
              <li>Real-time progress tracking</li>
              <li>Customizable calendar with deadlines</li>
              <li>Performance analytics and insights</li>
              <li>Certificate and achievement tracking</li>
              <li>Personalized recommendations</li>
            </ul>
            <div class="row">
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Dashboard 1.jpg" class="img-fluid rounded" alt="Dashboard Overview">
              </div>
              <div class="col-md-6 mb-3">
                <img src="photo/facilities/Dashboard 2.jpg" class="img-fluid rounded" alt="Progress Tracking">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming Webinars Section -->
  <section id="events">
    <div class="events">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section_title_container text-center">
              <h2 class="section_title mb-4 pb-2">Upcoming Webinars</h2>
              <div class="section_subtitle">
                <p>Stay updated with the latest online events in the IT world — from AI webinars to cloud computing workshops, learn from industry experts and boost your skills!</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row events_row">
          <?php foreach ($upcomingWebinars as $webinar):
            $webinarDate = new DateTime($webinar['webinar_date']);
            $day = $webinarDate->format('d');
            $month = $webinarDate->format('M');
          ?>
            <!-- Event -->
            <div class="col-lg-4 event_col">
              <div class="event event_left">
                <div class="event_image"><img src="admin/<?= htmlspecialchars($webinar['banner_image']) ?>" alt="<?= htmlspecialchars($webinar['title']) ?>" style="width: 100%; height: 100%;"></div>
                <div class="event_body d-flex flex-row align-items-start justify-content-start">
                  <div class="event_date">
                    <div class="d-flex flex-column align-items-center justify-content-center trans_200">
                      <div class="event_day trans_200"><?= $day ?></div>
                      <div class="event_month trans_200"><?= $month ?></div>
                    </div>
                  </div>
                  <div class="event_content">
                    <div class="event_title"><a href="webinar_detail.php?id=<?= $webinar['webinar_id'] ?>"><?= htmlspecialchars($webinar['title']) ?></a></div>
                    <div class="event_info_container">
                      <div class="event_info"><i class="fa-solid fa-clock" aria-hidden="true"></i><span><?= htmlspecialchars($webinar['time_schedule']) ?></span></div>
                      <div class="event_info"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span><?= htmlspecialchars($webinar['location']) ?></span></div>
                      <div class="event_text">
                        <p><?= substr(htmlspecialchars($webinar['intro_text']), 0, 100) ?>...</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Latest News/Blogs Section -->
  <section id="news">
    <div class="news">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section_title_container text-center">
              <h2 class="section_title mb-4">Latest Blogs</h2>
              <div class="section_subtitle">
                <p>Catch up on the most recent updates in the world of technology, including AI breakthroughs, cybersecurity trends, and the future of online learning in IT education.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row news_row">
          <div class="col-lg-7 news_col">
            <?php if (!empty($latestBlogs)):
              $mainBlog = $latestBlogs[0];
              $blogDate = new DateTime($mainBlog['updated_time']);
            ?>
              <!-- News Post Large -->
              <div class="news_post_large_container">
                <div class="news_post_large">
                  <div class="news_post_image"><img src="admin/<?= htmlspecialchars($mainBlog['blog_photo']) ?>" alt="<?= htmlspecialchars($mainBlog['title']) ?>"></div>
                  <div class="news_post_large_title"><a href="blog_detail.php?id=<?= $mainBlog['blog_id'] ?>"><?= htmlspecialchars($mainBlog['title']) ?></a></div>
                  <div class="news_post_meta">
                    <ul style="margin-left: -32px;">
                      <li><?= htmlspecialchars($mainBlog['writer']) ?></li>
                      <li><?= $blogDate->format('F j, Y') ?></li>
                    </ul>
                  </div>
                  <div class="news_post_text">
                    <p><?= substr(htmlspecialchars(strip_tags($mainBlog['intro_paragraph'])), 0, 150) ?>...</p>
                  </div>
                  <div class="news_post_link"><a href="blog_detail.php?id=<?= $mainBlog['blog_id'] ?>">read more</a></div>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="col-lg-5 news_col">
            <div class="news_posts_small">
              <?php for ($i = 1; $i < count($latestBlogs); $i++):
                $blog = $latestBlogs[$i];
                $blogDate = new DateTime($blog['updated_time']);
              ?>
                <!-- News Post Small -->
                <div class="news_post_small">
                  <div class="news_post_small_title"><a href="blog_detail.php?id=<?= $blog['blog_id'] ?>"><?= htmlspecialchars($blog['title']) ?></a></div>
                  <div class="news_post_meta">
                    <ul style="margin-left: -32px;">
                      <li><?= htmlspecialchars($blog['writer']) ?></li>
                      <li><?= $blogDate->format('F j, Y') ?></li>
                    </ul>
                  </div>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php
  include("footer.php");
  ?>
  <?php
  include("chatbot.php");
  ?>
  <?php
  include("event_pop.php");
  ?>







  <!-- External Libraries First -->
  <!-- JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

  <!-- Contact Form JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const contactForm = document.getElementById('contact-form');

      // Debugging: Log form submission
      contactForm.addEventListener('submit', function() {
        console.log('Form submitted');
      });

      // Scroll to form if there's a message
      <?php if (!empty($formMessage)): ?>
        setTimeout(function() {
          document.getElementById('contact-form').scrollIntoView({
            behavior: 'smooth'
          });
        }, 100);
      <?php endif; ?>

      // Optional: AJAX form submission
      /*
      contactForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(this);
          
          fetch('<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>', {
              method: 'POST',
              body: formData
          })
          .then(response => response.text())
          .then(data => {
              // Handle response
              console.log(data);
          })
          .catch(error => {
              console.error('Error:', error);
          });
      });
      */
    });
  </script>

  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

  <!-- Set PHP session data into JS -->
  <script>
    // Set the user ID from PHP session
    window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
  </script>

  <!-- Local Scripts -->
  <script src="js/cart.js"></script>
  <script src="js/custom.js"></script>
  <script src="js/search.js"></script>
  <script type="text/javascript" src="js/plugins.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
  <script src="js/home.js"></script>

  <!-- JavaScript to handle the click events -->
  <script src="js/home.js"></script>
  <script src="js/chatbot.js"></script>
  <script src="js/event_ad.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const facilityModal = document.getElementById('facilityModal');

      facilityModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const facility = button.getAttribute('data-facility');
        const modalTitle = facilityModal.querySelector('.modal-title');
        const allContents = facilityModal.querySelectorAll('.facility-content');

        // Hide all content first
        allContents.forEach(content => content.classList.add('d-none'));

        // Show the selected content
        const activeContent = document.getElementById(`${facility}-content`);
        if (activeContent) {
          activeContent.classList.remove('d-none');

          // Update modal title based on facility
          const facilityName = activeContent.querySelector('h3').textContent;
          modalTitle.textContent = facilityName;
        }
      });
    });
  </script>
</body>

</html>
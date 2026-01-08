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

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Page</title>
  <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/about.css">
  <link rel="stylesheet" href="css/about_responsive.css">
  <link rel="stylesheet" href="css/cartslidebar.css">
  <link rel="stylesheet" href="css/search.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/chatbot.css">



  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

  <!-- Fancybox CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
  <!-- Font Awesome for play icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
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
<style>


</style>

<body>

  <?php
  include("header.php");
  ?>

  <div class="home">
    <div class="breadcrumbs_container">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="breadcrumbs mb-3">
              <ul>
                <li><a href="index.php">Home</a></li>
                <li style="color: #384158; font-size: 16px; font-weight: 700;">About</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- About -->

  <div class="about">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="section_title_container text-center">
            <h2 class="section_title fs-1 mb-5">Welcome To EduITtutors</h2>
            <div class="section_subtitle mt-3">
              <p>EduITtutors is your trusted platform for accessible, high-quality IT education. We offer practical, industry-relevant online courses to help learners gain real-world tech skills.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row about_row">

        <!-- About Item -->
        <div class="col-lg-4 about_col about_col_left">
          <div class="about_item">
            <div class="about_item_image"><img src="photo/About/About 1.jpg" alt="" style="width: 100%; height: 300px;"></div>
            <div class="about_item_title"><a href="#">Our Story</a></div>
            <div class="about_item_text">
              <p>Founded by passionate IT, EduITtutors was born out of a desire to make tech education more inclusive. We believe anyone, anywhere, should be able to learn and grow in the IT field.</p>
            </div>
          </div>
        </div>

        <!-- About Item -->
        <div class="col-lg-4 about_col about_col_middle">
          <div class="about_item">
            <div class="about_item_image"><img src="photo/About/About 2.jpg" alt="" style="width: 100%; height: 300px;"></div>
            <div class="about_item_title"><a href="#">Our Mission</a></div>
            <div class="about_item_text">
              <p>Our mission is to provide affordable, engaging, and up-to-date IT courses that empower learners to succeed in today’s digital world — whether they're beginners or aspiring professionals.</p>
            </div>
          </div>
        </div>

        <!-- About Item -->
        <div class="col-lg-4 about_col about_col_right">
          <div class="about_item">
            <div class="about_item_image"><img src="photo/About/About 3.jpg" alt="" style="width: 100%; height: 300px;"></div>
            <div class="about_item_title"><a href="#">Our Vision</a></div>
            <div class="about_item_text">
              <p>We envision a future where IT education is borderless and everyone has the tools to innovate, solve problems, and lead in the tech-driven world through continuous learning.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>







  <div class="feature">
    <div class="feature_background" style="background-image:url(photo/About/plain-white-abstract-hd-wallpaper-peakpx-40-off.jpg); width: 100%;"></div>
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="section_title_container text-center">
            <h2 class="section_title">Why Choose EduITtutors</h2>
            <div class="section_subtitle mt-4">
              <p>At EduITtutors, we believe in practical, personalized, and industry-driven IT learning. Whether you're preparing for your first job or enhancing your skills, we’ve got you covered.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row feature_row">

        <!-- Feature Content -->
        <div class="col-lg-6 feature_col">
          <div class="feature_content">
            <!-- Accordions -->
            <div class="accordions">
              <div class="elements_accordions">

                <div class="accordion_container">
                  <div class="accordion d-flex flex-row align-items-center">
                    <div>Recognized by IT Professionals</div>
                  </div>
                  <div class="accordion_panel">
                    <p>Our courses are reviewed and recommended by working IT professionals, making them highly relevant and practical for today's tech demands.</p>
                  </div>
                </div>

                <div class="accordion_container">
                  <div class="accordion d-flex flex-row align-items-center active">
                    <div>Learn From Industry Experts</div>
                  </div>
                  <div class="accordion_panel">
                    <p>We work with experienced developers, engineers, and tech educators who bring real-world knowledge into the virtual classroom.</p>
                  </div>
                </div>

                <div class="accordion_container">
                  <div class="accordion d-flex flex-row align-items-center">
                    <div>Globally Accessible Certifications</div>
                  </div>
                  <div class="accordion_panel">
                    <p>Complete your training and receive certificates that demonstrate your skills to employers and institutions worldwide.</p>
                  </div>
                </div>

                <div class="accordion_container">
                  <div class="accordion d-flex flex-row align-items-center">
                    <div>Empowering You for Global Careers</div>
                  </div>
                  <div class="accordion_panel">
                    <p>Our flexible learning pathways prepare you for global opportunities, remote jobs, and freelance tech careers across industries.</p>
                  </div>
                </div>

              </div>
            </div>
            <!-- Accordions End -->
          </div>
        </div>

        <!-- Feature Video -->
        <div class="col-lg-6 feature_col">
          <div class="feature_video d-flex flex-column align-items-center justify-content-center">
            <div class="feature_video_background" style="background-image:url(photo/About/vd.jpg)"></div>
            <a data-fancybox href="https://youtu.be/VgmFPpkyVgU?si=JfhGjJay_s6rmCWb"
              class="feature_video_button">
              <img src="photo/logo/play.png" alt="">
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>




  <div class="team">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="section_title_container text-center">
            <h2 class="section_title mb-4">Meet Our Expert Tutors</h2>
            <div class="section_subtitle">
              <p>Our tutors are more than instructors — they are IT professionals, developers, and innovators passionate about helping you succeed.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row team_row mt-5">

        <?php
        // Function to fetch the top REGULAR teacher in a given category
        function getTopRegularTeacher($pdo, $category)
        {
          $column = $category . '_Percent';
          $query = "SELECT 
                    Teacher_ID, 
                    Teacher_Name, 
                    Teacher_Photo, 
                    $column AS Percent,
                    Expertise_Text,
                    Facebook_Link,
                    Twitter_Link,
                    Instagram_Link
                FROM teachers
                WHERE Teacher_Role = 'regular' 
                ORDER BY $column DESC
                LIMIT 1"; // Get the teacher with the highest percentage in this category

          $stmt = $pdo->prepare($query);
          $stmt->execute();
          return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Fetch the best REGULAR teacher in each category
        $topCurriculum = getTopRegularTeacher($pdo, 'Curriculum');
        $topKnowledge = getTopRegularTeacher($pdo, 'Knowledge');
        $topCommunication = getTopRegularTeacher($pdo, 'Communication');
        $topProficiency = getTopRegularTeacher($pdo, 'Proficiency');

        // Array of top teachers for looping
        $topTeachers = [
          ['teacher' => $topCurriculum, 'category' => 'Curriculum'],
          ['teacher' => $topKnowledge, 'category' => 'Knowledge'],
          ['teacher' => $topCommunication, 'category' => 'Communication'],
          ['teacher' => $topProficiency, 'category' => 'Proficiency']
        ];

        // Loop through each top teacher and display their card
        foreach ($topTeachers as $top) {
          $teacher = $top['teacher'];
          $category = $top['category'];

          if (!$teacher) continue; // Skip if no teacher found

          $photo = $teacher['Teacher_Photo'] ?: 'photo/default-teacher.jpg';
          $expertise = $teacher['Expertise_Text'] ?: $category . ' Expert (' . $teacher['Percent'] . '%)';
          $facebook = $teacher['Facebook_Link'] ?: '#';
          $twitter = $teacher['Twitter_Link'] ?: '#';
          $instagram = $teacher['Instagram_Link'] ?: '#';
        ?>

          <!-- Team Item (Top in Category) -->
          <div class="col-lg-3 col-md-6 team_col">
            <div class="team_item">
              <div class="team_image">
                <img src="admin/<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($teacher['Teacher_Name']) ?>">
              </div>
              <div class="team_body">
                <div class="team_title"><a href="teacherdetail.php?id=<?= $teacher['Teacher_ID'] ?>"><?= htmlspecialchars($teacher['Teacher_Name']) ?></a></div>
                <div class="social_list" style="margin-left: -30px;">
                  <ul>
                    <li><a href="<?= htmlspecialchars($facebook) ?>"><i class="fa-brands fa-facebook" aria-hidden="true"></i></a></li>
                    <li><a href="<?= htmlspecialchars($twitter) ?>"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a></li>
                    <li><a href="<?= htmlspecialchars($instagram) ?>"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

        <?php } ?>
      </div>
    </div>
  </div>



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
                <div class="counter_form_title">Contact Us</div>

                <?php if (!empty($formMessage)): ?>
                  <div class="form-message <?php echo $formMessageType; ?>" style="margin-top: -20px;">
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

  <section id="photo" class="py-5">
    <div class="container-xxl py-5">
      <div class="container">
        <div class="text-center">
          <h1 class="mb-5">Our Students</h1>
        </div>
        <div class="row g-3 mt-5">
          <div class="col-lg-6 col-md-6">
            <div class="row g-3">
              <div class="col-lg-12 col-md-12">
                <img class="img-fluid shadow-sm" src="photo/About/Our Student 1.jpg" alt="">
              </div>
              <div class="col-lg-6 col-md-12">
                <img class="img-fluid shadow-sm" src="photo/About/Our Student 2.jpg" alt="">
              </div>
              <div class="col-lg-6 col-md-12">
                <img class="img-fluid shadow-sm" src="photo/About/Our Student 3.jpg" alt="">
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6">
            <div class="row g-3">
              <div class="col-lg-6 col-md-12">
                <img class="img-fluid shadow-sm" src="photo/About/Our Student 4.jpg" alt="">
              </div>
              <div class="col-lg-6 col-md-12">
                <img class="img-fluid shadow-sm" src="photo/About/Our Student 5.jpg" alt="">
              </div>
              <div class="col-lg-12 col-md-12">
                <img class="img-fluid shadow-sm" src="photo/About/Our Student 6.jpg" alt="">
              </div>
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













  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
    crossorigin="anonymous"></script>
  <script>
    // Set the user ID from PHP session
    window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
  </script>
  <!-- Your cart script -->
  <script src="js/cart.js"></script>


  <!-- Search functionality (your script block) -->
  <script src="js/search.js"></script>
  <!-- Fancybox JS -->
  <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>

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
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const accordions = document.querySelectorAll(".accordion");

      accordions.forEach(accordion => {
        accordion.addEventListener("click", function() {
          // Collapse all open panels
          document.querySelectorAll(".accordion").forEach(item => {
            if (item !== this) {
              item.classList.remove("active");
              item.nextElementSibling.style.maxHeight = null;
            }
          });

          // Toggle current panel
          this.classList.toggle("active");

          const panel = this.nextElementSibling;
          if (panel.style.maxHeight) {
            panel.style.maxHeight = null;
          } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
          }
        });
      });
    });




    document.addEventListener("DOMContentLoaded", function() {
      const counters = document.querySelectorAll(".milestone_counter");

      const startCounting = (counter) => {
        const endValue = parseInt(counter.getAttribute("data-end-value"));
        const signAfter = counter.getAttribute("data-sign-after") || "";
        let current = 0;
        const speed = 50;

        const updateCounter = () => {
          const increment = Math.ceil(endValue / speed);
          if (current < endValue) {
            current += increment;
            if (current > endValue) current = endValue;
            counter.textContent = current + signAfter;
            setTimeout(updateCounter, 30);
          } else {
            counter.textContent = endValue + signAfter;
          }
        };

        updateCounter();
      };

      const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const counter = entry.target;
            startCounting(counter);
            observer.unobserve(counter); // Stop observing once counted
          }
        });
      }, {
        threshold: 0.7 // 70% visible to start
      });

      counters.forEach(counter => {
        observer.observe(counter);
      });
    });
  </script>
  <script src="js/chatbot.js"></script>


</body>

</html>
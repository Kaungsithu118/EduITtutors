<?php
session_start();
include("profilecalling.php");
include("admin/connect.php");

// Get webinar ID from URL
$webinar_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch webinar details
$stmt = $pdo->prepare("SELECT * FROM webinars WHERE webinar_id = ?");
$stmt->execute([$webinar_id]);
$webinar = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$webinar) {
    header("Location: webinar.php");
    exit();
}

// Format the date
$formattedDate = date('F j, Y', strtotime($webinar['webinar_date']));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $qualification = filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $organization = filter_input(INPUT_POST, 'organization', FILTER_SANITIZE_STRING);
    $industry_work = filter_input(INPUT_POST, 'industry', FILTER_SANITIZE_STRING);
    $webinar_id = filter_input(INPUT_POST, 'webinar_id', FILTER_VALIDATE_INT);

    // Basic validation
    if ($name && $email && $country && $qualification && $industry_work && $webinar_id) {
        try {
            // Check if email is already registered for this webinar
            $checkStmt = $pdo->prepare("SELECT * FROM webinar_registrations WHERE email = ? AND webinar_id = ?");
            $checkStmt->execute([$email, $webinar_id]);

            if ($checkStmt->rowCount() > 0) {
                $message = "You're already registered for this webinar!";
                $messageType = "warning";
            } else {
                // Insert new registration
                // Insert new registration
                $insertStmt = $pdo->prepare("INSERT INTO webinar_registrations 
                (webinar_id, name, email, country, qualification, phone, organization, industry, registration_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $insertStmt->execute([
                    $webinar_id,
                    $name,
                    $email,
                    $country,
                    $qualification,
                    $phone,
                    $organization,
                    $industry_work,
                    date("Y-m-d") // current date
                ]);

                $message = "Registration successful! You'll receive a confirmation email shortly.";
                $messageType = "success";

                // Here you would typically send a confirmation email
                // mail($email, "Webinar Registration Confirmation", "Thank you for registering...");
            }
        } catch (PDOException $e) {
            $message = "Registration failed. Please try again later.";
            $messageType = "danger";
            error_log("Webinar registration error: " . $e->getMessage());
        }
    } else {
        $message = "Please fill in all required fields correctly.";
        $messageType = "danger";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($webinar['title']) ?> | Webinar Information</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/webinardetail.css">
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />


    
</head>

<body>
    <?php include("header.php"); ?>

    <div class="home" style="height: 150px;">
        <div class="breadcrumbs_container">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="webinar.php">Webinars</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;"><?= htmlspecialchars($webinar['title']) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <section id="webinarcontent">
        <div class="container">
            <?php if (($_SESSION['role'] ?? '') !== 'Admin'): ?>
            <aside class="sidebar">
                <h2>Join Our Free IT Webinar</h2>
                <?php if (isset($message)): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="webinar_id" value="<?= $webinar_id ?>">
                    <label>Email
                        <input type="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </label>
                    <label for="country">Country
                        <select id="country" name="country" required>
                            <option value="">-- Select Your Country --</option>
                        </select>
                    </label>
                    <label>Name
                        <input type="text" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                    </label>
                    <label>
                        Qualification
                        <select name="qualification" required>
                            <option value="" disabled selected>Select your qualification</option>
                            <option value="highschool" <?= (isset($_POST['qualification']) && $_POST['qualification'] == 'highschool') ? 'selected' : '' ?>>High School Diploma</option>
                            <option value="associate" <?= (isset($_POST['qualification']) && $_POST['qualification'] == 'associate') ? 'selected' : '' ?>>Associate Degree</option>
                            <option value="bachelor" <?= (isset($_POST['qualification']) && $_POST['qualification'] == 'bachelor') ? 'selected' : '' ?>>Bachelor's Degree</option>
                            <option value="master" <?= (isset($_POST['qualification']) && $_POST['qualification'] == 'master') ? 'selected' : '' ?>>Master's Degree</option>
                            <option value="phd" <?= (isset($_POST['qualification']) && $_POST['qualification'] == 'phd') ? 'selected' : '' ?>>Ph.D. / Doctorate</option>
                            <option value="other" <?= (isset($_POST['qualification']) && $_POST['qualification'] == 'other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </label>
                    <label>Phone Number
                        <input type="tel" name="phone" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                    </label>
                    <label>School/ University/ Organization
                        <input type="text" name="organization" value="<?= isset($_POST['organization']) ? htmlspecialchars($_POST['organization']) : '' ?>">
                    </label>
                    <label class="agency-label">Are you currently working in the tech or education sector?</label>
                    <div class="radio-group">
                        <label><input type="radio" name="industry" value="yes" <?= (isset($_POST['industry']) && $_POST['industry'] == 'yes') ? 'checked' : '' ?> required> Yes</label>
                        <label><input type="radio" name="industry" value="no" <?= (isset($_POST['industry']) && $_POST['industry'] == 'no') ? 'checked' : '' ?>> No</label>
                    </div>
                    <button type="submit">Register Now</button>
                </form>
                <div class="privacy">
                    <small>
                        EduITtutors respects your privacy. By registering, you agree to receive updates about this webinar and other educational resources from EduITtutors, as outlined in our
                        <a href="#">Privacy Policy</a>. You can unsubscribe at any time.
                    </small>
                </div>
            </aside>
            <?php endif; ?>        
            <main class="main-content">
                <h1><?= htmlspecialchars($webinar['title']) ?></h1>
                <img src="admin/<?= htmlspecialchars($webinar['banner_image']) ?>" alt="Webinar visual" class="banner-img">
                <div class="webinar-details">
                    <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($webinar['location']) ?></p>
                    <p><i class="fas fa-clock"></i> <?= htmlspecialchars($webinar['time_schedule']) ?></p>
                    <p><i class="fas fa-calendar-alt"></i> <?= $formattedDate ?></p>
                </div>

                <p class="intro">
                    <?= htmlspecialchars($webinar['intro_text']) ?>
                </p>
                <p class="body">
                    <?= $webinar['body_text'] ?>
                </p>
                <p class="conclusion">
                    <?= htmlspecialchars($webinar['conclusion_text']) ?>
                </p>
            </main>
        </div>
    </section>

    <?php include("footer.php"); ?>
    <?php include("chatbot.php"); ?>

    <script>
        const countries = [
            "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda",
            "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain",
            "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia",
            "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso",
            "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic",
            "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)",
            "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti",
            "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea",
            "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon",
            "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea",
            "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India",
            "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan",
            "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon",
            "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar",
            "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania",
            "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro",
            "Morocco", "Mozambique", "Myanmar (Burma)", "Namibia", "Nauru", "Nepal", "Netherlands",
            "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway",
            "Oman", "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay",
            "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda",
            "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa",
            "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles",
            "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia",
            "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname",
            "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste",
            "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
            "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay",
            "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
        ];

        const countrySelect = document.getElementById("country");

        // Add countries to dropdown
        countries.forEach(country => {
            const option = document.createElement("option");
            option.value = country;
            option.textContent = country;
            // Preselect if form was submitted
            <?php if (isset($_POST['country'])): ?>
                if (country === "<?= htmlspecialchars($_POST['country']) ?>") {
                    option.selected = true;
                }
            <?php endif; ?>
            countrySelect.appendChild(option);
        });
    </script>
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
    <script src="js/chatbot.js"></script>
</body>

</html>
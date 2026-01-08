<?php
session_start();
require_once 'connect.php';
include ('profile_calling_admin.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Get admin data
$admin_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Admin not found");
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $bio = $_POST['bio'];
        $description = $_POST['description'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $country = $_POST['country'];

        // Handle profile image upload
        $profile_img = $admin['profile_img'];
        if (!empty($_FILES['profile_image']['name'])) {
            $upload_dir = 'uploads/User_Photo/';
            $file_name = basename($_FILES['profile_image']['name']);
            $file_path = $upload_dir . $file_name;

            // Validate file type and size
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (
                in_array($_FILES['profile_image']['type'], $allowed_types) &&
                $_FILES['profile_image']['size'] <= $max_size
            ) {

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
                    // Delete old profile image if it exists
                    if ($profile_img && file_exists($upload_dir . $profile_img)) {
                        unlink($upload_dir . $profile_img);
                    }
                    $profile_img = $file_name;
                }
            }
        }

        // Update admin data in database
        $stmt = $pdo->prepare("UPDATE user SET 
            Name = ?, 
            Email = ?, 
            phone = ?, 
            bio = ?, 
            description = ?, 
            address = ?, 
            city = ?, 
            country = ?, 
            profile_img = ? 
            WHERE User_ID = ?");

        $stmt->execute([
            $name,
            $email,
            $phone,
            $bio,
            $description,
            $address,
            $city,
            $country,
            $profile_img,
            $admin_id
        ]);

        $_SESSION['success_message'] = "Profile updated successfully!";
        header("Location: admin_edit_profile.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile Settings</title>
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">

    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="http://www.urbanui.com/" />
    <style>
        .profile-img-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php
        include('header.php');
        ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <?php
            include('choosesidebar.php');
            ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            Users Profiles
                        </h3>
                    </div>
                    <!-- Table Start -->
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="form-container">
                                    <h2 class="text-center mb-4">Admin Profile Settings</h2>

                                    <?php if (isset($_SESSION['success_message'])): ?>
                                        <div class="alert alert-success">
                                            <?= $_SESSION['success_message'] ?>
                                            <?php unset($_SESSION['success_message']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($error_message)): ?>
                                        <div class="alert alert-danger">
                                            <?= $error_message ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="text-center mb-4">
                                        <div class="profile-img-container mb-3">
                                            <img src="uploads/User_Photo/<?= htmlspecialchars($admin['profile_img'] ?? 'user_default_photo.png') ?>"
                                                class="profile-img"
                                                id="profileImageDisplay"
                                                alt="Profile Image">
                                        </div>
                                        <div>
                                            <input type="file" id="profileImageUpload" name="profile_image" accept="image/*" style="display: none;">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('profileImageUpload').click()">
                                                <i class="fas fa-upload me-1"></i> Change Photo
                                            </button>
                                            <?php if (!empty($admin['profile_img'])): ?>
                                                <button type="button" class="btn btn-danger btn-sm" id="removeProfileImageBtn">
                                                    <i class="fas fa-trash me-1"></i> Remove
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Full Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="<?= htmlspecialchars($admin['Name']) ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="<?= htmlspecialchars($admin['Email']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="bio" class="form-label">Bio</label>
                                            <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($admin['bio'] ?? '') ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <input type="text" class="form-control" id="description" name="description"
                                                value="<?= htmlspecialchars($admin['description'] ?? '') ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="<?= htmlspecialchars($admin['address'] ?? '') ?>">
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="country" class="form-label">Country</label>
                                                <select class="form-select" id="country" name="country">
                                                    <option value="">Select Country</option>
                                                    <?php if (!empty($admin['country'])): ?>
                                                        <option value="<?= htmlspecialchars($admin['country']) ?>" selected>
                                                            <?= htmlspecialchars($admin['country']) ?>
                                                        </option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="city" class="form-label">City</label>
                                                <select class="form-select" id="city" name="city"
                                                    <?= empty($admin['country']) ? 'disabled' : '' ?>>
                                                    <?php if (!empty($admin['city'])): ?>
                                                        <option value="<?= htmlspecialchars($admin['city']) ?>" selected>
                                                            <?= htmlspecialchars($admin['city']) ?>
                                                        </option>
                                                    <?php else: ?>
                                                        <option value="" selected disabled>Select City</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i> Save Changes
                                            </button>
                                            <a href="dashboard.php" class="btn btn-secondary">
                                                <i class="fas fa-times me-1"></i> Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Table End -->

                </div>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>




    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
    <script>
        // Country and city select functionality
        const countrySelect = document.getElementById("country");
        const citySelect = document.getElementById("city");

        // Load countries
        fetch("https://countriesnow.space/api/v0.1/countries/positions")
            .then(res => res.json())
            .then(data => {
                data.data.forEach(country => {
                    const option = document.createElement("option");
                    option.value = country.name;
                    option.textContent = country.name;
                    countrySelect.appendChild(option);
                });
            });

        // When country is selected, load cities
        countrySelect.addEventListener("change", () => {
            const selectedCountry = countrySelect.value;
            citySelect.innerHTML = '<option>Loading cities...</option>';
            citySelect.disabled = true;

            fetch("https://countriesnow.space/api/v0.1/countries/cities", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        country: selectedCountry
                    })
                })
                .then(res => res.json())
                .then(data => {
                    citySelect.innerHTML = "";
                    data.data.forEach(city => {
                        const option = document.createElement("option");
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                })
                .catch(err => {
                    citySelect.innerHTML = '<option>Error loading cities</option>';
                    console.error("Failed to load cities:", err);
                });
        });

        // Profile image upload preview
        document.getElementById('profileImageUpload').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, GIF)');
                    return;
                }

                if (file.size > maxSize) {
                    alert('Image size should not exceed 2MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImageDisplay').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Remove profile image
        document.getElementById('removeProfileImageBtn')?.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                fetch('remove_profile_image.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'remove'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('profileImageDisplay').src = 'uploads/User_Photo/user_default_photo.png';
                            this.style.display = 'none';
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the image');
                    });
            }
        });
    </script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/misc.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    
</body>

</html>
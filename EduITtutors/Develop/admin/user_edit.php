<!DOCTYPE html>
<html lang="en">

<?php
include('profile_calling_admin.php');
include("connect.php");

$id = $_GET['id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = :id");
$stmt->bindParam(":id", $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Count how many admins exist
$admin_count_sql = "SELECT COUNT(*) as admin_count FROM user WHERE Role = 'Admin'";
$admin_count_stmt = $pdo->query($admin_count_sql);
$admin_count = $admin_count_stmt->fetch(PDO::FETCH_ASSOC)['admin_count'];

// Check if this is the last admin
$is_last_admin = ($user['Role'] === 'Admin' && $admin_count <= 1);
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Melody Admin</title>
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
            margin-bottom: 20px;
        }

        .profile-img-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .section-title {
            border-bottom: 2px solid #4b7bec;
            padding-bottom: 5px;
            margin-bottom: 20px;
            color: #4b7bec;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php include('header.php'); ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <?php include('choosesidebar.php'); ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            Edit User Profile
                        </h3>
                    </div>

                    <form class="forms-sample" method="post" action="user_update.php" enctype="multipart/form-data">
                        <input type="hidden" name="uid" value="<?= htmlspecialchars($user['User_ID']) ?>">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-section">
                                    <h4 class="section-title">Profile Image</h4>
                                    <div class="profile-img-container text-center">
                                        <?php if (!empty($user['profile_img'])): ?>
                                            <img src="uploads/User_Photo/<?= htmlspecialchars($user['profile_img']) ?>" class="profile-img-preview" id="profile-img-preview" alt="Profile Image">
                                        <?php else: ?>
                                            <img src="uploads/user_default_photo.png" class="profile-img-preview" id="profile-img-preview" alt="Default Profile">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Change Profile Image</label>
                                        <input type="file" name="profile_img" class="form-control" onchange="previewImage(this)">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="form-section">
                                    <h4 class="section-title">Basic Information</h4>
                                    <div class="form-group">
                                        <label for="uname">Full Name</label>
                                        <input type="text" name="uname" class="form-control" id="uname" value="<?= htmlspecialchars($user['Name']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="uemail">Email</label>
                                        <input type="email" name="uemail" class="form-control" id="uemail" value="<?= htmlspecialchars($user['Email']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="utype">User Type</label>
                                        <select name="utype" class="form-control" id="utype" required <?= $is_last_admin ? 'onchange="return false;"' : '' ?>>
                                            <option value="Admin" <?= $user['Role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="User" <?= $user['Role'] === 'User' ? 'selected' : '' ?> <?= $is_last_admin ? 'disabled' : '' ?>>User</option>
                                        </select>
                                        <?php if ($is_last_admin): ?>
                                            <small class="text-danger">You cannot change the role of the last remaining admin.</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="ubio">Bio</label>
                                        <textarea name="ubio" class="form-control" id="ubio"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="udescription">Description</label>
                                        <textarea name="udescription" class="form-control" id="udescription"><?= htmlspecialchars($user['description'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4 class="section-title">Contact Information</h4>
                                    <div class="form-group">
                                        <label for="uphone">Phone</label>
                                        <input type="text" name="uphone" class="form-control" id="uphone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="uaddress">Address</label>
                                        <textarea name="uaddress" class="form-control" id="uaddress"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="ucountry">Country</label>
                                        <select name="ucountry" id="country" class="form-control" name="country" required>
                                            <option value="">Select Country</option>
                                            <?php if (!empty($user['country'])): ?>
                                                <option value="<?php echo htmlspecialchars($user['country']); ?>" selected><?php echo htmlspecialchars($user['country']); ?></option>
                                            <?php endif; ?>
                                        </select>
                                        <div id="countryLoading" class="spinner-border spinner-border-sm text-primary d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div id="countryError" class="invalid-feedback d-none">Failed to load countries</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="ucity" class="form-label">City</label>
                                        <select name="ucity" id="city" class="form-control" name="city" <?php echo empty($user['country']) ? 'disabled' : ''; ?> required>
                                            <?php if (!empty($user['city'])): ?>
                                                <option value="<?php echo htmlspecialchars($user['city']); ?>" selected><?php echo htmlspecialchars($user['city']); ?></option>
                                            <?php else: ?>
                                                <option value="" selected disabled>Select a country first</option>
                                            <?php endif; ?>
                                        </select>
                                        <div id="cityLoading" class="spinner-border spinner-border-sm text-primary d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div id="cityError" class="invalid-feedback d-none">Failed to load cities</div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4 class="section-title">Education & Interests</h4>
                                    <div class="form-group">
                                        <label for="uinstitution">Institution</label>
                                        <input type="text" name="uinstitution" class="form-control" id="uinstitution" value="<?= htmlspecialchars($user['institution'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="udegree">Degree Program</label>
                                        <input type="text" name="udegree" class="form-control" id="udegree" value="<?= htmlspecialchars($user['degree_program'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="uinterests" class="form-label">Areas of Interest</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="interests"
                                            name="uinterests"
                                            placeholder="Software, Networking, Data Science"
                                            value="<?= htmlspecialchars($user['areas_of_interest'] ?? '') ?>">
                                        <small class="form-text text-muted">Separate multiple interests with commas</small>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4 class="section-title">Additional Information</h4>
                                    <div class="form-group">
                                        <label for="udob">Date of Birth</label>
                                        <input type="date" name="udob" class="form-control" id="udob" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="ufb">Facebook ID</label>
                                        <input type="text" name="ufb" class="form-control" id="ufb" value="<?= htmlspecialchars($user['facebook_id'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" name="submit" class="btn btn-primary mr-2">Update</button>
                                    <a href="user_view.php?id=<?= $user['User_ID'] ?>" class="btn btn-light">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const isLastAdmin = <?= $is_last_admin ? 'true' : 'false' ?>;
            const currentRole = '<?= $user['Role'] ?>';

            form.addEventListener('submit', function(e) {
                if (isLastAdmin && currentRole === 'Admin') {
                    const selectedRole = document.getElementById('utype').value;
                    if (selectedRole !== 'Admin') {
                        e.preventDefault();
                        alert('You cannot change the role of the last remaining admin!');
                        return false;
                    }
                }
                return true;
            });
        });
    </script>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('profile-img-preview').src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById("country");
            const citySelect = document.getElementById("city");
            const countryLoading = document.getElementById("countryLoading");
            const cityLoading = document.getElementById("cityLoading");
            const countryError = document.getElementById("countryError");
            const cityError = document.getElementById("cityError");

            // Helper functions
            function showLoading(element) {
                element.classList.remove('d-none');
            }

            function hideLoading(element) {
                element.classList.add('d-none');
            }

            function showError(element) {
                element.classList.remove('d-none');
            }

            function hideError(element) {
                element.classList.add('d-none');
            }

            // Load countries from API
            async function loadCountries() {
                showLoading(countryLoading);
                hideError(countryError);

                try {
                    const response = await fetch("https://countriesnow.space/api/v0.1/countries");

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (!data || !data.data || !Array.isArray(data.data)) {
                        throw new Error("Invalid data format from API");
                    }

                    // Clear existing options except the first one
                    while (countrySelect.options.length > 1) {
                        countrySelect.remove(1);
                    }

                    // Add countries to select
                    data.data.forEach(country => {
                        if (country.country) {
                            const option = new Option(country.country, country.country);
                            countrySelect.add(option);
                        }
                    });

                    // If user already has a country selected, set it
                    const userCountry = "<?php echo !empty($user['country']) ? htmlspecialchars($user['country']) : ''; ?>";
                    if (userCountry) {
                        countrySelect.value = userCountry;
                        // Trigger city load for the selected country
                        loadCities(userCountry);
                    }

                } catch (error) {
                    console.error("Failed to load countries:", error);
                    showError(countryError);

                    // Fallback: Add some default countries if API fails
                    const fallbackCountries = ['Myanmar', 'Thailand', 'Singapore', 'Malaysia'];
                    fallbackCountries.forEach(country => {
                        const option = new Option(country, country);
                        countrySelect.add(option);
                    });

                    if (userCountry) {
                        countrySelect.value = userCountry;
                        loadCities(userCountry);
                    }
                } finally {
                    hideLoading(countryLoading);
                }
            }

            // Load cities for selected country
            async function loadCities(country) {
                showLoading(cityLoading);
                hideError(cityError);
                citySelect.disabled = true;
                citySelect.innerHTML = '<option value="">Loading cities...</option>';

                try {
                    const response = await fetch("https://countriesnow.space/api/v0.1/countries/cities", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            country: country
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (!data || !data.data || !Array.isArray(data.data)) {
                        throw new Error("Invalid data format from API");
                    }

                    // Clear existing options
                    citySelect.innerHTML = "";

                    // Add default option
                    const defaultOption = new Option("Select a city", "");
                    citySelect.add(defaultOption);

                    // Add cities to select
                    data.data.forEach(city => {
                        if (city) {
                            const option = new Option(city, city);
                            citySelect.add(option);
                        }
                    });

                    // Enable select and set user's city if available
                    citySelect.disabled = false;
                    const userCity = "<?php echo !empty($user['city']) ? htmlspecialchars($user['city']) : ''; ?>";
                    if (userCity) {
                        citySelect.value = userCity;
                    }

                } catch (error) {
                    console.error("Failed to load cities:", error);
                    showError(cityError);
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';

                    // Fallback: Add some default cities if API fails
                    if (country === 'Myanmar') {
                        const fallbackCities = ['Yangon', 'Mandalay', 'Naypyidaw', 'Bago'];
                        fallbackCities.forEach(city => {
                            const option = new Option(city, city);
                            citySelect.add(option);
                        });

                        if (userCity) {
                            citySelect.value = userCity;
                        }
                    }
                } finally {
                    hideLoading(cityLoading);
                }
            }

            // Event listener for country change
            countrySelect.addEventListener("change", function() {
                const selectedCountry = this.value;
                if (selectedCountry) {
                    loadCities(selectedCountry);
                } else {
                    citySelect.innerHTML = '<option value="">Select a country first</option>';
                    citySelect.disabled = true;
                }
            });

            // Initialize the country dropdown
            loadCountries();
        });
    </script>
</body>

</html>
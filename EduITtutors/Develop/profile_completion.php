<?php
session_start();
require_once "admin/connect.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if profile is already complete (optional, if you want to prevent revisits)
$stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = :user_id AND (bio IS NOT NULL OR phone IS NOT NULL)");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
if ($stmt->rowCount() > 0) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = $_POST['bio'] ?? null;
    $description = $_POST['description'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $dob = $_POST['dob'] ?? null;
    $address = $_POST['address'] ?? null;
    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;
    $institution = $_POST['institution'] ?? null;
    $degree_program = $_POST['degree_program'] ?? null;
    $areas_of_interest = $_POST['areas_of_interest'] ?? null;

    // Handle profile image upload
    $profile_img = null;

    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'admin/uploads/User_Photo/';
        $extension = strtolower(pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION));
        $file_name = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $upload_path = $upload_dir . $file_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $upload_path)) {
            $profile_img = $file_name; // ✅ Store only the filename in the DB
        }
    }

    try {
        $sql = "UPDATE user SET 
                bio = :bio,
                description = :description,
                phone = :phone,
                date_of_birth = :dob,
                address = :address,
                country = :country,
                city = :city,
                institution = :institution,
                degree_program = :degree_program,
                areas_of_interest = :areas_of_interest";

        if ($profile_img) {
            $sql .= ", profile_img = :profile_img";
        }

        $sql .= " WHERE User_ID = :user_id";

        $stmt = $pdo->prepare($sql);

        $params = [
            ':bio' => $bio,
            ':description' => $description,
            ':phone' => $phone,
            ':dob' => $dob,
            ':address' => $address,
            ':country' => $country,
            ':city' => $city,
            ':institution' => $institution,
            ':degree_program' => $degree_program,
            ':areas_of_interest' => $areas_of_interest,
            ':user_id' => $_SESSION['user_id']
        ];

        if ($profile_img) {
            $params[':profile_img'] = $profile_img;
        }

        $stmt->execute($params);

        $_SESSION['profile_complete'] = true;
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - EduITtutors</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        /* Background & layout */
        body {
            background: linear-gradient(135deg, #dce3f2, #f8faff);
            font-family: 'Segoe UI', sans-serif;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main card */
        .container {
            max-width: 960px;
            width: 95%;
            padding: 40px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(14px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        /* Header */
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h2 {
            font-size: 30px;
            font-weight: 700;
            color: #1e2a38;
        }

        .profile-header p {
            color: #555;
            font-size: 15px;
        }

        /* Alert message */
        .alert-danger {
            background: #ffe5e8;
            color: #d8000c;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .profile-pic-container {
            width: 160px;
            height: 160px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border: 4px solid #4f46e5;
            /* Indigo border for a modern touch */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }

        .profile-pic-container:hover {
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.3);
        }

        .profile-pic-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        .profile-pic-container:hover img {
            transform: scale(1.05);
        }

        .profile-pic-upload {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            text-align: center;
            padding: 8px 0;
            color: #fff;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .profile-pic-upload:hover {
            background: rgba(0, 0, 0, 0.75);
        }

        .profile-pic-upload input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            cursor: pointer;
        }


        /* Form Group */
        .form-group {
            position: relative;
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            font-size: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background-color: #fcfcfc;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
            background-color: #fff;
            outline: none;
        }

        /* Small helper text */
        .form-text.text-muted {
            font-size: 12px;
            color: #888;
        }

        /* Submit button */
        .btn-submit {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 12px 32px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 40px;
            transition: all 0.4s ease;
            box-shadow: 0 5px 10px rgba(38, 132, 255, 0.3);
        }

        .btn-submit:hover {
            background: linear-gradient(to right, #3b82f6, #4f46e5);
            transform: translateY(-2px);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            .btn-submit {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <h2>Complete Your Profile</h2>
                <p>Please provide some additional information to help us personalize your experience</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="profile-pic-container">
                            <div id="profile-pic-preview"></div>
                            <div class="profile-pic-upload">
                                <span>Upload Photo</span>
                                <input type="file" name="profile_img" id="profile-img-input" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="bio">Short Bio</label>
                            <input type="text" class="form-control" id="bio" name="bio" placeholder="Tell us about yourself in a few words">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="More about you..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="+959 123 456 789">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="123 Main Street">
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="country" class="form-label">Country</label>
                        <select id="country" class="form-select" name="country" required>
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
                    <div class="col-md-6">
                        <label for="city" class="form-label">City</label>
                        <select id="city" class="form-select" name="city" <?php echo empty($user['country']) ? 'disabled' : ''; ?> required>
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

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="institution">Institution</label>
                            <input type="text" class="form-control" id="institution" name="institution" placeholder="Info Myanmar University">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="degree_program">Degree Program</label>
                            <input type="text" class="form-control" id="degree_program" name="degree_program" placeholder="HND Program">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="areas_of_interest">Areas of Interest</label>
                    <input type="text" class="form-control" id="areas_of_interest" name="areas_of_interest" placeholder="Software, Networking, Data Science">
                    <small class="form-text text-muted">Separate multiple interests with commas</small>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-submit">Complete Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Preview profile image before upload
        document.getElementById('profile-img-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profile-pic-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById("country");
            const citySelect = document.getElementById("city");
            const countryLoading = document.getElementById("countryLoading");
            const cityLoading = document.getElementById("cityLoading");
            const countryError = document.getElementById("countryError");
            const cityError = document.getElementById("cityError");

            // Function to show loading state
            function showLoading(element) {
                element.classList.remove('d-none');
            }

            // Function to hide loading state
            function hideLoading(element) {
                element.classList.add('d-none');
            }

            // Function to show error
            function showError(element) {
                element.classList.remove('d-none');
            }

            // Function to hide error
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
                    if (userCountry && countrySelect.querySelector(`option[value="${userCountry}"]`)) {
                        countrySelect.value = userCountry;
                        // Trigger city load for the selected country
                        loadCities(userCountry);
                    }

                } catch (error) {
                    console.error("Failed to load countries:", error);
                    showError(countryError);
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
                    if (userCity && citySelect.querySelector(`option[value="${userCity}"]`)) {
                        citySelect.value = userCity;
                    }

                } catch (error) {
                    console.error("Failed to load cities:", error);
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                    showError(cityError);
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
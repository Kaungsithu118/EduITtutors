
const countrySelect = document.getElementById("country");
const citySelect = document.getElementById("city");

// 1. Load countries
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

// 2. When a country is selected, load cities
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

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('profileImageUpload').addEventListener('change', function (e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 2 * 1024 * 1024;
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, GIF)');
                return;
            }
            if (file.size > maxSize) {
                alert('Image size should not exceed 2MB');
                return;
            }
            // Preview image
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('profileImageDisplay').src = e.target.result;
            }
            reader.readAsDataURL(file);
            // Upload to server
            const formData = new FormData();
            formData.append('profile_image', file);
            formData.append('action', 'upload');
            fetch('profile_image_handler.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Profile picture updated successfully!');
                        document.getElementById('removeProfileImageBtn').disabled = false;
                        document.getElementById('profileImageDisplay').src = data.image_path + '?t=' + new Date().getTime();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while uploading the image');
                });
        }
    });
    document.getElementById('removeProfileImageBtn').addEventListener('click', function () {
        if (confirm('Are you sure you want to remove your profile picture?')) {
            fetch('profile_image_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=remove'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Profile picture removed successfully!');
                        document.getElementById('profileImageDisplay').src = 'photo/default-profile.jpg';
                        document.getElementById('removeProfileImageBtn').disabled = true;
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
});

// Example JavaScript for the profile settings page
document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Handle profile picture upload
    document.querySelector('.btn-outline-primary').addEventListener('click', function () {
        // In a real app, this would trigger a file upload dialog
        alert('Profile picture upload functionality would go here');
    });




    // Password strength checker (same as in login.php)
    function checkPasswordStrength(password) {
        const meter = document.getElementById('password-strength-meter');
        const text = document.getElementById('password-strength-text');

        // Reset classes and text
        meter.className = 'progress';
        text.textContent = '';

        // Check password strength
        let strength = 0;

        // Length check
        if (password.length >= 8) strength++;

        // Contains uppercase
        if (/[A-Z]/.test(password)) strength++;

        // Contains lowercase
        if (/[a-z]/.test(password)) strength++;

        // Contains number
        if (/[0-9]/.test(password)) strength++;

        // Update meter
        meter.classList.add(`strength-${strength}`);

        // Update text
        const messages = [
            'Very Weak',
            'Weak',
            'Moderate',
            'Strong',
            'Very Strong'
        ];

        if (password.length > 0) {
            text.textContent = `Strength: ${messages[strength]}`;
        } else {
            text.textContent = 'Password must contain at least 8 characters, one uppercase, one lowercase, and one number';
        }

        return strength >= 3; // At least 3 out of 4 requirements met
    }

    // Modified password change form handler
    document.getElementById('passwordChangeForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const messageDiv = document.getElementById('passwordChangeMessage');
        const newPassword = form.querySelector('#newPassword').value;
        const confirmPassword = form.querySelector('#confirmPassword').value;

        // Clear previous messages
        messageDiv.innerHTML = '';

        // Validate password strength
        if (!checkPasswordStrength(newPassword)) {
            messageDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show">
                Password is not strong enough. It must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            return;
        }

        // Validate passwords match
        if (newPassword !== confirmPassword) {
            messageDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show">
                Passwords do not match
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        Changing Password...
    `;

        try {
            const formData = new FormData(form);

            // For social media users without password, we don't send current_password
            if (document.getElementById('currentPassword') && !document.getElementById('currentPassword').value) {
                formData.delete('current_password');
            }

            const response = await fetch('update_password.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                messageDiv.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show">
                    ${result.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

                // Reset form and reload after 2 seconds
                setTimeout(() => {
                    form.reset();
                    bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                    location.reload(); // Refresh to update "last changed" date
                }, 2000);
            } else {
                messageDiv.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show">
                    ${result.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            }
        } catch (error) {
            console.error("Error:", error);
            messageDiv.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show">
                Network error occurred. Please try again.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Change Password';
        }
    });




});

document.addEventListener('DOMContentLoaded', function () {
    const educationForm = document.getElementById('educationForm');

    if (educationForm) {
        educationForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('saveEducationBtn');
            const messageDiv = document.getElementById('educationMessage');

            // Validate required fields
            const institution = document.getElementById('institution').value.trim();
            const degree = document.getElementById('degree').value.trim();

            if (!institution || !degree) {
                showMessage('Institution and Degree Program are required', 'danger');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Saving...
                `;

            try {
                const formData = new FormData(educationForm);

                const response = await fetch('updateedu.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                // Reload to show updated data
                window.location.reload();

            } catch (error) {
                console.error("Error:", error);
                showMessage('An error occurred. Please try again.', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            }
        });
    }

    function showMessage(message, type) {
        const messageDiv = document.getElementById('educationMessage');
        messageDiv.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
});

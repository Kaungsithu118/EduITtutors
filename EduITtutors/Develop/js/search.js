document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('course-search');
    const searchResults = document.getElementById('search-results');
    let debounceTimer;
    let courses = [];

    // Fetch courses
    fetch('courses_fetch.php')
        .then(response => response.json())
        .then(data => {
            courses = data;
        })
        .catch(error => {
            console.error('Error fetching courses:', error);
        });

    // Handle search input
    searchInput.addEventListener('input', function (e) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchTerm = e.target.value.trim().toLowerCase();

            if (searchTerm.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            const filteredCourses = courses.filter(course =>
                course.name.toLowerCase().includes(searchTerm) ||
                course.teacher.toLowerCase().includes(searchTerm)
            );

            displayResults(filteredCourses);
        }, 300);
    });

    // Display results
    function displayResults(results) {
        searchResults.innerHTML = '';

        if (results.length === 0) {
            searchResults.innerHTML = '<div class="dropdown-item">No courses found</div>';
            searchResults.style.display = 'block';
            return;
        }

        results.forEach(course => {
            const item = document.createElement('a');
            item.className = 'dropdown-item search-result-item d-flex align-items-center';
            item.href = `course_detail.php?id=${course.id}`;
            item.innerHTML = `
                <img src="admin/${course.image}" alt="${course.name}">
                <div class="course-info">
                    <div class="course-name">${course.name}</div>
                    <div class="course-teacher">${course.teacher}</div>
                </div>
                <div class="ms-auto text-primary">$${parseFloat(course.price).toFixed(2)}</div>
            `;
            searchResults.appendChild(item);
        });

        searchResults.style.display = 'block';
    }

    // Prevent only search form submission
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Optional: trigger search on explicit form submit
            if (searchInput.value.trim().length > 1) {
                searchInput.dispatchEvent(new Event('input'));
            }
        });
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchForm.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
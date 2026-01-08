<?php
include("profilecalling.php");
?>

<?php
// Get current file name, like 'about.php'
$currentPage = basename($_SERVER['PHP_SELF'], ".php");

// Format page name (capitalize first letter)
$breadcrumbTitle = ucfirst($currentPage);

// Special case for index.php
if ($currentPage === 'index') {
	$breadcrumbTitle = 'Home';
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
include("admin/connect.php");

// Fetch all departments for categories dropdown
$departments = $pdo->query("SELECT Department_ID, Department_Name FROM Departments")->fetchAll(PDO::FETCH_ASSOC);

// Fetch department data for gallery section
$departments_gallery = $pdo->query("SELECT Department_ID, Department_Name, Department_Photo FROM Departments")->fetchAll(PDO::FETCH_ASSOC);

// Get current date
$currentDate = date('Y-m-d H:i:s');

// Query active event discounts
$activeEvents = $pdo->query("
    SELECT e.event_id, e.discount_percentage, GROUP_CONCAT(ec.course_id) AS course_ids
    FROM event_discounts e
    LEFT JOIN event_discount_courses ec ON e.event_id = ec.event_id
    WHERE e.is_active = 1 
    AND '$currentDate' BETWEEN e.start_datetime AND e.end_datetime
    GROUP BY e.event_id
")->fetchAll(PDO::FETCH_ASSOC);

// Create a map of course IDs to their discount percentages
$courseDiscounts = [];
foreach ($activeEvents as $event) {
	$discount = $event['discount_percentage'];
	$courseIds = explode(',', $event['course_ids']);
	foreach ($courseIds as $courseId) {
		$courseDiscounts[$courseId] = $discount;
	}
}

// Search logic
$keyword = $_POST['search'] ?? '';
$category = $_POST['category'] ?? 'All Categories';

if (!empty($keyword)) {
	$sql = "
        SELECT 
            c.Course_ID, 
            c.Course_Name, 
            c.Course_Photo, 
            c.Duration, 
            c.Course_Fees,
            c.updated_at,
            t.Teacher_Name,
            d.Department_Name,
            cd.Introduction_Text
        FROM Courses c
        JOIN Teachers t ON c.Teacher_ID = t.Teacher_ID
        JOIN Departments d ON c.Department_ID = d.Department_ID
        JOIN Course_Descriptions cd ON c.Description_ID = cd.Description_ID
        WHERE (c.Course_Name LIKE :kw 
               OR t.Teacher_Name LIKE :kw 
               OR cd.Introduction_Text LIKE :kw)";

	if ($category !== 'All Categories') {
		$sql .= " AND d.Department_Name = :cat";
	}

	$sql .= " ORDER BY c.updated_at DESC";

	$stmt = $pdo->prepare($sql);
	$kw = "$keyword%"; // Starts with keyword
	$stmt->bindParam(':kw', $kw);

	if ($category !== 'All Categories') {
		$stmt->bindParam(':cat', $category);
	}

	$stmt->execute();
	$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
	// No search: show all courses
	$courses = $pdo->query("
        SELECT 
            c.Course_ID, 
            c.Course_Name, 
            c.Course_Photo, 
            c.Duration, 
            c.Course_Fees,
            c.updated_at,
            t.Teacher_Name,
            d.Department_Name,
            cd.Introduction_Text
        FROM Courses c
        JOIN Teachers t ON c.Teacher_ID = t.Teacher_ID
        JOIN Departments d ON c.Department_ID = d.Department_ID
        JOIN Course_Descriptions cd ON c.Description_ID = cd.Description_ID
        ORDER BY c.updated_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

// Get latest 3 courses
$latestCourses = array_slice($courses, 0, 3);

// Shuffle and limit department photos
shuffle($departments_gallery);
$departments_galleryphoto = array_slice($departments_gallery, 0, 6);
?>




<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Course</title>

	<link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
	<link rel="stylesheet" href="css/course.css">
	<link rel="stylesheet" href="css/courses_responsive.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/cartslidebar.css">
	<link rel="stylesheet" href="css/search.css">
	<link rel="stylesheet" href="css/chatbot.css">


	<!-- Font Awesome for play icon -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />


	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900"
		rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

	<style>

	</style>
</head>

<body>

	<?php
	include("header.php");
	?>



	<div class="home">
		<div class="breadcrumbs_container">
			<div class="container">
				<div class="row">
					<div class="col">
						<div class="breadcrumbs">
							<ul>
								<li><a href="index.php">Home</a></li>
								<?php if ($breadcrumbTitle !== 'Home'): ?>
									<li style="color: #384158; font-size: 16px; font-weight: 700;">
										<?= $breadcrumbTitle ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Courses -->

	<div class="courses container">
		<div class="row">

			<!-- Courses Main Content -->
			<div class="col-lg-8">
				<div class="courses_search_container d-flex align-items-center">
					<form action="Course.php" method="POST" id="courses_search_form" class="courses_search_form d-flex flex-row align-items-center justify-content-start">
						<input type="search" name="search" class="courses_search_input" placeholder="Search Courses" required>
						<select name="category" class="courses_search_select courses_search_input">
							<option value="All Categories">All Categories</option>
							<?php foreach ($departments as $dept): ?>
								<option value="<?= htmlspecialchars($dept['Department_Name']) ?>" <?= ($category === $dept['Department_Name']) ? 'selected' : '' ?>>
									<?= htmlspecialchars($dept['Department_Name']) ?>
								</option>
							<?php endforeach; ?>
						</select>
						<button type="submit" class="courses_search_button">Search now</button>
					</form>
				</div>


				<div class="courses_container mt-5">

					<!-- Pagination -->
					<div class="row pagination_row">
						<div class="col-12">
							<div class="pagination_container d-flex flex-wrap align-items-center justify-content-between gap-3">
								<!-- Pagination list -->
								<ul class="pagination_list pagination mb-0 d-flex flex-wrap align-items-center gap-1">
									<li class="page-item disabled">
										<a class="page-link" href="#" aria-label="Previous">
											<i class="fa fa-angle-left" aria-hidden="true"></i>
										</a>
									</li>
									<li class="page-item active"><a class="page-link" href="#">1</a></li>
									<li class="page-item"><a class="page-link" href="#">2</a></li>
									<li class="page-item">
										<a class="page-link" href="#" aria-label="Next">
											<i class="fa fa-angle-right" aria-hidden="true"></i>
										</a>
									</li>
								</ul>

								<!-- Courses showing info -->
								<div class="courses_show_container d-flex align-items-center flex-wrap justify-content-end">
									<div class="courses_show_text me-3">
										Showing <span class="courses_showing">1-6</span> of <span class="courses_total">10</span> results:
									</div>
									<div class="courses_show_content">
										<span>Show: </span>
										<select id="courses_show_select" class="courses_show_select form-select form-select-sm w-auto d-inline-block">
											<option value="6" selected>06</option>
											<option value="12">12</option>
											<option value="24">24</option>
											<option value="36">36</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>



					<div class="row courses_row mt-5">
						<?php foreach ($courses as $course):
							$introFirstPara = strtok($course['Introduction_Text'], "\n");
							$shortIntro = strlen($introFirstPara) > 150 ? substr($introFirstPara, 0, 150) . '...' : $introFirstPara;

							// Check if this course has a discount
							$hasDiscount = isset($courseDiscounts[$course['Course_ID']]);
							$discountPercent = $hasDiscount ? $courseDiscounts[$course['Course_ID']] : 0;
							$discountedPrice = $hasDiscount ?
								$course['Course_Fees'] * (1 - $discountPercent / 100) :
								$course['Course_Fees'];
						?>
							<div class="col-lg-6 course_col course_item mb-4">
								<div class="course">
									<div class="course_image">
										<img src="admin/<?= htmlspecialchars($course['Course_Photo']) ?>" alt="<?= htmlspecialchars($course['Course_Name']) ?>">
									</div>
									<div class="course_body">
										<h3 class="course_title mt-1">
											<a href="course_detail.php?id=<?= $course['Course_ID'] ?>"><?= htmlspecialchars($course['Course_Name']) ?></a>
										</h3>
										<div class="course_teacher"><?= htmlspecialchars($course['Teacher_Name']) ?></div>
										<div class="course_text">
											<p><?= htmlspecialchars($shortIntro) ?></p>
										</div>
									</div>
									<div class="course_footer">
										<div class="course_footer_content d-flex flex-row align-items-center justify-content-start">
											<div class="course_info">
												<i class="fa fa-graduation-cap" aria-hidden="true"></i>
												<span class="mx-3"><?= htmlspecialchars($course['Department_Name']) ?></span>
											</div>
											<div class="course_price ml-auto d-flex flex-row justify-content-center align-items-center">
												<?php if ($hasDiscount): ?>
													<span class="course_price ml-auto">$<?= number_format($course['Course_Fees'], 2) ?></span>
													<div class="course_price ml-auto">$<?= number_format($discountedPrice, 2) ?></div>	
													
												<?php else: ?>
													<div class="course_price ml-auto">$<?= number_format($course['Course_Fees'], 2) ?></div>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>


				</div>
			</div>

			<!-- Sidebar -->
			<div class="col-lg-4">
				<div class="sidebar">

					<!-- Categories -->
					<div class="sidebar_section">
						<div class="sidebar_section_title">Categories</div>
						<div class="sidebar_categories">
							<ul>
								<?php foreach ($departments as $dept): ?>
									<li><a href="department.php?id=<?= $dept['Department_ID'] ?>"><?= htmlspecialchars($dept['Department_Name']) ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>


					<!-- Latest Courses -->
					<div class="sidebar_section">
						<div class="sidebar_section_title">Latest Courses</div>
						<div class="sidebar_latest">
							<?php foreach ($latestCourses as $course): ?>
								<div class="latest d-flex flex-row align-items-start justify-content-start mb-3">
									<div class="latest_image">
										<div><img src="admin/<?= htmlspecialchars($course['Course_Photo']) ?>" alt="<?= htmlspecialchars($course['Course_Name']) ?>" style="width: 60px; height: 60px; object-fit: cover;"></div>
									</div>
									<div class="latest_content">
										<div class="latest_title"><a href="course_detail.php?id=<?= $course['Course_ID'] ?>"><?= htmlspecialchars($course['Course_Name']) ?></a></div>
										<div class="latest_price">$<?= number_format($course['Course_Fees'], 2) ?></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<!-- You can continue with Instagram section and others... -->
					<!-- Gallery -->
					<div class="sidebar_section">
						<div class="sidebar_section_title">Department Gallery</div>
						<div class="sidebar_gallery">
							<ul class="gallery_items d-flex flex-row align-items-start justify-content-between flex-wrap">
								<?php foreach ($departments_galleryphoto as $dept): ?>
									<li class="gallery_item">
										<div class="gallery_item_overlay d-flex flex-column align-items-center justify-content-center">
											+
										</div>
										<a href="#" class="gallery-link" data-image="admin/<?= htmlspecialchars($dept['Department_Photo']) ?>">
											<img src="admin/<?= htmlspecialchars($dept['Department_Photo']) ?>" alt="<?= htmlspecialchars($dept['Department_Name']) ?>">
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>



					<div id="lightbox">
						<span class="close">&times;</span>
						<img id="lightbox-image" src="" alt="">
					</div>




					<!-- Banner -->
					<div class="sidebar_section">
						<div class="sidebar_section_title">Our Gudience</div>
						<div
							class="sidebar_banner mt-5 d-flex flex-column align-items-center justify-content-center text-center">
							<div class="sidebar_banner_background"
								style="background-image:url(../Develop/admin/uploads/learning-outcomes.webp)"></div>
							<div class="sidebar_banner_overlay"></div>
							<div class="sidebar_banner_content">
								<div class="banner_title">Our Gudience</div>
								<div class="banner_button"><a href="../Develop/admin/uploads/Computer_Science_Programs_Goals_Student_Learning_O.pdf">download now</a></div>
							</div>
						</div>
					</div>



				</div>
			</div>
		</div>

	</div>


	<?php
	include("footer.php");
	?>
	<?php
	include("chatbot.php");
	?>
	



	<script>
		document.addEventListener("DOMContentLoaded", function() {
			const coursesPerPageSelect = document.getElementById("courses_show_select");
			const courseItems = Array.from(document.querySelectorAll(".course_col")); // Your course item class
			const paginationList = document.querySelector(".pagination_list");
			const showingText = document.querySelector(".courses_showing");
			const totalText = document.querySelector(".courses_total");

			let currentPage = 1;
			let coursesPerPage = parseInt(coursesPerPageSelect.value);
			let totalPages = Math.ceil(courseItems.length / coursesPerPage);

			function renderCourses() {
				const totalCourses = courseItems.length;
				totalText.textContent = totalCourses;

				const start = (currentPage - 1) * coursesPerPage;
				const end = Math.min(start + coursesPerPage, totalCourses);

				showingText.textContent = `${start + 1}-${end}`;

				courseItems.forEach((course, index) => {
					course.style.display = (index >= start && index < end) ? "block" : "none";
				});

				renderPagination();
			}

			function renderPagination() {
				paginationList.innerHTML = "";

				// Always show "<" (Previous)
				const prevLi = document.createElement("li");
				prevLi.innerHTML = `<a href="#"><i class="fa fa-angle-left" aria-hidden="true"></i></a>`;
				if (currentPage > 1) {
					prevLi.addEventListener("click", function(e) {
						e.preventDefault();
						currentPage--;
						renderCourses();
					});
				} else {
					prevLi.classList.add("disabled");
				}
				paginationList.appendChild(prevLi);

				// Sliding window pagination numbers
				let startPage = Math.max(currentPage - 1, 1);
				if (currentPage >= totalPages - 1) {
					startPage = Math.max(totalPages - 2, 1);
				}

				for (let i = startPage; i < startPage + 3 && i <= totalPages; i++) {
					const li = document.createElement("li");
					if (i === currentPage) li.classList.add("active");
					li.innerHTML = `<a href="#">${i}</a>`;
					li.addEventListener("click", function(e) {
						e.preventDefault();
						currentPage = i;
						renderCourses();
					});
					paginationList.appendChild(li);
				}

				// Always show ">" (Next)
				const nextLi = document.createElement("li");
				nextLi.innerHTML = `<a href="#"><i class="fa fa-angle-right" aria-hidden="true"></i></a>`;
				if (currentPage < totalPages) {
					nextLi.addEventListener("click", function(e) {
						e.preventDefault();
						currentPage++;
						renderCourses();
					});
				} else {
					nextLi.classList.add("disabled");
				}
				paginationList.appendChild(nextLi);
			}

			coursesPerPageSelect.addEventListener("change", function() {
				coursesPerPage = parseInt(this.value);
				totalPages = Math.ceil(courseItems.length / coursesPerPage);
				currentPage = 1;
				renderCourses();
			});

			renderCourses();
		});
	</script>




	<!-- JavaScript for Lightbox -->




	<!-- Bootstrap Bundle (includes Popper) - Only one version is needed -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
		crossorigin="anonymous"></script>

	<!-- Your custom course search script -->
	<script src="js/courses.js"></script>

	<script>
		// Set the user ID from PHP session
		window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
	</script>
	<!-- Your cart script -->
	<script src="js/cart.js"></script>


	<!-- Search functionality (your script block) -->
	<script src="js/search.js"></script>
	<script src="js/chatbot.js"></script>

	<script src="js/course_search.js"></script>


</body>

</html>
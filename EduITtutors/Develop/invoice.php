<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - EduITtutors</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/invoice.css">

    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            /* Fix for Bootstrap printing issues */
            .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }

            /* Ensure tables don't break across pages */
            table {
                page-break-inside: avoid;
            }

            /* Adjust font sizes */
            body {
                font-size: 12pt;
            }

            /* Make sure totals stand out */
            .last-row td,
            .last-row th {
                font-weight: bold;
                font-size: 1.1em;
            }

            /* Remove background images if any */
            .logo img {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>


    <style>
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-print,
        .btn-download,
        .btn-continue {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            font-size: 16px;
        }

        .btn-print {
            background-color: #04617B;
            color: white;
        }

        .btn-print:hover {
            background-color: #034b60;
            transform: translateY(-2px);
        }

        .btn-download {
            background-color: #d32f2f;
            color: white;
        }

        .btn-download:hover {
            background-color: #b71c1c;
            transform: translateY(-2px);
        }

        .btn-continue {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            text-align: center;
        }

        .btn-continue:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .btn-container {
                flex-direction: column;
                align-items: center;
            }

            .btn-print,
            .btn-download,
            .btn-continue {
                width: 100%;
                max-width: 250px;
                justify-content: center;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <?php
    session_start();
    include("admin/connect.php");

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Get last order ID from session
    if (!isset($_SESSION['last_order_id'])) {
        header("Location: course.php");
        exit();
    }

    $order_id = $_SESSION['last_order_id'];
    $user_id = $_SESSION['user_id'];

    // Get order details
    $order_stmt = $pdo->prepare("
    SELECT o.*, u.Name, u.Email, u.phone, u.address, u.city, u.country 
    FROM orders o
    JOIN user u ON o.User_ID = u.User_ID
    WHERE o.Order_ID = ? AND o.User_ID = ?");
    $order_stmt->execute([$order_id, $user_id]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: course.php");
        exit();
    }

    // Get order items
    $items_stmt = $pdo->prepare("SELECT * FROM order_items WHERE Order_ID = ?");

    $items_stmt->execute([$order_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals from database (for consistency)
    $subtotal = $order['Subtotal'];
    $tax = $order['Tax'];
    $total = $order['Total'];
    ?>

    <section id="invoice">
        <!--Paper Invoice Page Here -->
        <div class="container my-5 px-5 py-5" id="invoice-print">
            <div class="row">
                <div class="col-3 contact-details">
                    <h5>EduITtutors</h5>
                    <h6><em>No 34, Kannar Road, Yangon</em></h6>
                    <p>Phone: +959 123 456 789<br>Email: info@eduitutors.com</p>
                </div>
                <div class="col-1 offset-2 logo">
                    <img width="160px" height="160px" src="photo/logo/EduITtutors_Colorver_Logo.png" />
                </div>
                <div class="invoice-details col-3 offset-3 text-right">
                    <h6>Invoice No. #<?= uniqid() ?></h6>
                    <h6>Issued at: <?= date('d/m/Y H:i') ?></h6>
                </div>
            </div>

            <div class="container-fluid invoice-letter mt-3">
                <div class="row">
                    <div class="col-3 text-white pl-5 py-2 letter-title">
                        <h5>Summary & Notes</h5>
                    </div>
                    <div class="col-9 text-white pr-5 py-2 letter-content">
                        <p>Thank you for your purchase with EduITtutors. This invoice confirms your enrollment in the selected courses. Please keep this for your records. For any questions, contact our support team at support@eduitutors.com.</p>
                    </div>
                </div>
            </div>

            <div class="row table mt-5">
                <table class="invoice table table-hover">
                    <thead class="thead">
                        <tr>
                            <th scope="col">NO.</th>
                            <th scope="col">Course</th>
                            <th scope="col">Instructor</th>
                            <th scope="col">Price</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                            <tr>
                                <th scope="row"><?= $index + 1 ?></th>
                                <td class="item"><?= htmlspecialchars($item['Course_Name']) ?></td>
                                <td><?= htmlspecialchars($item['Teacher_Name']) ?></td>
                                <td>$<?= number_format($item['Price'], 2) ?></td>
                                <td>$<?= number_format($item['Price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <!-- invoiced to details -->
                <div class="offset-2 col-4">
                    <div class="to text-right">
                        <div class="">
                            <p class="mb-1 fw-semibold text-muted">Invoiced to:</p>
                            <h5 class="fw-bold text-dark mb-3"><?= htmlspecialchars($order['Name']) ?></h5>
                            <p class="mb-3 text-secondary">
                                <?= htmlspecialchars($order['address'] ?? 'N/A') ?><br>
                                <?= htmlspecialchars($user['city'] ?? 'N/A') ?>, <?= htmlspecialchars($order['country'] ?? 'N/A') ?>
                            </p>
                            <p class="mb-3 text-secondary">
                                <?= htmlspecialchars($order['Email']) ?><br>
                                <?= htmlspecialchars($order['phone'] ?? 'N/A') ?>
                            </p>
                            <p class="fw-semibold mt-3">Due date: <?= date('d/m/Y', strtotime('+7 days')) ?></p>
                        </div>
                    </div>
                </div>
                <!-- Invoice assets and total -->
                <div class="col-6 pr-5">
                    <table class="table table-borderless text-left" style="width: 100%;">
                        <tbody>
                            <tr>
                                <th class="text-start" style="width: 70%;">Subtotal</th>
                                <td class="text-start">$<?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <tr>
                                <th class="text-start">Taxes <small>(7%)</small></th>
                                <td class="text-start">$<?= number_format($tax, 2) ?></td>
                            </tr>
                            <tr class="border-top fw-bold">
                                <th class="text-start" style="font-size: 1.1rem;">Total</th>
                                <td class="text-start" style="font-size: 1.1rem;">$<?= number_format($total, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-center mt-3"><em>* Payment is due within 7 days. Access to courses will be granted after payment confirmation.</em></p>
        </div>

        <div class="container my-5 px-5 py-5 no-print">
            <div class="btn-container">
                <!-- Print Button -->
                <button onclick="window.print()" class="btn-print">
                    <i class="fas fa-print me-2"></i> Print
                </button>

                <!-- Download PDF Button -->
                <button id="download-pdf" class="btn-download">
                    <i class="fas fa-file-pdf me-2"></i> Download PDF
                </button>

                <!-- Continue Button -->
                <a href="complete_order.php" class="btn-continue">
                    <i class="fas fa-arrow-right me-2"></i> Continue to Lessons
                </a>
            </div>
        </div>

        <!-- Loading overlay -->
        <div id="pdf-loading" class="pdf-loading-overlay">
            <div class="pdf-loading-content">
                <div class="spinner"></div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // After printing, redirect or do something else if needed
        window.onafterprint = function() {
            console.log("Printing completed or canceled");
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('download-pdf').addEventListener('click', async function() {
            const button = this;
            const loadingOverlay = document.getElementById('pdf-loading');
            const originalText = button.innerHTML;

            try {
                loadingOverlay.style.display = 'flex';
                button.disabled = true;

                const element = document.getElementById('invoice-print');
                window.scrollTo(0, 0); // Ensure top rendering

                const opt = {
                    margin: [5, 5, 5, 5], // Reduced margin
                    filename: 'EduITtutors_Invoice.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 1.0
                    },
                    html2canvas: {
                        scale: 3, // Higher scale for better resolution
                        useCORS: true,
                        allowTaint: true,
                        scrollX: 0,
                        scrollY: 0,
                        letterRendering: true
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait',
                        compress: true
                    },
                    pagebreak: {
                        mode: 'avoid-all'
                    }
                };

                const pdfPromise = html2pdf().set(opt).from(element).save();

                const timeoutPromise = new Promise((_, reject) => {
                    setTimeout(() => reject(new Error('PDF generation timeout')), 15000);
                });

                await Promise.race([pdfPromise, timeoutPromise]);

            } catch (error) {
                console.error('PDF generation error:', error);
                alert('PDF generation took too long. Please try again or use the Print button instead.');
            } finally {
                loadingOverlay.style.display = 'none';
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    </script>
</body>

</html>
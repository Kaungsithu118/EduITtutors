<div id="eventDiscountModal" class="modal fade" tabindex="-1" aria-hidden="true" data-event-id="<?= isset($event['event_id']) ? $event['event_id'] : '' ?>">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Special Event Discount!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                // Get current date
                $currentDate = date('Y-m-d H:i:s');

                // Query to get active event discounts
                $stmt = $pdo->prepare("SELECT * FROM event_discounts 
                                     WHERE is_active = 1 
                                     AND start_datetime <= :current_date 
                                     AND end_datetime >= :current_date 
                                     ORDER BY end_datetime ASC 
                                     LIMIT 1");
                $stmt->bindParam(':current_date', $currentDate);
                $stmt->execute();
                $event = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($event) {
                    $endDate = date('F j, Y', strtotime($event['end_datetime']));
                ?>
                    <div class="event-ad">
                        <h3 class="event-title"><?= htmlspecialchars($event['event_name']) ?></h3>
                        <div class="discount-badge">
                            🔥<?= htmlspecialchars($event['discount_percentage']) ?>% OFF
                        </div>
                        <p class="event-description mt-4 mb-2" style="font-size:15px;"><?= htmlspecialchars($event['event_description']) ?></p>
                        <p class="event-end-date"><strong>Offer ends:</strong> <?= $endDate ?></p>
                    </div>
                <?php } else { ?>
                    <p>No current events</p>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="courses.php" class="btn btn-primary">View Courses</a>
            </div>
        </div>
    </div>
</div>
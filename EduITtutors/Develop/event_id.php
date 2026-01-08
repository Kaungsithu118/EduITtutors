    <?php if ($event): ?>
        <script>
            // Pass PHP variables to JavaScript
            var eventData = {
                event_id: <?= json_encode($event['event_id']) ?>,
                // add other event properties you need
            };
        </script>
    <?php endif; ?>
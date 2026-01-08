
// Show modal if there's an active event
document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('eventDiscountModal'));

    // Check if event data exists (passed from PHP)
    if (typeof eventData !== 'undefined' && eventData) {
        // Only show if there's an active event
        modal.show();

        // Set localStorage to prevent showing again for this event
        const eventShown = localStorage.getItem('eventShown_' + eventData.event_id);
        if (!eventShown) {
            modal.show();
            localStorage.setItem('eventShown_' + eventData.event_id, 'true');
        }
    }
});
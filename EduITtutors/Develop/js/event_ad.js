document.addEventListener('DOMContentLoaded', function () {
    fetchActiveEvent(); // Always check every time page loads

    // Close button
    const closeBtn = document.getElementById('closePopup');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            const popup = document.getElementById('eventDiscountPopup');
            if (popup) {
                popup.style.display = 'none';
                document.body.style.overflow = '';  // Restore scrolling
            }
        });
    }
});

function fetchActiveEvent() {
    fetch('get_active_event.php')
        .then(response => response.json())
        .then(data => {
            if (data.active && data.event) {
                showEventPopup(data.event);
            }
        })
        .catch(error => console.error('Error fetching event:', error));
}

function showEventPopup(event) {
    const popup = document.getElementById('eventDiscountPopup');
    const banner = document.getElementById('eventDiscountBanner');
    const title = document.getElementById('eventDiscountTitle');
    const description = document.getElementById('eventDiscountDescription');
    const percentage = document.getElementById('eventDiscountPercentage');
    const endDate = document.getElementById('eventDiscountEnd');

    if (!popup) return;

    // Format end date
    const endDateObj = new Date(event.end_datetime);
    const formattedEndDate = endDateObj.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Fill content
    if (banner) banner.style.backgroundImage = `url(admin/${event.banner_image})`;
    if (title) title.textContent = event.event_name;
    if (description) description.textContent = event.event_description;
    if (percentage) percentage.textContent = `${event.discount_percentage}% OFF`;
    if (endDate) endDate.textContent = `Offer ends: ${formattedEndDate}`;

    popup.style.display = 'flex';

    // Disable page scroll when popup is visible
    document.body.style.overflow = 'hidden';
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle card tabs for all cards
    document.querySelectorAll('.card').forEach(card => {
        const buttons = card.querySelectorAll(".card-buttons button");
        const sections = card.querySelectorAll(".card-section");
        
        const handleButtonClick = (e) => {
            const targetSection = e.target.getAttribute("data-section");
            const section = card.querySelector(targetSection);
            
            // Update card state
            card.setAttribute("data-state", targetSection);
            
            // Toggle active classes
            sections.forEach((s) => s.classList.remove("is-active"));
            buttons.forEach((b) => b.classList.remove("is-active"));
            
            e.target.classList.add("is-active");
            section.classList.add("is-active");
        };
        
        buttons.forEach(btn => {
            btn.addEventListener("click", handleButtonClick);
        });
    });
    
    // Delete teacher functionality
    document.querySelectorAll('.delete-teacher').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this teacher?')) {
                const teacherId = this.getAttribute('data-id');
                window.location.href = 'teacher_delete.php?id=' + teacherId;
            }
        });
    });
});
<div id="chatbot">
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <h3 class="text-white">EduITtutors <i class="fa-solid fa-globe"></i></h3>
            <button class="chatbot-close" id="chatbotClose">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <!-- Messages will be inserted here -->
        </div>
        <div class="quick-replies" id="quickReplies">
            <button class="quick-reply" data-message="What webinars are coming up?">Upcoming webinars</button>
            <button class="quick-reply" data-message="How can I register for a webinar?">Webinar registration</button>
            <button class="quick-reply" data-message="Introduce teacher John Doe">Teacher introduction</button>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbotInput" placeholder="Ask me about IT courses, webinars, or blogs..." autocomplete="off">
            <button id="chatbotSend"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <button class="chatbot-toggle" id="chatbotToggle">
        <img src="photo/logo/EduITtutors_Blackver_Logo.png" alt="">
    </button>
</div>
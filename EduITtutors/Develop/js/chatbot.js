document.addEventListener('DOMContentLoaded', function () {
    // Chatbot elements
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const chatbotClose = document.getElementById('chatbotClose');
    const chatbotMessages = document.getElementById('chatbotMessages');
    const chatbotInput = document.getElementById('chatbotInput');
    const chatbotSend = document.getElementById('chatbotSend');
    const quickReplies = document.getElementById('quickReplies');

    // Function to fetch webinars data
    async function getWebinars() {
        try {
            const response = await fetch('get_webinars.php');
            return await response.json();
        } catch (error) {
            console.error('Error fetching webinars:', error);
            return [];
        }
    }

    // Initialize with welcome message if window is open by default
    if (chatbotWindow.classList.contains('active') && chatbotMessages.children.length === 0) {
        initializeWelcomeMessages();
    }

    // Toggle chatbot window
    chatbotToggle.addEventListener('click', async function () {
        chatbotWindow.classList.toggle('active');
        if (chatbotWindow.classList.contains('active') && chatbotMessages.children.length === 0) {
            initializeWelcomeMessages();
        }
    });

    chatbotClose.addEventListener('click', function () {
        chatbotWindow.classList.remove('active');
    });

    // Send message when button is clicked
    chatbotSend.addEventListener('click', sendMessage);

    // Send message when Enter is pressed
    chatbotInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Quick replies
    document.querySelectorAll('.quick-reply').forEach(button => {
        button.addEventListener('click', function () {
            const message = this.getAttribute('data-message');
            addUserMessage(message);
            chatbotInput.value = '';
            setTimeout(() => {
                processMessage(message);
            }, 500);
        });
    });

    // Function to initialize welcome messages
    function initializeWelcomeMessages() {
        addBotMessage("Hello! I'm the EduITtutors assistant. How can I help you today? I can:");
        addBotMessage("- Answer IT questions (programming, AI, cybersecurity, etc.)");
        addBotMessage("- Help you find courses");
        addBotMessage("- Recommend blogs");
        addBotMessage("- Provide information about webinars");
        addBotMessage("- Connect you with department information");
        addBotMessage("- Share teacher profiles");
    }

    // Function to add a user message
    function addUserMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message user-message';
        messageDiv.textContent = text;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Function to add a bot message
    function addBotMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        messageDiv.innerHTML = text; // Using innerHTML to allow for links
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Function to show typing indicator
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        chatbotMessages.appendChild(typingDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        return typingDiv;
    }

    // Function to hide typing indicator
    function hideTypingIndicator(typingDiv) {
        if (typingDiv && typingDiv.parentNode) {
            typingDiv.parentNode.removeChild(typingDiv);
        }
    }

    // Function to send a message
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (message) {
            addUserMessage(message);
            chatbotInput.value = '';

            // Show typing indicator
            const typingDiv = showTypingIndicator();

            // Process the message after a short delay
            setTimeout(() => {
                hideTypingIndicator(typingDiv);
                processMessage(message);
            }, 1000);
        }
    }

    // Function to process the user's message and generate a response
    async function processMessage(message) {
        const lowerMessage = message.toLowerCase();

        // Check for greetings
        if (lowerMessage.includes('hi') || lowerMessage.includes('hello') || lowerMessage.includes('hey')) {
            addBotMessage("Hello there! How can I assist you with EduITtutors today?");
            return;
        }

        // Check for thank you
        if (lowerMessage.includes('thank') || lowerMessage.includes('thanks')) {
            addBotMessage("You're welcome! Is there anything else I can help you with?");
            return;
        }

        // Check for home page information
        if (lowerMessage.includes('home') || lowerMessage.includes('main page') ||
            lowerMessage.includes('landing page') || lowerMessage.includes('welcome page')) {
            addBotMessage("Our home page is your gateway to all our IT education resources! Here's what you'll find:<br><br>" +
                "🏠 <strong>Key sections:</strong><br>" +
                "- Featured courses and programs<br>" +
                "- Upcoming webinars and events<br>" +
                "- Success stories from our students<br>" +
                "- Latest blog posts and resources<br><br>" +
                "You can visit our <a href='index.php' style='color: #4a6bff;'>home page</a> to explore all these offerings.");
            return;
        }

        // Check for about page information
        if (lowerMessage.includes('about') || lowerMessage.includes('about us') ||
            lowerMessage.includes('who we are') || lowerMessage.includes('mission') || lowerMessage.includes('who you are')) {
            addBotMessage("EduITtutors is a premier IT education platform dedicated to:<br><br>" +
                "🎯 <strong>Our Mission:</strong><br>" +
                "- Providing high-quality IT education<br>" +
                "- Empowering students with practical skills<br>" +
                "- Connecting learners with industry experts<br><br>" +
                "🌟 <strong>What makes us special:</strong><br>" +
                "- Experienced instructors with real-world expertise<br>" +
                "- Hands-on, project-based learning approach<br>" +
                "- Flexible learning options for all schedules<br><br>" +
                "Learn more on our <a href='about.php' style='color: #4a6bff;'>about page</a> or ask me specific questions!");
            return;
        }

        // Check for location information
        if (lowerMessage.includes('where') || lowerMessage.includes('location') ||
            lowerMessage.includes('address') || lowerMessage.includes('find us')) {
            addBotMessage("EduITtutors is located at:<br><br>" +
                "📍 <strong>No 34, Kannar Road, Yangon</strong><br><br>" +
                "You can also contact us at:<br>" +
                "📧 Email: EduITtutors@edu.com<br>" +
                "📞 Phone: +959 123 456 789<br><br>" +
                "Visit our <a href='contact.php' style='color: #4a6bff;'>contact page</a> for more information.");
            return;
        }

        // Check for department information
        if (lowerMessage.includes('department') || lowerMessage.includes('subject') ||
            lowerMessage.includes('field') || lowerMessage.includes('area')) {

            // Show typing indicator while fetching data
            const typingDiv = showTypingIndicator();

            // Fetch departments from database
            fetch('get_departments.php')
                .then(response => response.json())
                .then(departments => {
                    hideTypingIndicator(typingDiv);

                    if (departments.length > 0) {
                        let response = "We offer courses in these departments:<br><br>";
                        departments.forEach(dept => {
                            response += `
                                <strong>${dept.Department_Name}</strong><br>
                                ${dept.Description}<br>
                                <a href="department.php?id=${dept.Department_ID}" style="color: #4a6bff; text-decoration: none;">Learn more →</a>
                                <br><br>
                            `;
                        });
                        response += "You can explore all our <a href='departments.php' style='color: #4a6bff;'>departments here</a>.";
                        addBotMessage(response);
                    } else {
                        addBotMessage("Our departments include Programming, Data Science, Cybersecurity, AI, and more. You can explore all our <a href='departments.php' style='color: #4a6bff;'>departments here</a>.");
                    }
                })
                .catch(error => {
                    hideTypingIndicator(typingDiv);
                    addBotMessage("Our departments include Programming, Data Science, Cybersecurity, AI, and more. You can explore all our <a href='departments.php' style='color: #4a6bff;'>departments here</a>.");
                    console.error('Error fetching departments:', error);
                });
            return;
        }

        // Check for teacher information
        if (lowerMessage.includes('teacher') || lowerMessage.includes('instructor') ||
            lowerMessage.includes('mentor') || lowerMessage.includes('professor')) {

            // Show typing indicator while fetching data
            const typingDiv = showTypingIndicator();

            // Fetch teachers from database
            fetch('get_teachers.php')
                .then(response => response.json())
                .then(teachers => {
                    hideTypingIndicator(typingDiv);

                    if (teachers.length > 0) {
                        // If asking about specific teacher
                        const specificTeacher = teachers.find(teacher =>
                            lowerMessage.includes(teacher.Teacher_Name.toLowerCase())
                        );

                        if (specificTeacher) {
                            let response = `
                                <strong>${specificTeacher.Teacher_Name}</strong><br>
                                ${specificTeacher.Teacher_Bio}<br><br>
                                <strong>Expertise:</strong> ${specificTeacher.Expertise_Text}<br>
                                <strong>Experience:</strong> ${specificTeacher.Experience_Text}<br><br>
                                <a href="teacherdetail.php?id=${specificTeacher.Teacher_ID}" style="color: #4a6bff; text-decoration: none;">View full profile →</a>
                            `;
                            addBotMessage(response);
                        } else {
                            let response = "Our expert instructors include:<br><br>";
                            // Show 5 random teachers
                            const shuffled = teachers.sort(() => 0.5 - Math.random());
                            const selected = shuffled.slice(0, 5);

                            selected.forEach(teacher => {
                                response += `
                                    <strong>${teacher.Teacher_Name}</strong><br>
                                    ${teacher.Teacher_Role === 'main' ? '👑 Head Mentor' : '👨‍🏫 Instructor'} - ${teacher.Teacher_Bio.substring(0, 100)}...<br>
                                    <a href="teacherdetail.php?id=${teacher.Teacher_ID}" style="color: #4a6bff; text-decoration: none;">View profile →</a>
                                    <br><br>
                                `;
                            });
                            response += "You can browse all our <a href='departments.php' style='color: #4a6bff;'>instructors in departments.</a> You can find them..";
                            addBotMessage(response);
                        }
                    } else {
                        addBotMessage("We have a team of experienced instructors across all IT disciplines. You can learn more about our <a href='departments.php' style='color: #4a6bff;'>teachers here</a>.");
                    }
                })
                .catch(error => {
                    hideTypingIndicator(typingDiv);
                    addBotMessage("We have a team of experienced instructors across all IT disciplines. You can learn more about our <a href='departments.php' style='color: #4a6bff;'>teachers here</a>.");
                    console.error('Error fetching teachers:', error);
                });
            return;
        }


        // Check for webinar-related questions
        if (lowerMessage.includes('webinar') || lowerMessage.includes('event') ||
            lowerMessage.includes('upcoming') || lowerMessage.includes('schedule')) {

            const webinars = await getWebinars();

            if (webinars.length > 0) {
                let response = "Here are our upcoming webinars:<br><br>";
                webinars.forEach(webinar => {
                    const formattedDate = new Date(webinar.webinar_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    response += `
                        <strong>${webinar.title}</strong><br>
                        <small>📅 ${formattedDate} | ⏰ ${webinar.time_schedule}</small><br>
                        <a href="webinar_detail.php?id=${webinar.webinar_id}" style="color: #4a6bff; text-decoration: none;">Learn more →</a>
                        <br><br>
                    `;
                });

                response += "You can register for any of these webinars by clicking 'Learn more' and following the instructions.";
                addBotMessage(response);
            } else {
                addBotMessage("Currently, there are no upcoming webinars scheduled. Please check back later or explore our <a href='course.php' style='color: #4a6bff;'>courses</a> in the meantime.");
            }
            return;
        }

        // Check for course-related questions
        if (lowerMessage.includes('course') || lowerMessage.includes('class') ||
            lowerMessage.includes('learn') || lowerMessage.includes('study')) {

            // Check for specific course topics
            if (lowerMessage.includes('cyber') || lowerMessage.includes('security')) {
                addBotMessage("We offer several cybersecurity courses:<br><br>" +
                    "1. <strong>Advanced Tech Cybersecurity Practices</strong> - 12 weeks, $1200<br>" +
                    "2. <strong>Cybersecurity Fundamentals for Beginners</strong> - 6 weeks, $800<br><br>" +
                    "You can browse all our <a href='course.php' style='color: #4a6bff;'>courses here</a>.");
            } else if (lowerMessage.includes('cloud')) {
                addBotMessage("We have a great course on cloud computing:<br><br>" +
                    "<strong>Cloud Computing Essentials</strong> - 8 weeks, $900<br><br>" +
                    "This course covers cloud service models, deployment strategies, and hands-on practice with major platforms. <a href='course.php' style='color: #4a6bff;'>View all courses</a>.");
            } else if (lowerMessage.includes('ai') || lowerMessage.includes('artificial') || lowerMessage.includes('machine learning')) {
                addBotMessage("Our AI and Machine Learning courses include:<br><br>" +
                    "1. <strong>Introduction to Artificial Intelligence</strong> - 10 weeks, $1200<br>" +
                    "2. <strong>Machine Learning Fundamentals</strong> - 12 weeks, $1400<br><br>" +
                    "Explore these and more on our <a href='course.php' style='color: #4a6bff;'>courses page</a>.");
            } else if (lowerMessage.includes('web') || lowerMessage.includes('frontend') || lowerMessage.includes('backend')) {
                addBotMessage("For web development, we recommend:<br><br>" +
                    "<strong>Web Development Bootcamp</strong> - 12 weeks, $1500<br><br>" +
                    "This comprehensive course covers HTML, CSS, JavaScript, and more. <a href='course.php' style='color: #4a6bff;'>See all web development courses</a>.");
            } else {
                addBotMessage("We offer a wide range of IT courses in programming, data science, cybersecurity, cloud computing, and more. You can browse all our <a href='course.php' style='color: #4a6bff;'>courses here</a>.");
            }
            return;
        }

        // Check for blog-related questions
        if (lowerMessage.includes('blog') || lowerMessage.includes('article') ||
            lowerMessage.includes('read') || lowerMessage.includes('resource')) {

            // Check for specific blog topics
            if (lowerMessage.includes('interview') || lowerMessage.includes('job')) {
                addBotMessage("We have a great blog post about IT job interviews:<br><br>" +
                    "<strong>How to Prepare for an IT Job Interview: A Complete Guide for Students</strong><br>" +
                    "This covers technical knowledge, behavioral questions, and practical tips. <a href='blog.php' style='color: #4a6bff;'>Read it here</a>.");
            } else if (lowerMessage.includes('certif') || lowerMessage.includes('cert')) {
                addBotMessage("Check out our blog post:<br><br>" +
                    "<strong>The Importance of IT Certifications for Students: Boost Your Career Early</strong><br>" +
                    "It explains why certifications matter and which ones to consider. <a href='blog.php' style='color: #4a6bff;'>Find it here</a>.");
            } else if (lowerMessage.includes('time') || lowerMessage.includes('manage') || lowerMessage.includes('product')) {
                addBotMessage("We have a helpful blog post about time management:<br><br>" +
                    "<strong>Time Management Tips for Online IT Learners: Stay Productive and Focused</strong><br>" +
                    "Perfect for students balancing studies with other commitments. <a href='blog.php' style='color: #4a6bff;'>Read it here</a>.");
            } else {
                addBotMessage("Our blog covers various IT topics from career guidance to technical tutorials. You can explore all our <a href='blog.php' style='color: #4a6bff;'>blogs here</a>.");
            }
            return;
        }

        // Check for IT knowledge questions
        if (lowerMessage.includes('what is') || lowerMessage.includes('explain') ||
            lowerMessage.includes('define') || lowerMessage.includes('tell me about')) {

            if (lowerMessage.includes('cloud')) {
                addBotMessage("Cloud computing is the delivery of computing services (servers, storage, databases, networking, software) over the internet ('the cloud'). Instead of owning physical hardware, users access these resources on demand from providers like AWS, Azure, or Google Cloud.<br><br>" +
                    "Key benefits include:<br>" +
                    "- Cost efficiency (pay-as-you-go)<br>" +
                    "- Scalability (easily adjust resources)<br>" +
                    "- Reliability (redundant infrastructure)<br>" +
                    "- Global accessibility<br><br>" +
                    "Common service models:<br>" +
                    "- IaaS (Infrastructure as a Service)<br>" +
                    "- PaaS (Platform as a Service)<br>" +
                    "- SaaS (Software as a Service)<br><br>" +
                    "We have a detailed blog post explaining cloud computing: <a href='blog.php' style='color: #4a6bff;'>What is Cloud Computing? A Beginner's Guide</a>");
            } else if (lowerMessage.includes('cyber') || lowerMessage.includes('security')) {
                addBotMessage("Cybersecurity refers to protecting systems, networks, and data from digital attacks. It involves preventing threats like:<br>" +
                    "- Malware (viruses, ransomware)<br>" +
                    "- Phishing (fraudulent emails)<br>" +
                    "- Hacking (unauthorized access)<br>" +
                    "- DDoS attacks (overwhelming systems)<br><br>" +
                    "Key principles (CIA Triad):<br>" +
                    "- Confidentiality (protecting data privacy)<br>" +
                    "- Integrity (ensuring data accuracy)<br>" +
                    "- Availability (maintaining access)<br><br>" +
                    "Career paths in cybersecurity:<br>" +
                    "- Security Analyst<br>" +
                    "- Ethical Hacker/Penetration Tester<br>" +
                    "- Security Architect<br>" +
                    "- Chief Information Security Officer (CISO)<br><br>" +
                    "Learn more from our blog: <a href='blog.php' style='color: #4a6bff;'>Cybersecurity Basics Every Student Should Know</a>");
            } else if (lowerMessage.includes('ai') || lowerMessage.includes('artificial') || lowerMessage.includes('machine learning')) {
                addBotMessage("Artificial Intelligence (AI) is the simulation of human intelligence in machines. Key concepts include:<br><br>" +
                    "<strong>Machine Learning</strong> (a subset of AI):<br>" +
                    "- Supervised learning (trained on labeled data)<br>" +
                    "- Unsupervised learning (finds patterns in unlabeled data)<br>" +
                    "- Reinforcement learning (learns through trial and error)<br><br>" +
                    "<strong>Deep Learning</strong> (a subset of ML):<br>" +
                    "- Uses neural networks with multiple layers<br>" +
                    "- Excels at image/speech recognition<br><br>" +
                    "Practical applications:<br>" +
                    "- Natural Language Processing (chatbots, translators)<br>" +
                    "- Computer Vision (facial recognition, medical imaging)<br>" +
                    "- Predictive Analytics (stock market, weather forecasting)<br><br>" +
                    "Our blog has a great introduction: <a href='blog.php' style='color: #4a6bff;'>Exploring Careers in IT: From Developer to Data Analyst</a>");
            } else if (lowerMessage.includes('programming') || lowerMessage.includes('coding')) {
                addBotMessage("Programming is the process of creating instructions for computers to execute. Key concepts include:<br><br>" +
                    "<strong>Popular Languages:</strong><br>" +
                    "- Python (general purpose, great for beginners)<br>" +
                    "- JavaScript (web development)<br>" +
                    "- Java (enterprise applications)<br>" +
                    "- C++ (system/performance-critical applications)<br>" +
                    "- SQL (database management)<br><br>" +
                    "<strong>Core Concepts:</strong><br>" +
                    "- Variables (storing data)<br>" +
                    "- Control structures (loops, conditionals)<br>" +
                    "- Functions (reusable code blocks)<br>" +
                    "- Object-oriented programming (classes, objects)<br>" +
                    "- Algorithms and data structures<br><br>" +
                    "Check out our blog: <a href='blog.php' style='color: #4a6bff;'>Top Programming Languages for Beginners</a>");
            } else if (lowerMessage.includes('data science') || lowerMessage.includes('data analysis')) {
                addBotMessage("Data Science is an interdisciplinary field that extracts knowledge and insights from structured and unstructured data. It combines:<br><br>" +
                    "- Statistics and mathematics<br>" +
                    "- Programming and database skills<br>" +
                    "- Domain expertise<br>" +
                    "- Data visualization<br><br>" +
                    "Key components:<br>" +
                    "- Data cleaning and preprocessing<br>" +
                    "- Exploratory data analysis<br>" +
                    "- Machine learning modeling<br>" +
                    "- Results interpretation<br><br>" +
                    "Common tools:<br>" +
                    "- Python (Pandas, NumPy, Scikit-learn)<br>" +
                    "- R programming<br>" +
                    "- SQL databases<br>" +
                    "- Tableau/Power BI for visualization<br><br>" +
                    "Learn more: <a href='course.php' style='color: #4a6bff;'>Explore our Data Science courses</a>");
            } else if (lowerMessage.includes('devops')) {
                addBotMessage("DevOps is a set of practices that combines software development (Dev) and IT operations (Ops) to shorten the systems development life cycle.<br><br>" +
                    "Key principles:<br>" +
                    "- Continuous Integration/Continuous Deployment (CI/CD)<br>" +
                    "- Infrastructure as Code<br>" +
                    "- Monitoring and logging<br>" +
                    "- Microservices architecture<br><br>" +
                    "Popular tools:<br>" +
                    "- Docker and Kubernetes (containerization)<br>" +
                    "- Jenkins, GitHub Actions (CI/CD)<br>" +
                    "- Terraform, Ansible (infrastructure automation)<br>" +
                    "- Prometheus, Grafana (monitoring)<br><br>" +
                    "Benefits:<br>" +
                    "- Faster time to market<br>" +
                    "- Improved collaboration<br>" +
                    "- Higher quality software<br>" +
                    "- More reliable releases<br><br>" +
                    "See our <a href='course.php' style='color: #4a6bff;'>DevOps courses</a> for hands-on training");
            } else if (lowerMessage.includes('blockchain') || lowerMessage.includes('crypto')) {
                addBotMessage("Blockchain is a decentralized, distributed ledger technology that records transactions across many computers. Key features:<br><br>" +
                    "- Decentralization (no central authority)<br>" +
                    "- Immutability (records cannot be altered)<br>" +
                    "- Transparency (all participants can verify)<br>" +
                    "- Security through cryptography<br><br>" +
                    "Applications beyond cryptocurrency:<br>" +
                    "- Smart contracts<br>" +
                    "- Supply chain tracking<br>" +
                    "- Digital identity verification<br>" +
                    "- Voting systems<br><br>" +
                    "Popular platforms:<br>" +
                    "- Ethereum (smart contracts)<br>" +
                    "- Hyperledger (enterprise solutions)<br>" +
                    "- Solana (high-speed transactions)<br><br>" +
                    "Learn more: <a href='blog.php' style='color: #4a6bff;'>Blockchain Fundamentals Explained</a>");
            } else if (lowerMessage.includes('iot') || lowerMessage.includes('internet of things')) {
                addBotMessage("The Internet of Things (IoT) refers to the network of physical objects embedded with sensors, software, and connectivity to exchange data.<br><br>" +
                    "Key components:<br>" +
                    "- Sensors and actuators<br>" +
                    "- Connectivity (WiFi, Bluetooth, LoRaWAN)<br>" +
                    "- Data processing (edge/cloud computing)<br>" +
                    "- User interfaces<br><br>" +
                    "Applications:<br>" +
                    "- Smart homes (thermostats, security)<br>" +
                    "- Wearable devices (fitness trackers)<br>" +
                    "- Industrial monitoring (predictive maintenance)<br>" +
                    "- Smart cities (traffic management)<br><br>" +
                    "Skills needed:<br>" +
                    "- Embedded systems programming<br>" +
                    "- Network protocols<br>" +
                    "- Data analytics<br>" +
                    "- Security considerations<br><br>" +
                    "Explore our <a href='course.php' style='color: #4a6bff;'>IoT courses</a>");
            } else if (lowerMessage.includes('web') || lowerMessage.includes('frontend') || lowerMessage.includes('backend')) {
                addBotMessage("Web development involves building websites and web applications. There are three main layers:<br><br>" +
                    "<strong>Frontend Development</strong> (client-side):<br>" +
                    "- HTML (structure)<br>" +
                    "- CSS (styling)<br>" +
                    "- JavaScript (interactivity)<br>" +
                    "- Frameworks like React, Vue, Angular<br><br>" +
                    "<strong>Backend Development</strong> (server-side):<br>" +
                    "- Server languages (Node.js, Python, PHP, Java)<br>" +
                    "- Databases (SQL, MongoDB)<br>" +
                    "- APIs (REST, GraphQL)<br>" +
                    "- Authentication/authorization<br><br>" +
                    "<strong>DevOps for Web</strong>:<br>" +
                    "- Hosting and deployment<br>" +
                    "- Performance optimization<br>" +
                    "- Security practices<br><br>" +
                    "Career paths:<br>" +
                    "- Frontend Developer<br>" +
                    "- Backend Developer<br>" +
                    "- Full Stack Developer<br>" +
                    "- UX/UI Designer<br><br>" +
                    "Start learning: <a href='course.php' style='color: #4a6bff;'>Web Development courses</a>");
            } else if (lowerMessage.includes('database') || lowerMessage.includes('sql') || lowerMessage.includes('nosql')) {
                addBotMessage("Databases are organized collections of data. Main types:<br><br>" +
                    "<strong>Relational Databases (SQL):</strong><br>" +
                    "- Structured data in tables<br>" +
                    "- Uses SQL (Structured Query Language)<br>" +
                    "- Examples: MySQL, PostgreSQL, SQL Server<br>" +
                    "- ACID properties (Atomicity, Consistency, Isolation, Durability)<br><br>" +
                    "<strong>NoSQL Databases:</strong><br>" +
                    "- Flexible schema for unstructured data<br>" +
                    "- Types: Document (MongoDB), Key-Value (Redis), Columnar (Cassandra), Graph (Neo4j)<br>" +
                    "- Scalable for big data applications<br><br>" +
                    "Key concepts:<br>" +
                    "- Normalization (SQL)<br>" +
                    "- Indexing for performance<br>" +
                    "- Transactions and concurrency<br>" +
                    "- Data modeling<br><br>" +
                    "Learn more: <a href='course.php' style='color: #4a6bff;'>Database courses</a>");
            } else {
                addBotMessage("I can explain various IT concepts including:<br><br>" +
                    "- Cloud Computing<br>" +
                    "- Cybersecurity<br>" +
                    "- AI & Machine Learning<br>" +
                    "- Programming Languages<br>" +
                    "- Data Science<br>" +
                    "- DevOps<br>" +
                    "- Blockchain<br>" +
                    "- IoT<br>" +
                    "- Web Development<br>" +
                    "- Databases<br><br>" +
                    "Could you specify which topic you're interested in? For example: 'Explain cloud computing' or 'What is DevOps?'");
            }
            return;
        }

        // Check for registration or signup questions
        if (lowerMessage.includes('register') || lowerMessage.includes('sign up') ||
            lowerMessage.includes('enroll') || lowerMessage.includes('join')) {

            if (lowerMessage.includes('webinar')) {
                if (webinars.length > 0) {
                    addBotMessage("To register for a webinar:<br><br>" +
                        "1. Visit our <a href='webinar.php' style='color: #4a6bff;'>webinars page</a><br>" +
                        "2. Click on the webinar you're interested in<br>" +
                        "3. Follow the registration instructions<br><br>" +
                        "Would you like me to list the upcoming webinars for you?");
                } else {
                    addBotMessage("Currently there are no webinars available for registration. Please check back later or explore our <a href='course.php' style='color: #4a6bff;'>courses</a>.");
                }
            } else if (lowerMessage.includes('course')) {
                addBotMessage("To enroll in a course:<br><br>" +
                    "1. Browse our <a href='course.php' style='color: #4a6bff;'>courses</a><br>" +
                    "2. Select the course you're interested in<br>" +
                    "3. Click 'Enroll Now' and follow the checkout process<br><br>" +
                    "Need help choosing a course? I can make recommendations based on your interests.");
            } else {
                addBotMessage("You can register for webinars or enroll in courses through our website. What would you like to register for?");
            }
            return;
        }

        // Check for account-related questions
        if (lowerMessage.includes('account') || lowerMessage.includes('profile') ||
            lowerMessage.includes('login') || lowerMessage.includes('sign in')) {

            if (window.userId && window.userId !== 'null') {
                addBotMessage("You're already logged in. You can manage your account:<br><br>" +
                    "- <a href='profilesetting.php' style='color: #4a6bff;'>Update your profile</a><br>" +
                    "- <a href='store.php' style='color: #4a6bff;'>View your courses</a>");
            } else {
                addBotMessage("You can <a href='login.php' style='color: #4a6bff;'>login here</a> to access your courses and profile. Don't have an account yet? <a href='login.php' style='color: #4a6bff;'>Sign up</a> to get started!");
            }
            return;
        }

        // Check for contact information
        if (lowerMessage.includes('contact') || lowerMessage.includes('email') ||
            lowerMessage.includes('phone') || lowerMessage.includes('address')) {

            addBotMessage("You can contact EduITtutors at:<br><br>" +
                "📧 Email: EduITtutors@edu.com<br>" +
                "📞 Phone: +959 123 456 789<br>" +
                "📍 Address: No 34, Kannar Road, Yangon<br><br>" +
                "Or visit our <a href='contact.php' style='color: #4a6bff;'>contact page</a> for more options.");
            return;
        }

        // Check for app download
        if (lowerMessage.includes('app') || lowerMessage.includes('mobile') ||
            lowerMessage.includes('download')) {

            addBotMessage("You can download the EduITtutors mobile app from:<br><br>" +
                "- <a href='https://play.google.com/store/apps/details?id=com.eduit.tutors' style='color: #4a6bff;' target='_blank'>Google Play Store</a><br>" +
                "- <a href='https://apps.apple.com/app/id1234567890' style='color: #4a6bff;' target='_blank'>Apple App Store</a>");
            return;
        }

        // Default response for unrecognized messages
        addBotMessage("I'm not sure I understand. I can help with:<br><br>" +
            "- IT topics (cloud, cybersecurity, AI, etc.)<br>" +
            "- Finding courses<br>" +
            "- Recommending blogs<br>" +
            "- Webinar information<br>" +
            "- Department and teacher information<br>" +
            "- Account help<br><br>" +
            "Could you rephrase your question or be more specific?");
    }
});
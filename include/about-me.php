<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meet the Developer | University Name</title>
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons for social links -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Use the Inter font family defined in the <link> tag */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="container mx-auto px-4 py-8 md:py-16">

        <!-- Page Header -->
        <header class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800">Meet the Developer</h1>
            <p class="mt-2 text-lg text-slate-600">The mind behind the university's digital experience.</p>
        </header>

        <!-- Main Profile Card -->
        <main class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 sm:p-10">
                <!-- Flex container for responsive layout -->
                <div class="flex flex-col md:flex-row items-center md:items-start text-center md:text-left gap-8">

                    <!-- Left Column: Image and Social Links -->
                    <div class="flex-shrink-0 w-full md:w-1/3">
                        <!-- Developer Image -->
                        <img 
                            src="https://media.licdn.com/dms/image/v2/D5603AQFJiTBZG7TNrA/profile-displayphoto-shrink_800_800/profile-displayphoto-shrink_800_800/0/1680520499482?e=1762387200&v=beta&t=Nayx8rY-NJdyfZahYwJnziavTLMumZHl60DyRe_naL0 
                            alt="Vihanga Manodhya" 
                            class="rounded-full w-40 h-40 sm:w-48 sm:h-48 object-cover mx-auto border-4 border-white shadow-lg"
                        >
                        
                        <h2 class="mt-5 text-2xl font-bold text-gray-900">Vihanga Manodhya</h2>
                        <p class="mt-1 text-blue-600 font-semibold">Lead Student Developer</p>
                        
                        <!-- Social Media Links -->
                        <div class="mt-5 flex justify-center md:justify-start gap-4">
                            <a href="https://www.linkedin.com/in/vihangamanodhya/" class="text-gray-500 hover:text-blue-700 transition-colors" aria-label="LinkedIn Profile">
                                <i data-lucide="linkedin" class="w-6 h-6"></i>
                            </a>
                            <a href="https://github.com/Vihanga-manodhya" class="text-gray-500 hover:text-gray-900 transition-colors" aria-label="GitHub Profile">
                                <i data-lucide="github" class="w-6 h-6"></i>
                            </a>
                            <a href="https://www.google.com/search?q=vihanga+manodhya&ie=UTF-8" class="text-gray-500 hover:text-rose-500 transition-colors" aria-label="Portfolio Website">
                                <i data-lucide="globe" class="w-6 h-6"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Bio and Project Details -->
                    <div class="w-full md:w-2/3">
                        <!-- About Me Section -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 border-b-2 border-gray-100 pb-2">About Me</h3>
                            <p class="mt-4 text-gray-600 leading-relaxed">
                                I am a final-year Computer Science student at SLIATE with a passion for building intuitive, performant, and user-centric web applications. This website was developed as part of my final year project, aiming to create a modern digital portal for our university community.
                            </p>
                        </div>

                        <!-- Project Vision Section -->
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold text-gray-800 border-b-2 border-gray-100 pb-2">Project Vision</h3>
                            <p class="mt-4 text-gray-600 leading-relaxed">
                                The goal was to create a fast, accessible, and mobile-first platform for students, faculty, and prospective applicants. By leveraging modern web technologies, we aimed to deliver a seamless experience that reflects the innovative spirit of our university.
                            </p>
                        </div>
                        
                        <!-- Technology Stack Section -->
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold text-gray-800 border-b-2 border-gray-100 pb-2">Technology Stack</h3>
                            <div class="mt-4 flex flex-wrap gap-2 justify-center md:justify-start">
                                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">HTML5</span>
                                <span class="bg-sky-100 text-sky-800 text-sm font-medium px-3 py-1 rounded-full">Tailwind CSS</span>
                                <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">JavaScript</span>
                                <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">PHP</span>
                                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">MySQL</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer / Contact Section -->
        <footer class="text-center mt-16">
            <p class="text-slate-600">Have a question or a project in mind?</p>
            <a href="mailto:email@example.com" class="mt-4 inline-block bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-transform hover:scale-105">
                Get in Touch
            </a>
        </footer>

    </div>

    <script>
        // This script initializes the Lucide icons used in the social media links.
        lucide.createIcons();
    </script>

</body>
</html>

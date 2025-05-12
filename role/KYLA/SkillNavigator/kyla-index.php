<?php
// Main entry point for the application
require_once "config/kyla-constants.php";
require_once "includes/kyla-header.php";
require_once "config/kyla-database.php";
require_once "includes/kyla-auth.php";

// Redirect to appropriate dashboard if already logged in
if (is_logged_in()) {
    redirect_by_role();
}
?>

<div class="flex flex-col items-center justify-center">
    <div class="max-w-4xl text-center">
        <h1 class="text-3xl md:text-5xl font-bold mb-6 text-gray-800">Welcome to the Skill Development Tracker</h1>
        <p class="text-xl mb-8 text-gray-600">
            Monitor, measure, and improve your skills throughout your internship journey
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-blue-500 text-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">For Interns</h3>
                <p class="text-gray-600 mb-4">
                    Track your skill progress, set goals, and identify areas for improvement
                </p>
                <ul class="text-left text-gray-600 mb-4">
                    <li class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Self-assess your skills
                    </li>
                    <li class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Visualize your progress
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Get learning suggestions
                    </li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-purple-500 text-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto">
                        <path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">For Supervisors</h3>
                <p class="text-gray-600 mb-4">
                    Evaluate intern skills, provide feedback, and assign learning tasks
                </p>
                <ul class="text-left text-gray-600 mb-4">
                    <li class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Rate intern skills
                    </li>
                    <li class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Compare self vs. supervisor ratings
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Assign mentoring sessions
                    </li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-red-500 text-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto">
                        <line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">For HR/Admin</h3>
                <p class="text-gray-600 mb-4">
                    Generate reports, identify skill gaps, and monitor overall progress
                </p>
                <ul class="text-left text-gray-600 mb-4">
                    <li class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        View skill growth reports
                    </li>
                    <li class="flex items-center mb-1">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Identify top performers
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Export data for analysis
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="mt-8">
            <a href="kyla-login.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-300 inline-block">
                Login to Get Started
            </a>
        </div>
    </div>
</div>

<?php
require_once "includes/kyla-footer.php";
?>

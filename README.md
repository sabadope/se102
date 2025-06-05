# A WEB-BASED SOLUTION FOR MONITORING AND EVALUATING STUDENT INTERN PERFORMANCE

To develop a web-based platform that modernizes how companies monitor and evaluate intern performance by providing a fair, transparent, and data-driven evaluation system.

<h3 align="center">KEY CONCEPTS OF THE SYSTEM</h3>
<br>

<div align="center">

<ul align="left">
  <li><strong>Real-time Monitoring</strong><br>Track intern activities and performance in real-time through digital logs and reports.</li>
  <li><strong>Data-Driven Evaluation</strong><br>Use system-generated data and analytics to assess intern productivity and outcomes.</li>
  <li><strong>Multi-Perspective Feedback</strong><br>Collect insights from supervisors, HR, and system logs for a well-rounded evaluation.</li>
  <li><strong>Smart Decision Support</strong><br>Help companies identify high-performing interns for potential employment.</li>
  <li><strong>Scalable and Feasible</strong><br>Designed to be practical for small teams and adaptable for wider institutional use.</li>
</ul>

</div>

---

<h3 align="center">HOW TO SETUP</h3>
<br>

To run the system locally or deploy it for testing, follow the steps below:

### Requirements
- PHP >= 7.4 (or higher)
- MySQL
- Web Server (e.g., XAMPP, MAMP, WAMP, Apache/Nginx)

### Setup Procedure
1. Extract the downloaded file.
2. Copy the main project folder.
3. Paste it inside your `xampp/htdocs/` directory.
4. Open your browser and go to: [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
5. Click on the **Databases** tab.
6. Create the following databases:
   - `se102`
   - `chat_app_db`
   - `feedback_system`
   - `intern_logs`
   - `naattendance`
   - `skill_tracker`

7. Import the following SQL files:
   - For those following databases, just click the **import** tab from the mysql server, after that locate each sql files inside of the `se102/database` folder.
   
8. Click the **Go** button to complete the import.
9. Now open your browser and visit: [http://localhost/se102/index.html](http://localhost/se102/index.html)

   > **To start running the program.**

---

### Default Login Credentials

**Admin**  
email: `admin@gmail.com`  
pass: `admin1230`

**Supervisor**  
email: `supervisor@gmail.com`  
pass: `supervisor1230`

> For **Student** and **Client**, please register manually through the system registration page.

---

### THAT'S ALL! I hope you followed the steps correctly and enjoy using the program!


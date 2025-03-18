<link rel="stylesheet" href="css/fontawesome/css/all.min.css" media="screen">

<style>
    .bg-black-300 {
        background-color: #2d545e;
    }
</style>
<div class="left-sidebar bg-black-300 box-shadow ">
    <div class="sidebar-content">
        <div class="user-info closed">
            <img src="uploads/arimi.jpg" alt="Arimi Bonface" class="img-circle profile-img">
            <h6 class="title">Arimi Bonface</h6>
            <small class="info">PHP Developer</small>
        </div>
        <!-- /.user-info -->

        <div class="sidebar-nav">
            <ul class="side-nav color-gray">
                <li class="nav-header">
                    <span>Main Category</span>
                </li>
                <li>
                    <a href="dashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                </li>

                <li class="nav-header">
                    <span>Appearance</span>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-user-graduate"></i> <span>Students</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="add-students.php"><i class="fa fa-user-plus"></i> <span>Add Students</span></a></li>
                        <li><a href="manage-students.php"><i class="fa fa-users-cog"></i> <span>Manage Students</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-bank"></i> <span>Classes</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="create-class.php"><i class="fa fa-plus-circle"></i> <span>Create Class</span></a></li>
                        <li><a href="manage-classes.php"><i class="fa fa-list"></i> <span>Manage Classes</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-book"></i> <span>Subjects</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="create-subject.php"><i class="fa fa-plus"></i> <span>Create Subject</span></a></li>
                        <li><a href="manage-subjects.php"><i class="fa fa-cogs"></i> <span>Manage Subjects</span></a></li>
                        <li><a href="create-subject-combination.php"><i class="fa fa-code-branch"></i> <span>Create Subject Combination Code</span></a></li>
                        <li><a href="add-subjectcombination.php"><i class="fa fa-plus-square"></i> <span>Add Subject Combination</span></a></li>
                        <li><a href="manage-subjectcombination.php"><i class="fa fa-th-list"></i> <span>Manage Subject Combination</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-calendar-alt"></i> <span>TimeTable</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="add-timetable-entry.php"><i class="fa fa-calendar-plus"></i> <span>Add TimeTable Entry</span></a></li>
                        <li><a href="manage-timetable.php"><i class="fa fa-calendar-check"></i> <span>Manage Timetable Entry</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-calendar-day"></i> <span>Attendance</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="view-attendance.php"><i class="fa fa-eye"></i> <span>View Attendance</span></a></li>
                        <li><a href="mark-attendance.php"><i class="fa fa-pencil-alt"></i> <span>Mark Attendance</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-clock"></i> <span>School Period</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="add-school-period.php"><i class="fa fa-clock"></i> <span>Add School Period</span></a></li>
                        <li><a href="manage-school-period.php"><i class="fa fa-cogs"></i> <span>Manage School Period</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-pen"></i> <span>Exam</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="add-exam-period.php"><i class="fa fa-calendar-plus"></i> <span>Add Exam Period</span></a></li>
                        <li><a href="manage-exam-period.php"><i class="fa fa-calendar-check"></i> <span>Manage Exam Period</span></a></li>
                        <li><a href="exam-results.php"><i class="fa fa-chart-line"></i> <span>Exam Results</span></a></li>
                    </ul>
                </li>
                <li class="has-children">
                    <a href="#"><i class="fa fa-chalkboard-teacher"></i> <span>Teachers</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="add-teachers.php"><i class="fa fa-user-plus"></i> <span>Add Teachers</span></a></li>
                        <li><a href="manage-teachers.php"><i class="fa fa-users-cog"></i> <span>Manage Teachers</span></a></li>
                    </ul>
                </li>

                <li class="has-children">
                    <a href="#"><i class="fa fa-envelope"></i> <span>Messages</span> <i class="fa fa-angle-right arrow"></i></a>
                    <ul class="child-nav">
                        <li><a href="add-recipients.php"><i class="fa fa-paper-plane"></i> <span>Send Message</span></a></li>
                        <li><a href="manage-messages.php"><i class="fa fa-inbox"></i> <span>Manage Messages</span></a></li>
                    </ul>
                </li>
                <li>
                    <a href="change-password.php"><i class="fa fa-lock"></i> <span>Admin Change Password</span></a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-nav -->
    </div>
    <!-- /.sidebar-content -->
</div>
<!-- /.left-sidebar -->

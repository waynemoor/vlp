// =====================
// Admin Dashboard Script
// =====================

// ============================
// AUTO-REFRESH ALL SECTIONS
// ============================

// Refresh interval in milliseconds
const REFRESH_INTERVAL = 5000; // 5 seconds

setInterval(() => {
    // Students
    if (document.getElementById('students').style.display === 'block') {
        loadStudentsAdmin();
    }

    // Lecturers
    if (document.getElementById('lecturers').style.display === 'block') {
        loadLecturersAdmin();
    }

    // Communities
    if (document.getElementById('communities').style.display === 'block') {
        loadCommunitiesAdmin();
    }

    // Performance
    if (document.getElementById('performance').style.display === 'block') {
        const course = document.getElementById('filter-course').value;
        const student = document.getElementById('filter-student').value;
        loadPerformanceData(course, student);
    }

    // Notifications
    if (document.getElementById('notifications').style.display === 'block') {
        loadNotifications();
    }

    // Dashboard Overview (optional, refresh every interval)
    if (document.getElementById('overview').style.display === 'block') {
        loadAdminOverview();
    }

    // Database tables (optional, refresh only when visible)
    if (document.getElementById('database').style.display === 'block') {
        loadTables();
    }

}, REFRESH_INTERVAL);


// Run after DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {

    // --- Navigation Toggle ---
    const navLinks = document.querySelectorAll('.nav-links a');
    const sections = document.querySelectorAll('section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section');

            // Show selected section, hide others
            sections.forEach(sec => {
                sec.style.display = (sec.id === sectionId) ? 'block' : 'none';
            });

            // Set active class on nav
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // --- Load Dashboard Overview ---
    loadAdminOverview();

    // --- Load Students Table ---
    loadStudentsAdmin();

    // --- Load Lecturers Table ---
    loadLecturersAdmin();

    // --- Load Communities Table ---
    loadCommunitiesAdmin();

    // --- Load Performance Records ---
    loadPerformanceData();

    // --- Load Notifications ---
    loadNotifications();

    // --- Load Database Tables ---
    loadTables();

}); // End of DOMContentLoaded

// =====================
// TOGGLE SIDEBAR MENU
// =====================
function toggleMenu() {
    const nav = document.querySelector('.nav-links');
    nav.classList.toggle('show');
}

// =====================
// DASHBOARD OVERVIEW
// =====================
async function loadAdminOverview() {
    const res = await fetch('admin_overview.php'); // PHP should return JSON stats
    const data = await res.json();

    // Update HTML with stats
    document.getElementById('total-students').innerText = data.total_students;
    document.getElementById('total-lecturers').innerText = data.total_lecturers;
    document.getElementById('total-assignments').innerText = data.total_assignments;
    document.getElementById('total-announcements').innerText = data.total_announcements;
}

// =====================
// STUDENT MANAGEMENT
// =====================
async function loadStudentsAdmin() {
    const res = await fetch('list_students.php');
    const students = await res.json();

    const tbody = document.getElementById('students-table-body');
    tbody.innerHTML = '';

    students.forEach(student => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${student.name}</td>
            <td>${student.email}</td>
            <td>${student.student_id}</td>
            <td>${student.program}</td>
            <td>
                <button onclick="editStudent(${student.id})">Edit</button>
                <button onclick="deleteStudent(${student.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Handle adding student
document.getElementById('add-student-form').addEventListener('submit', function(e){
    e.preventDefault(); // prevent page reload

    let formData = new FormData(this);

    fetch('add_student.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const statusDiv = document.getElementById('add-student-status');
        statusDiv.textContent = data.message;
        statusDiv.style.color = data.status === 'success' ? 'green' : 'red';

        if(data.status === 'success'){
            this.reset(); // clear form
            // Optionally, reload students table
            location.reload();
        }
    })
    .catch(err => console.error(err));
});

//ADDING LECTURER

// =====================
// LECTURER MANAGEMENT
// =====================

// Load lecturers table dynamically
async function loadLecturersAdmin() {
    try {
        const res = await fetch('list_lecturers.php');
        const lecturers = await res.json();

        const tbody = document.getElementById('lecturers-table-body');
        tbody.innerHTML = '';

        lecturers.forEach(lecturer => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${lecturer.name}</td>
                <td>${lecturer.email}</td>
                <td>${lecturer.employee_id}</td>
                <td>${lecturer.department}</td>
                <td>
                    <button onclick="editLecturer('${lecturer.id}')">Edit</button>
                    <button onclick="deleteLecturer('${lecturer.id}')">Delete</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error('Error loading lecturers:', err);
    }
}

// Handle Add Lecturer form submission
document.getElementById('add-lecturer-form').addEventListener('submit', function(e){
    e.preventDefault(); // prevent page reload

    const formData = new FormData(this);

    fetch('add_lecturer.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const statusDiv = document.getElementById('add-lecturer-status');
        statusDiv.textContent = data.message;
        statusDiv.style.color = data.status === 'success' ? 'green' : 'red';

        if(data.status === 'success'){
            this.reset(); // clear form
            loadLecturersAdmin(); // refresh table immediately
        }
    })
    .catch(err => console.error('Add lecturer error:', err));
});

// Placeholder edit/delete functions
function editLecturer(id) {
    alert(`Edit lecturer ${id} - implement form here`);
}
function deleteLecturer(id) {
    alert(`Delete lecturer ${id} - implement backend here`);
}

// =====================
// Auto-refresh lecturers table
// =====================
setInterval(() => {
    if(document.getElementById('lecturers').style.display === 'block'){
        loadLecturersAdmin();
    }
}, 5000); // refresh every 5 seconds


// Placeholder functions
function editStudent(id) { alert(`Edit student ${id} - implement form here`); }
function deleteStudent(id) { alert(`Delete student ${id} - implement deletion here`); }

// =====================
// LECTURER MANAGEMENT
// =====================
async function loadLecturersAdmin() {
    const res = await fetch('list_lecturers.php');
    const lecturers = await res.json();

    const tbody = document.getElementById('lecturers-table-body');
    tbody.innerHTML = '';

    lecturers.forEach(lecturer => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${lecturer.name}</td>
            <td>${lecturer.email}</td>
            <td>${lecturer.employee_id}</td>
            <td>${lecturer.department}</td>
            <td>
                <button onclick="editLecturer(${lecturer.id})">Edit</button>
                <button onclick="deleteLecturer(${lecturer.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

document.getElementById('add-lecturer-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('add_lecturer.php', { method: 'POST', body: formData });
    const json = await res.json();
    document.getElementById('add-lecturer-status').innerText = json.success || json.error;
    loadLecturersAdmin();
});

function editLecturer(id) { alert(`Edit lecturer ${id} - implement form here`); }
function deleteLecturer(id) { alert(`Delete lecturer ${id} - implement deletion here`); }

// =====================
// COMMUNITIES MANAGEMENT
// =====================
async function loadCommunitiesAdmin() {
    const res = await fetch('list_communities.php');
    const communities = await res.json();

    const tbody = document.getElementById('communities-table-body');
    tbody.innerHTML = '';

    communities.forEach(comm => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${comm.community_name}</td>
            <td>${comm.description || '-'}</td>
            <td>${comm.members_count}</td>
            <td>
                <button onclick="editCommunity(${comm.id})">Edit</button>
                <button onclick="deleteCommunity(${comm.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

document.getElementById('add-community-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('add_community.php', { method: 'POST', body: formData });
    const json = await res.json();
    document.getElementById('add-community-status').innerText = json.success || json.error;
    loadCommunitiesAdmin();
});

function editCommunity(id) { alert(`Edit community ${id} - implement form here`); }
function deleteCommunity(id) { alert(`Delete community ${id} - implement deletion here`); }

// =====================
// PERFORMANCE MONITORING
// =====================
async function loadPerformanceData(courseFilter = '', studentFilter = '') {
    const res = await fetch('list_performance.php');
    let data = await res.json();

    // Apply filters
    if (courseFilter) data = data.filter(d => d.course === courseFilter);
    if (studentFilter) data = data.filter(d => d.name.toLowerCase().includes(studentFilter.toLowerCase()) || d.student_id.includes(studentFilter));

    const tbody = document.getElementById('performance-table-body');
    tbody.innerHTML = '';

    data.forEach(record => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${record.name}</td>
            <td>${record.student_id}</td>
            <td>${record.course}</td>
            <td>${record.assignment}</td>
            <td>${record.score}</td>
            <td>${record.grade}</td>
        `;
        tbody.appendChild(tr);
    });
}

document.getElementById('apply-filters').addEventListener('click', () => {
    const course = document.getElementById('filter-course').value;
    const student = document.getElementById('filter-student').value;
    loadPerformanceData(course, student);
});

// =====================
// NOTIFICATIONS
// =====================
async function loadNotifications() {
    const res = await fetch('list_notifications.php');
    const notifications = await res.json();
    const ul = document.getElementById('notification-list');
    ul.innerHTML = '';
    notifications.forEach(n => {
        const li = document.createElement('li');
        li.textContent = `${n.created_at} - [${n.type}] ${n.message}`;
        ul.appendChild(li);
    });
}

document.getElementById('notification-form').addEventListener('submit', async function(e){
    e.preventDefault();
    const type = document.getElementById('notification-type').value;
    const message = document.getElementById('notification-message').value;

    const res = await fetch('send_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type, message })
    });

    const json = await res.json();
    document.getElementById('notification-status').innerText = json.success || json.error;
    
    if(json.success){
        document.getElementById('notification-message').value = '';
        loadNotifications();
    }
});

// =====================
// DATABASE TABLES MANAGEMENT
// =====================
async function loadTables() {
    const res = await fetch('admin/list_tables.php');
    const tables = await res.json();
    const ul = document.getElementById('tables-list');
    ul.innerHTML = '';
    tables.forEach(t => {
        const li = document.createElement('li');
        li.innerHTML = `<a href="#" onclick="viewTable('${t}')">${t}</a> 
                        <button onclick="deleteTable('${t}')">Delete</button>`;
        ul.appendChild(li);
    });
}

async function viewTable(tableName) {
    const res = await fetch(`admin/view_table.php?table=${tableName}`);
    const data = await res.json();
    const table = document.getElementById('table-rows');
    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');

    document.getElementById('table-data').style.display = 'block';
    document.getElementById('table-title').innerText = tableName;
    document.getElementById('tables-list').parentElement.style.display = 'none';

    thead.innerHTML = '';
    tbody.innerHTML = '';

    if (data.length === 0) return;

    const headers = Object.keys(data[0]);
    let trHead = document.createElement('tr');
    headers.forEach(h => trHead.innerHTML += `<th>${h}</th>`);
    trHead.innerHTML += `<th>Actions</th>`;
    thead.appendChild(trHead);

    data.forEach(row => {
        const tr = document.createElement('tr');
        headers.forEach(h => tr.innerHTML += `<td>${row[h]}</td>`);
        tr.innerHTML += `<td><button onclick="deleteRow('${tableName}', ${row.id})">Delete</button></td>`;
        tbody.appendChild(tr);
    });
}

// Placeholder table actions
function deleteTable(name){ alert(`Delete table ${name} - implement backend`);}
function deleteRow(table, id){ alert(`Delete row ${id} from ${table} - implement backend`);}

// SQL Console
document.getElementById('run-sql').addEventListener('click', async () => {
    const query = document.getElementById('sql-query').value;
    const res = await fetch('admin/run_sql.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({query})
    });
    const result = await res.text();
    document.getElementById('sql-result').innerText = result;
});

// System Logs
document.getElementById('refresh-logs').addEventListener('click', async () => {
    const res = await fetch('admin/view_logs.php');
    const logs = await res.text();
    document.getElementById('system-logs').innerText = logs;
});

// Back button for tables
document.getElementById('back-to-tables').addEventListener('click', () => {
    document.getElementById('table-data').style.display = 'none';
    document.getElementById('tables-list').parentElement.style.display = 'block';
});

document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-links a');
    const sections = document.querySelectorAll('section');

    navLinks.forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section');

            // Show/hide sections
            sections.forEach(sec => sec.style.display = (sec.id === sectionId) ? 'block' : 'none');

            // Set active nav link
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // Load dynamic content if necessary
            if(sectionId === 'assignments') loadAssignments();
            if(sectionId === 'announcements') loadStudentAnnouncements();
            if(sectionId === 'messages') loadMessages();
        });
    });
});

// ========================
// Assignments
// ========================


// Upload form  

document.getElementById('upload-assignment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
try {
    const res = await fetch('assignments.php', {
        method: 'POST',
        body: formData
    });

    const json = await res.json();

    if (json.success) {
        alert(json.success);
        this.reset();
    } else if (json.error) {
        alert("Upload failed Because: " + json.error + 
              (json.details ? "\nDetails: " + json.details : ""));
    } else {
        alert('Unexpected response from server.');
    }
} catch (e) {
    alert("Upload failed because: " + e.message);
    console.error("Upload error:", e);
}

});


// Initial load + auto-refresh every 10 seconds
loadAssignments();
setInterval(loadAssignments, 10000);


// ========================
// Messages
// ========================
async function loadMessages() {
    try {
        const res = await fetch('messages/list_messages.php'); // optional if you want to refresh
        const messages = await res.json();
        const container = document.getElementById('messages-container');
        container.innerHTML = '';
        messages.forEach(m => {
            const div = document.createElement('div');
            div.classList.add('message');
            div.innerHTML = `<span class="sender">${m.sender}:</span> <span class="text">${m.text}</span>`;
            container.appendChild(div);
        });
    } catch(e) {
        console.error('Failed to load messages:', e);
    }
}

document.getElementById('message-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = this.message;
    if(!input.value) return;
    try {
        await fetch('messages/send_message.php', {
            method: 'POST',
            body: JSON.stringify({ recipient_id: 0, message: input.value }),
            headers: { 'Content-Type': 'application/json' }
        });
        input.value = '';
        loadMessages();
    } catch(e) {
        console.error('Failed to send message:', e);
    }
});

// ========================
// Announcements
// ========================
async function loadStudentAnnouncements() {
    try {
        const res = await fetch('announcements/list_announcements.php');
        const data = await res.json();
        const ul = document.getElementById('student-announcements-list');
        ul.innerHTML = '';
        data.forEach(a => {
            const li = document.createElement('li');
            const date = new Date(a.created_at).toLocaleString();
            li.textContent = `${date} - ${a.message}`;
            ul.appendChild(li);
        });
    } catch(e) {
        console.error('Failed to load announcements:', e);
    }
}

// Load quiz questions for a selected quiz
async function loadQuiz(quiz_id){
    const res = await fetch(`get_quiz.php?quiz_id=${quiz_id}`);
    const questions = await res.json();
    const container = document.getElementById('quiz-questions-container');
    container.innerHTML = '';

    questions.forEach(q => {
        const div = document.createElement('div');
        div.innerHTML = `
            <p><strong>${q.question_text}</strong></p>
            <label><input type="radio" name="q${q.id}" value="A" required> ${q.option_a}</label><br>
            <label><input type="radio" name="q${q.id}" value="B"> ${q.option_b}</label><br>
            <label><input type="radio" name="q${q.id}" value="C"> ${q.option_c}</label><br>
            <label><input type="radio" name="q${q.id}" value="D"> ${q.option_d}</label><br>
            <hr>
        `;
        container.appendChild(div);
    });
}

// Submit quiz answers
document.getElementById('take-quiz-form').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const res = await fetch('submit_quiz.php', {method:'POST', body: formData});
    const result = await res.json();
    document.getElementById('quiz-result').innerText = `You scored ${result.score}/${result.total}`;
    loadAttempts(); // refresh previous attempts
});

// Load previous quiz attempts
async function loadAttempts(){
    const res = await fetch('get_results.php');
    const attempts = await res.json();
    const list = document.getElementById('quiz-attempts-list');
    list.innerHTML = '';
    attempts.forEach(a => {
        const li = document.createElement('li');
        li.innerText = `Quiz: ${a.title} | Score: ${a.score}/${a.total_questions}`;
        list.appendChild(li);
    });
}

// Auto-load previous attempts on dashboard load
document.addEventListener('DOMContentLoaded', loadAttempts);


//messages csript
const recipientType = document.getElementById('recipient-type');
const recipientId = document.getElementById('recipient-id');
const chatBox = document.getElementById('chat-box');
const messageForm = document.getElementById('message-form');

// Populate recipients dynamically
async function loadRecipients() {
    const type = recipientType.value;
    const res = await fetch(`get_recipients.php?type=${type}`);
    const users = await res.json();
    recipientId.innerHTML = '';
    users.forEach(u => {
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = u.name;
        recipientId.appendChild(opt);
    });
}

// Load messages for selected recipient
async function loadMessages() {
    const type = recipientType.value;
    const recv = recipientId.value;
    const res = await fetch(`get_messages.php?type=${type}&receiver_id=${recv}`);
    const msgs = await res.json();
    chatBox.innerHTML = msgs.map(m => `<div><b>${m.sender_name || 'Me'}:</b> ${m.message_text}</div>`).join('');
}

// Send a message
messageForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const type = recipientType.value;
    const recv = recipientId.value;
    const msg = messageForm.message.value;
    await fetch('send_message.php', {
        method:'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body:`type=${type}&receiver_id=${recv}&message=${encodeURIComponent(msg)}`
    });
    messageForm.message.value = '';
    loadMessages();
});



// Event listeners
recipientType.addEventListener('change', loadRecipients);
recipientId.addEventListener('change', loadMessages);

const recipientList = document.getElementById('recipient-list');

function selectRecipient(li, id) {
  document.querySelectorAll('#recipient-list li').forEach(el => el.classList.remove('active'));
  li.classList.add('active');
  loadMessages(id);
}


// Initial load
loadRecipients();


// Auto-refresh announcements every 30 seconds
setInterval(loadStudentAnnouncements, 30000);

// ========================
// Initial load
// ========================
loadAssignments();
loadStudentAnnouncements();
loadMessages();

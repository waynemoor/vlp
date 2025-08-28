async function loadAssignments() {
    // fetch assignments from lecturer folder
    const response = await fetch('../lecturer/get_assignments.php');
    const assignments = await response.json();

    const tbody = document.getElementById('submitted-assignments-list');
    tbody.innerHTML = '';

    assignments.forEach(a => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-4 py-2">${a.student_id}</td>
            <td class="px-4 py-2"><span id="title-${a.id}">${a.assignment_title}</span></td>
            <td class="px-4 py-2">${a.uploaded_at}</td>
            <td class="px-4 py-2 space-x-2">
                <a href="${a.file_path}" target="_blank" class="text-blue-600 hover:underline">Open</a>
                <button onclick="editTitle(${a.id})" class="text-yellow-600 hover:underline">Edit</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function editTitle(id) {
    const currentTitle = document.getElementById(`title-${id}`).innerText;
    const newTitle = prompt("Enter new title:", currentTitle);

    if (newTitle && newTitle !== currentTitle) {
        // update_assignment.php is inside lecturer folder
        fetch('../lecturer/update_assignment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&title=${encodeURIComponent(newTitle)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`title-${id}`).innerText = newTitle;
            } else {
                alert("Error updating title");
            }
        });
    }
}

// Auto-load assignments
document.addEventListener('DOMContentLoaded', loadAssignments);


/// Quiz Section
let questionCount = 0;

function addQuestion() {
    questionCount++;
    const container = document.getElementById('questions-container');
    const div = document.createElement('div');
    div.innerHTML = `
        <h4>Question ${questionCount}</h4>
        <input type="text" name="question_text_${questionCount}" placeholder="Question text" required>
        <input type="text" name="option_a_${questionCount}" placeholder="Option A" required>
        <input type="text" name="option_b_${questionCount}" placeholder="Option B" required>
        <input type="text" name="option_c_${questionCount}" placeholder="Option C" required>
        <input type="text" name="option_d_${questionCount}" placeholder="Option D" required>
        <select name="correct_option_${questionCount}" required>
            <option value="">Select Correct Option</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>
        <hr>
    `;
    container.appendChild(div);
}

// Submit quiz to backend
document.getElementById('create-quiz-form').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const response = await fetch('../lecturer/save_quiz.php', {
        method: 'POST',
        body: formData
    });
    const result = await response.json();
    alert(result.message);
    if(result.success){
        this.reset();
        document.getElementById('questions-container').innerHTML = '';
        questionCount = 0;
    }
});
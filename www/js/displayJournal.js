   //header and navigation function
   function toggleNav() {
    var sideNav = document.getElementById("sideNav");
    if (sideNav.style.width === "250px") {
        sideNav.style.width = "0";
    } else {
        sideNav.style.width = "250px";
    }
}

  function closeNav() {
        document.getElementById("sideNav").style.width = "0";
    }
    
    document.getElementById('backButton').addEventListener('click', () => {
window.history.back(); // Navigate to the previous page
});
// Fetch journal data from PHP
async function fetchJournalData() {
    try {
        const response = await fetch("server/displayJournal.php", { method: "GET" });
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();
        if (result.success) {
            displayJournalData(result.data);
        } else {
            document.getElementById("responseContainer").innerHTML = `<div class="alert alert-danger">${result.error || 'Failed to retrieve data'}</div>`;
        }
    } catch (error) {
        console.error("Error fetching journal data:", error);
        document.getElementById("responseContainer").innerHTML = `<div class="alert alert-danger">Error fetching data: ${error.message}</div>`;
    }
}

// Display journal data inside white boxes with images and details
// Display journal data inside white boxes with images and details
function displayJournalData(journals) {
const journalDataContainer = document.getElementById("journalData");
journalDataContainer.innerHTML = ''; // Clear any existing content



// If there are journals, display them
journals.forEach((journal) => {
    const journalBox = document.createElement('div');
    journalBox.classList.add('journal-box');
    journalBox.innerHTML = `
    <div class="d-flex justify-content-center">
            ${journal.image_path ? 
                `<img src="${journal.image_path}" alt="Image" class="journal-image">` : 
                `<p>No Image</p>`}
        </div>
        <div><strong>Place:</strong> ${journal.place_name}</div>
        <div><strong>State:</strong> ${journal.state}</div>
        <div><strong>Country:</strong> ${journal.country}</div>
        <div><strong>Travel Date:</strong> ${journal.travel_date}</div>
        <div><strong>Feeling:</strong> ${journal.feeling}</div>
        <div><strong>Impression:</strong> ${journal.impression}</div>
        <div class="currency-info"><strong>Currency Use:</strong> ${journal.spending_currency}</div>
        <div><strong>Food Spending:</strong> ${journal.food_spending}</div>
        <div><strong>Transport Spending:</strong> ${journal.transport_spending}</div>
        <div><strong>Other Spending:</strong> ${journal.other_spending}</div>
        <div><strong>Total Spending Amount:</strong> ${journal.spending_amount}</div>
        <div class="currency-info"><strong>Converted Currency Use:</strong> ${journal.converted_currency}</div>
        <div><strong>Converted Amount:</strong> ${journal.converted_amount}</div>
        
        

        <div class="actions-btns">
            <button class="btn btn-warning" onclick="editJournal(${journal.journal_id})">Edit</button>
            <button class="btn btn-danger" onclick="deleteJournal(${journal.journal_id})">Delete</button>
        </div>
    `;
    journalDataContainer.appendChild(journalBox);
});
}

function deleteJournal(journalid) {
// Show confirmation before deleting
if (confirm("Are you sure you want to delete this journal?")) {
    // Call the force delete function directly
    forceDeleteJournal(journalid);
} else {
    // If the user cancels, show a message and do nothing
    alert("Deletion canceled.");
}
}

function forceDeleteJournal(journalid) {
// Perform the forced delete using fetch
fetch(`server/displayJournal.php?action=force_delete&journal_id=${journalid}`, {
    method: 'GET',
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert("Journal deleted successfully!");
        location.reload(); // Reload the page after successful deletion
    } else {
        alert("Failed to delete journal: " + (data.error || "Unknown error"));
    }
})
.catch(() => {
    location.reload(); // Reload the page regardless of any failure
});
}


// Update the editJournal function to redirect to editJournal.html
function editJournal(journalId) {
// Redirect to the editJournal page, passing the journal ID as a query parameter
window.location.href = `editJournal.html?journal_id=${journalId}`;
}



// On page load, fetch journal data
window.onload = function () {
    fetchJournalData();
};


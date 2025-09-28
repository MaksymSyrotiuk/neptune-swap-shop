// Script written partially with ChatGPT

let currentType = "sold"; // Default transaction type
let currentPage = 1; // Default page number
let scrollPosition = 0; // To save the scroll position before chat opens

const transactionsContainer = document.getElementById("transactions-container");
const pageText = document.getElementById("page-text");
const requestTitle = document.querySelector(".request-title");

const prevBtn = document.getElementById("prev-page");
const nextBtn = document.getElementById("next-page");

// Event listeners for type selection
document.getElementById("show-sold").addEventListener("click", () => {
    currentType = "sold";
    currentPage = 1;
    updateTitle();
    loadTransactions();
});

document.getElementById("show-purchased").addEventListener("click", () => {
    currentType = "purchased";
    currentPage = 1;
    updateTitle();
    loadTransactions();
});

// Event listeners for pagination
prevBtn.addEventListener("click", () => {
    if (currentPage > 1) {
        currentPage--;
        loadTransactions();
    }
});

nextBtn.addEventListener("click", () => {
    currentPage++;
    loadTransactions();
});

// Update title based on current transaction type
function updateTitle() {
    requestTitle.textContent =
        currentType === "sold" ? "Sold products" : "Purchased products";
}

// Load transactions from server
async function loadTransactions() {
    pageText.textContent = currentPage;
    transactionsContainer.innerHTML = "<p>Loading...</p>";

    const response = await fetch(
        `../../src/modules/load_transactions.php?type=${currentType}&page=${currentPage}`,
    );
    const data = await response.json();

    if (!data.success) {
        transactionsContainer.innerHTML = `<p>Error: ${data.error}</p>`;
        togglePagination(false);
        return;
    }

    if (data.transactions.length === 0) {
        transactionsContainer.innerHTML = `<p>No transactions found.</p>`;
        togglePagination(false);
        return;
    }

    function capitalize(str) {
        if (!str) return "";
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    transactionsContainer.innerHTML = "";
    data.transactions.forEach((t) => {
        const transactionDiv = document.createElement("div");
        transactionDiv.className = "transaction";
        transactionDiv.innerHTML = `
                        <div class="transaction-image-block">
                            <img class="background-image" src="uploads/${t.photo}" alt="">
                        </div>
                        <div class="transaction-info-block">
                            <h3 class="transaction-h3">Transaction-ID: ${t.id}</h3>
                            <p class="transaction-p">Offer-ID: ${t.ad_id}</p>
                            <p class="transaction-p">${t.date}</p>
                            <p class="transaction-p">Price: ${t.price}$</p>
                            <p class="transaction-p">Status: ${capitalize(t.status)}</p>
                            <p class="transaction-p">${currentType === "sold" ? "Customer" : "Seller"}: ${currentType === "sold" ? t.buyer_name : t.seller_name}</p>
                            ${renderButton(t)}
                        </div>
                    `;
        transactionDiv.dataset.title = t.title; // save product name for messages
        transactionsContainer.appendChild(transactionDiv);
    });

    togglePagination(data.has_more); // Show/hide pagination buttons
    attachActionButtons(); // Attach event listeners to action buttons
}

// Render action button based on transaction status
function renderButton(t) {
    if (
        currentType === "sold" &&
        t.status !== "shipped" &&
        t.status !== "delivered"
    ) {
        return `<button class="first-button transaction-button" data-action="ship" data-id="${t.ad_id}" data-title="${t.title}">Ship</button>`;
    }
    if (currentType === "purchased" && t.status === "shipped") {
        return `<button class="first-button transaction-button" data-action="deliver" data-id="${t.ad_id}" data-title="${t.title}">Delivered</button>`;
    }
    return "";
}

// Toggle pagination buttons visibility
function togglePagination(show) {
    if (!show || currentPage === 1) {
        prevBtn.style.display = currentPage === 1 ? "none" : "inline-block";
    } else {
        prevBtn.style.display = "inline-block";
    }

    nextBtn.style.display = show ? "inline-block" : "none";
}

// Attach event listeners to action buttons
function attachActionButtons() {
    const buttons = document.querySelectorAll(".transaction-button");
    buttons.forEach((btn) => {
        btn.addEventListener("click", async () => {
            const adId = btn.dataset.id;
            const action = btn.dataset.action;
            const title = btn.dataset.title;

            // Update status on server
            const response = await fetch(
                "../../src/modules/update_status.php",
                {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ ad_id: adId, action: action }),
                },
            );

            const result = await response.json();
            if (!result.success) {
                alert(result.error || "Error updating status.");
                return;
            }

            // Show alert message
            if (action === "ship") {
                alert(
                    `The buyer will be notified that the product "${title}" has been shipped.`,
                );
            } else if (action === "deliver") {
                alert(
                    `The seller will be notified that the product "${title}" has been delivered and picked up.`,
                );
            }

            // Reload transactions
            loadTransactions();
        });
    });
}

// Initial load
updateTitle();
loadTransactions();

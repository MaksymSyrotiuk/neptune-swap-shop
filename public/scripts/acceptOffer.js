document.querySelectorAll(".first-button").forEach((button) => {
    button.addEventListener("click", function () {
        const requestId = this.dataset.id;

        fetch("../../src/modules/accept-request.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "request_id=" + encodeURIComponent(requestId),
        })
            .then((response) => response.text())
            .then((result) => {
                if (result === "success") {
                    alert("Offer accepted. Messages sent.");
                    location.reload(); // Reload the page
                } else {
                    alert(result); // Show error
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    });
});

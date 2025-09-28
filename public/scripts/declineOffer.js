document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".second-button").forEach((button) => {
        button.addEventListener("click", function () {
            let requestId = this.dataset.id;
            if (!confirm("Are you sure you want to decline this offer?")) {
                return;
            }

            fetch("../../src/modules//decline_request.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ request_id: requestId }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("The offer has been declined.");
                        location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred.");
                });
        });
    });
});

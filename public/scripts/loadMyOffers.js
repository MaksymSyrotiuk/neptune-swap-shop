document.addEventListener("DOMContentLoaded", function () {
    // Initialize the current page number.
    let currentPage = 1;
    // Get references to DOM elements.
    const shopBlock = document.getElementById("shop-block");
    const prevPageBtn = document.getElementById("prev-page");
    const nextPageBtn = document.getElementById("next-page");
    const pageText = document.getElementById("page-text");
    // Store the currently active action menu.
    let activeMenu = null;

    /**
     * Loads offers from the server and populates the shop block.
     * @param {number} page - The page number to load.
     */
    function loadOffers(page) {
        fetch(`../..src/modules/fetch_my_offers.php?page=${page}`)
            .then((response) => response.json())
            .then((data) => {
                // Clear the shop block before adding new offers.
                shopBlock.innerHTML = "";
                if (data.success) {
                    // Iterate through the products and create item blocks.
                    data.products.forEach((offer) => {
                        const itemBlock = document.createElement("div");
                        itemBlock.classList.add("item-block");
                        itemBlock.dataset.id = offer.id;

                        // Determine the status color and text based on the offer status.
                        let statusColor = "#000";
                        let statusText =
                            offer.status.charAt(0).toUpperCase() +
                            offer.status.slice(1);

                        switch (offer.status.toLowerCase()) {
                            case "active":
                                statusColor = "#2B4BAC";
                                break;
                            case "disabled":
                                statusColor = "#000000";
                                break;
                            case "shipped":
                                statusColor = "#7F78F7";
                                break;
                            case "delivered":
                                statusColor = "#060079";
                                break;
                            case "purchased":
                                statusColor = "#2F3B5F";
                                break;
                        }

                        // Create the status block element.
                        const statusBlock = document.createElement("div");
                        statusBlock.classList.add("status-block");
                        statusBlock.style.backgroundColor = statusColor;
                        statusBlock.textContent = statusText;

                        // Append the trade count to the status text if available.
                        if (offer.trade_count > 0) {
                            statusBlock.textContent += ` (${offer.trade_count})`;
                        }

                        // Populate the item block with offer details.
                        itemBlock.innerHTML = `
                            <div class="image-block">
                                <img class="background-image" src="uploads/${offer.photo}" alt="${offer.title}">
                            </div>
                            <h2 class="item-name">${offer.title}</h2>
                            <p class="brief-text">${offer.brief1}</p>
                            <p class="brief-text">${offer.brief2}</p>
                            <p class="brief-text">${offer.brief3}</p>
                            <div class="price-block">
                                <p class="price-text">${offer.price}$</p>
                            </div>
                        `;

                        // Add the status block to the item block.
                        itemBlock.prepend(statusBlock);
                        // Append the item block to the shop block.
                        shopBlock.appendChild(itemBlock);

                        // Add a click event listener to the item block to open the action menu.
                        itemBlock.addEventListener("click", (event) => {
                            openActionMenu(
                                event,
                                itemBlock,
                                offer.id,
                                offer.status,
                            );
                        });
                    });

                    // Update the page text and disable/enable pagination buttons.
                    pageText.textContent = `${data.current_page}/${data.total_pages}`;
                    prevPageBtn.disabled = data.current_page === 1;
                    nextPageBtn.disabled =
                        data.current_page === data.total_pages;
                }
            })
            .catch((error) => console.error("Error loading offers:", error));
    }

    /**
     * Opens the action menu for an offer.
     * @param {MouseEvent} event - The click event.
     * @param {HTMLElement} itemBlock - The clicked item block.
     * @param {number} offerId - The offer ID.
     * @param {string} offerStatus - The offer status.
     */
    function openActionMenu(event, itemBlock, offerId, offerStatus) {
        // Remove the previous active menu if it exists.
        if (activeMenu) {
            activeMenu.remove();
            activeMenu = null;
        }

        setTimeout(() => {
            const lowerStatus = offerStatus.toLowerCase();
            const isToggleAvailable =
                lowerStatus === "active" || lowerStatus === "inactive";
            const isEditAvailable = isToggleAvailable;
            const isDisabled = lowerStatus === "inactive";
            const actionText = isDisabled ? "Enable" : "Disable";
            const menu = document.createElement("div");

            menu.classList.add("action-menu");
            menu.style.position = "absolute";
            menu.style.left = `${event.pageX - 20}px`;
            menu.style.top = `${event.pageY - 20}px`;

            // Populate the action menu with relevant options.
            menu.innerHTML = `
                ${
                    isEditAvailable
                        ? `<div class="first-action" data-id="${offerId}">Edit</div>`
                        : `<div class="first-action" data-id="${offerId}">Description</div>`
                }
                <div class="second-action" data-id="${offerId}">Suggestions</div>
                ${
                    isToggleAvailable
                        ? `<div class="third-action" data-id="${offerId}" data-status="${offerStatus}">${actionText}</div>`
                        : ""
                }
            `;

            // Append the menu to the document body and set it as the active menu.
            document.body.appendChild(menu);
            activeMenu = menu;

            setTimeout(() => {
                // Add a click listener to close the menu when clicking outside.
                document.addEventListener("click", closeMenuOutside);
            }, 10);

            // Add event listeners to the menu actions.
            if (isEditAvailable) {
                menu.querySelector(".first-action").addEventListener(
                    "click",
                    function () {
                        let offerId = this.getAttribute("data-id");
                        if (offerId) {
                            window.location.href = `./edit-offer.php?id=${offerId}`;
                        }
                    },
                );
            } else {
                menu.querySelector(".first-action").addEventListener(
                    "click",
                    function () {
                        let offerId = this.getAttribute("data-id");
                        if (offerId) {
                            window.location.href = `./edit-offer.php?id=${offerId}`;
                        }
                    },
                );
            }

            menu.querySelector(".second-action").addEventListener(
                "click",
                function () {
                    let offerId = this.getAttribute("data-id");
                    if (offerId) {
                        window.open(
                            `./suggestions.php?id=${offerId}`,
                            "_blank",
                        );
                    }
                },
            );

            if (isToggleAvailable) {
                menu.querySelector(".third-action").addEventListener(
                    "click",
                    function () {
                        toggleOfferStatus(offerId, isDisabled);
                    },
                );
            }
        }, 50);
    }

    /**
     * Closes the action menu when clicking outside of it.
     * @param {MouseEvent} event - The click event.
     */
    function closeMenuOutside(event) {
        if (activeMenu && !activeMenu.contains(event.target)) {
            activeMenu.remove();
            activeMenu = null;
            document.removeEventListener("click", closeMenuOutside);
        }
    }

    /**
     * Toggles the status of an offer.
     * @param {number} offerId - The offer ID.
     * @param {boolean} enable - Whether to enable or disable the offer.
     */
    function toggleOfferStatus(offerId, enable) {
        fetch("../../src/modules/toggle_offer.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: offerId, enable: enable }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(
                        `Offer ${enable ? "enabled" : "disabled"} successfully!`,
                    );
                    loadOffers(currentPage);
                } else {
                    if (data.error) {
                        alert("Error: " + data.error);
                    } else {
                        alert("Error: " + data.message);
                    }
                }
            })
            .catch((error) => console.error("Error toggling offer:", error));
    }

    // Add event listeners to the pagination buttons.
    prevPageBtn.addEventListener("click", () => {
        if (currentPage > 1) {
            currentPage--;
            loadOffers(currentPage);
        }
    });

    nextPageBtn.addEventListener("click", () => {
        currentPage++;
        loadOffers(currentPage);
    });

    // Load the initial page of offers.
    loadOffers(currentPage);
});

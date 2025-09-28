// Script written partially with ChatGPT

document.addEventListener("DOMContentLoaded", function () {
    let currentPage = 1;
    let searchQuery = "";
    let selectedCategories = [];
    let selectedSort = "no";
    let timeout = null;
    let openHagglePopupHandler;
    let scrollPosition = 0;

    const shopBlock = document.getElementById("shop-block");
    const filterBlock = document.querySelectorAll(".filter-block");
    const prevButton = document.getElementById("prev-page");
    const nextButton = document.getElementById("next-page");
    const pageText = document.getElementById("page-text");
    const searchInput = document.querySelector(".filter-search");
    const categoryInputs = document.querySelectorAll(
        ".options input[type='checkbox']",
    );
    const sortSelect = document.querySelector(".search-category");
    const body = document.querySelector("body");
    const previewItemBlock = document.querySelector(".preview-item-block");
    const returnButton = document.querySelector(".return-button");
    const paginationBlock = document.querySelector(".page-switcher");

    const firstButton = document.querySelector(".first-button");
    const secondButton = document.querySelector(".second-button");
    const thirdButton = document.querySelector(".third-button");

    // Get product ID from URL (if it exists)
    const params = new URLSearchParams(window.location.search);
    const productIdFromURL = params.get("id");

    function loadProducts(page) {
        const categories =
            selectedCategories.length > 0
                ? selectedCategories.join(",")
                : "all";
        const url = `../src/modules/get_products.php?page=${page}&search=${encodeURIComponent(searchQuery)}&category=${categories}&sort=${selectedSort}`;

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                shopBlock.innerHTML = "";
                shopBlock.style.display = "flex";
                filterBlock.forEach((block) => (block.style.display = "flex"));
                if (paginationBlock) paginationBlock.style.display = "flex";
                if (previewItemBlock) previewItemBlock.style.display = "none";

                data.products.forEach((product) => {
                    const item = document.createElement("div");
                    item.className = "item-block";
                    // Use template literal for HTML markup
                    item.innerHTML = `
                        <div class="image-block">
                            <img class="background-image" src="uploads/${product.photo}" alt="${product.title}">
                        </div>
                        <h2 class="item-name">${product.title}</h2>
                        <p class="brief-text">${product.brief1}</p>
                        <p class="brief-text">${product.brief2}</p>
                        <p class="brief-text">${product.brief3}</p>
                        <div class="price-block">
                            <p class="price-text">${product.price}€</p>
                        </div>
                    `;
                    item.addEventListener("click", () => openPreview(product));
                    shopBlock.appendChild(item);
                });

                pageText.innerText = `${page} / ${data.totalPages}`;
                prevButton.disabled = page <= 1;
                nextButton.disabled = page >= data.totalPages;

                // If a product ID is provided in the URL, look for it in the fetched data
                if (productIdFromURL) {
                    const matchingProduct = data.products.find(
                        (p) => p.id == productIdFromURL,
                    );
                    if (matchingProduct) {
                        openPreview(matchingProduct);
                        // Optionally, remove the "id" parameter from the URL:
                        const urlObj = new URL(window.location);
                        urlObj.searchParams.delete("id");
                        window.history.replaceState({}, "", urlObj);
                    }
                }
            })
            .catch((error) => console.error("Error loading products:", error));
    }

    function openPreview(product) {
        if (!previewItemBlock) return;

        // Save scroll position, hide other blocks, and show preview
        scrollPosition =
            window.pageYOffset || document.documentElement.scrollTop;
        shopBlock.style.display = "none";
        previewItemBlock.style.display = "flex";
        filterBlock.forEach((block) => (block.style.display = "none"));
        paginationBlock.style.display = "none";

        // Update URL with the product ID
        const urlObj = new URL(window.location);
        urlObj.searchParams.set("id", product.id);
        window.history.pushState({ productId: product.id }, "", urlObj);

        // Populate preview with product details
        previewItemBlock.querySelector(".preview-image").src =
            `uploads/${product.photo}`;
        previewItemBlock.querySelector(".preview-item-name").textContent =
            product.title;
        previewItemBlock.querySelector(".seller-name").textContent =
            `Seller: ${product.username}`;

        const briefTexts = previewItemBlock.querySelectorAll(
            ".preview-brief-text",
        );
        [product.brief1, product.brief2, product.brief3].forEach(
            (text, index) => {
                if (text) {
                    briefTexts[index].textContent = text;
                    briefTexts[index].style.display = "flex";
                } else {
                    briefTexts[index].style.display = "none";
                }
            },
        );

        previewItemBlock.querySelector(".description-block").textContent =
            product.description;
        previewItemBlock.querySelector(".preview-price").textContent =
            `${product.price}€`;

        // Clear button container and add appropriate buttons
        const btnContainer = previewItemBlock.querySelector(".buttons-block");
        btnContainer.innerHTML = "";

        if (product.isOwnProduct) {
            // Show Edit button for own product
            const editBtn = document.createElement("button");
            editBtn.className = "first-button";
            editBtn.textContent = "Edit";
            editBtn.addEventListener("click", () => {
                window.location.href = `edit-offer.php?id=${product.id}`;
            });
            btnContainer.appendChild(editBtn);
        }
        if (!product.isOwnProduct) {
            // (Previously created Buy button code remains unchanged)
            const buyBtn = document.createElement("button");
            buyBtn.className = "first-button";
            buyBtn.textContent = "Buy";
            buyBtn.addEventListener("click", () => {
                if (confirm("Do you want to buy this item?")) {
                    fetch("../../src/modules/buy_ad.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `ad_id=${product.id}`,
                    })
                        .then((res) => res.json())
                        .then((data) => {
                            if (data.success) {
                                alert("Purchase successful!");
                                returnButton.click(); // Return to list
                                loadProducts(currentPage);
                            } else {
                                alert("Error: " + data.error);
                            }
                        })
                        .catch((err) => {
                            console.error("Buy error:", err);
                            alert("Something went wrong. Try again.");
                        });
                }
            });
            btnContainer.appendChild(buyBtn);

            // Create Contact with the Seller button
            const contactBtn = document.createElement("button");
            contactBtn.className = "second-button";
            contactBtn.textContent = "Contact with the Seller";
            contactBtn.addEventListener("click", () => {
                // Assume current user is the buyer.
                // Send a POST request to open or create a chat.
                fetch("../../src/modules/open_dialog.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        product_id: product.id,
                        seller_id: product.user_id, // product.user_id should be the seller's ID
                        product_title: product.title,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            // Open the chat in a new tab with the returned chat ID.
                            window.open(
                                `messages.php?chat_id=${data.chat_id}`,
                                "_blank",
                            );
                        } else {
                            alert("Error: " + data.error);
                        }
                    })
                    .catch((error) => {
                        console.error("Error opening chat:", error);
                        alert("An error occurred. Please try again later.");
                    });
            });
            btnContainer.appendChild(contactBtn);

            // Create Haggle button as before
            const haggleBtn = document.createElement("button");
            haggleBtn.className = "third-button";
            haggleBtn.textContent = "Haggle";
            haggleBtn.addEventListener("click", () => {
                openHagglePopup(product.price, product.id, product.user_id);
            });
            btnContainer.appendChild(haggleBtn);
        }

        // Scroll to the top of the page (or to where desired)
        body.scrollIntoView();
    }

    // Pop-up for Haggle
    function addHaggleRequestHandler(productId, sellerId) {
        const requestButton = document.querySelector(".haggle-request");
        requestButton.addEventListener("click", () => {
            const hagglePriceText =
                document.querySelector(".haggle-price").textContent;
            const newPrice = parseFloat(
                hagglePriceText.replace("Price: ", "").replace("$", ""),
            );
            if (isNaN(newPrice)) {
                alert("Error: Invalid price.");
                return;
            }
            fetch("../../src/modules/request_trade.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    product_id: productId,
                    seller_id: sellerId,
                    requested_price: newPrice,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Haggle request sent successfully!");
                        document.body.style.overflow = "";
                        document.querySelector(".haggle-popup").remove();
                        document.querySelector(".black-screen").remove();
                    } else {
                        alert("Error: " + data.error);
                    }
                })
                .catch((error) => {
                    console.error("Error sending request:", error);
                    alert("An error occurred. Please try again later.");
                });
        });
    }

    function openHagglePopup(originalPrice, productId, sellerId) {
        document.body.style.overflow = "hidden";

        // Create black screen overlay
        const blackScreen = document.createElement("div");
        blackScreen.className = "black-screen";
        document.body.appendChild(blackScreen);

        // Create pop-up element
        const popup = document.createElement("div");
        popup.className = "haggle-popup";
        popup.style.position = "fixed";
        popup.style.left = "50%";
        popup.style.top = "50%";
        popup.style.transform = "translate(-50%, -50%)";

        popup.innerHTML = `
            <button class="haggle-return-button">✖</button>
            <h2 class="haggle-h2">Haggle</h2>
            <p class="haggle-p">You can request a price reduction of up to 30%!</p>
            <div class="input-haggle-block">
                <input class="haggle-input-range" type="range" min="0" max="30" step="1" value="0">
                <input class="haggle-input-number" max="30" value="0" type="number">
            </div>
            <p class="haggle-price">Price: ${originalPrice}€</p>
            <button class="haggle-request first-button">Request</button>
        `;
        document.body.appendChild(popup);

        // Add handler for the Request button inside the popup
        addHaggleRequestHandler(productId, sellerId);

        const rangeInput = popup.querySelector(".haggle-input-range");
        const numberInput = popup.querySelector(".haggle-input-number");
        const priceText = popup.querySelector(".haggle-price");

        function updatePrice(value) {
            const discount = (originalPrice * value) / 100;
            const newPrice = originalPrice - discount;
            priceText.textContent = `Price: ${newPrice.toFixed(2)}€`;
        }

        rangeInput.addEventListener("input", () => {
            numberInput.value = rangeInput.value;
            updatePrice(rangeInput.value);
        });

        numberInput.addEventListener("input", () => {
            let value = parseInt(numberInput.value, 10) || 0;
            value = Math.min(Math.max(value, 0), 30);
            numberInput.value = value;
            rangeInput.value = value;
            updatePrice(value);
        });

        popup
            .querySelector(".haggle-return-button")
            .addEventListener("click", () => {
                document.body.removeChild(popup);
                document.body.removeChild(
                    document.querySelector(".black-screen"),
                );
                document.body.style.overflow = "";
            });
    }

    if (returnButton) {
        returnButton.addEventListener("click", () => {
            if (previewItemBlock) {
                previewItemBlock.style.display = "none";
                // Reset button container contents and remove event listeners
                const btnContainer =
                    previewItemBlock.querySelector(".buttons-block");
                btnContainer.innerHTML = "";

                const url = new URL(window.location);
                url.searchParams.delete("id");
                window.history.pushState({}, "", url);
            }
            shopBlock.style.display = "flex";
            filterBlock.forEach((block) => (block.style.display = "flex"));
            if (paginationBlock) paginationBlock.style.display = "flex";
            window.scrollTo(0, scrollPosition);
        });
    }

    prevButton.addEventListener("click", function () {
        if (currentPage > 1) {
            currentPage--;
            loadProducts(currentPage);
            body.scrollIntoView();
        }
    });

    nextButton.addEventListener("click", function () {
        currentPage++;
        loadProducts(currentPage);
        body.scrollIntoView();
    });

    searchInput.addEventListener("input", function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            searchQuery = searchInput.value.trim();
            currentPage = 1;
            loadProducts(currentPage);
        }, 300);
    });

    categoryInputs.forEach((input) => {
        input.addEventListener("change", function () {
            selectedCategories = Array.from(categoryInputs)
                .filter((i) => i.checked)
                .map((i) => i.value);
            currentPage = 1;
            loadProducts(currentPage);
        });
    });

    sortSelect.addEventListener("change", function () {
        selectedSort = sortSelect.value;
        currentPage = 1;
        loadProducts(currentPage);
    });

    loadProducts(currentPage);
});

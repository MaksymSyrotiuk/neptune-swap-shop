document.addEventListener("DOMContentLoaded", function () {
    let currentPage = 1;
    const limit = 8; // Maximum chats per page
    const usersList = document.querySelector(".users-list");
    const prevBtn = document.getElementById("prev-page");
    const nextBtn = document.getElementById("next-page");
    const pageText = document.getElementById("page-text");
    const pageSwitcher = document.querySelector(".page-switcher");

    const returnButton = document.querySelector(".haggle-return-button");
    const chatBlock = document.querySelector(".chat-block");
    const inputField = document.querySelector(".chat-input");
    const sendButton = document.querySelector(".send-button");

    let currentChatUserName = "";
    let currentChatId = null;

    // Load chats with pagination
    function loadChats(page = 1) {
        fetch(`../../src/modules/get_dialogs.php?limit=${limit}&page=${page}`)
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    console.error("Error: " + data.error);
                    usersList.innerHTML = `<p class="error-message">${data.error}</p>`;
                    return;
                }

                usersList.innerHTML = ""; // Clear the list
                data.chats.forEach((chat) => {
                    const userDiv = document.createElement("div");
                    userDiv.className = "user";
                    userDiv.dataset.chatId = chat.chat_id;
                    userDiv.dataset.userName = chat.username;

                    const unreadText =
                        chat.unread_count && chat.unread_count > 0
                            ? ` (${chat.unread_count})`
                            : "";
                    userDiv.innerHTML = `<p class="chat-p">${chat.username}${unreadText}</p>`;
                    usersList.appendChild(userDiv);

                    userDiv.addEventListener("click", function () {
                        openChat(chat.chat_id, chat.username);
                        history.replaceState(
                            null,
                            "",
                            `?chat_id=${chat.chat_id}`,
                        );
                    });
                });

                pageText.textContent = `${data.current_page} / ${data.total_pages}`;
                prevBtn.disabled = data.current_page <= 1;
                nextBtn.disabled = data.current_page >= data.total_pages;

                // Show or hide the page switcher
                pageSwitcher.style.display =
                    data.total_pages >= 1 ? "flex" : "none";
            })
            .catch((error) => console.error("Error loading chats:", error));
    }

    // Page switching
    prevBtn.addEventListener("click", () => {
        if (currentPage > 1) {
            currentPage--;
            loadChats(currentPage);
        }
    });

    nextBtn.addEventListener("click", () => {
        currentPage++;
        loadChats(currentPage);
    });

    // Open chat
    function openChat(chatId, userName) {
        currentChatUserName = userName;
        currentChatId = chatId;

        chatBlock.style.display = "block";
        returnButton.style.display = "block";
        usersList.style.display = "none";
        document.querySelector(".select-user-h2").style.display = "none";

        document.querySelector(".chat-block .chat-h2").textContent =
            "Chat with " + userName;

        pageSwitcher.style.display = "none"; // <-- Hide when chat is open

        loadMessages(chatId);

        fetch("../../src/modules/mark_as_read.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ chat_id: chatId }),
        });
    }

    returnButton.addEventListener("click", function () {
        chatBlock.style.display = "none";
        returnButton.style.display = "none";
        usersList.style.display = "flex";
        document.querySelector(".select-user-h2").style.display = "block";

        currentChatId = null;
        currentChatUserName = "";

        pageSwitcher.style.display = "flex"; // <-- Show again

        const urlObj = new URL(window.location);
        urlObj.searchParams.delete("chat_id");
        window.history.replaceState(null, "", urlObj);
    });

    // Send messages
    sendButton.addEventListener("click", sendMessage);
    inputField.addEventListener("keydown", function (event) {
        if (event.key === "Enter") sendMessage();
    });

    function sendMessage() {
        const message = inputField.value.trim();
        if (message === "" || !currentChatId) return;

        fetch("../../src/modules/send_message.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                chat_id: currentChatId,
                message: message,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    inputField.value = "";
                    loadMessages(currentChatId);
                } else {
                    console.error("Error sending:", data.error);
                }
            })
            .catch((error) => console.error("Error:", error));
    }

    function loadMessages(chatId) {
        const messagesBlock = document.querySelector(".messages-block");
        fetch(`../../src/modules/get_messages.php?chat_id=${chatId}`)
            .then((response) => response.json())
            .then((messages) => {
                messagesBlock.innerHTML = "";
                if (Array.isArray(messages)) {
                    messages.forEach((msg) => {
                        const msgDiv = document.createElement("div");
                        msgDiv.className = msg.message_type;

                        const senderName =
                            msg.message_type === "my-message"
                                ? "You"
                                : currentChatUserName;
                        const formattedTime = formatMessageTime(msg.created_at);

                        msgDiv.innerHTML = `
                            <p class="sender-name">${senderName}</p>
                            <p class="message">${msg.message}</p>
                            <p class="message-time">${formattedTime}</p>
                        `;
                        messagesBlock.appendChild(msgDiv);
                    });
                    messagesBlock.scrollTop = messagesBlock.scrollHeight;
                } else {
                    messagesBlock.innerHTML = `<p class="error-message">Error: ${messages.error}</p>`;
                }
            })
            .catch((error) => console.error("Error loading messages:", error));
    }

    // Function written partially with ChatGPT
    function formatMessageTime(dateString) {
        const messageDate = new Date(dateString);
        const now = new Date();
        const pad = (n) => n.toString().padStart(2, "0");

        const isToday =
            messageDate.getDate() === now.getDate() &&
            messageDate.getMonth() === now.getMonth() &&
            messageDate.getFullYear() === now.getFullYear();

        const isYesterday =
            messageDate.getDate() === now.getDate() - 1 &&
            messageDate.getMonth() === now.getMonth() &&
            messageDate.getFullYear() === now.getFullYear();

        const hours = pad(messageDate.getHours());
        const minutes = pad(messageDate.getMinutes());

        if (isToday) return `${hours}:${minutes}`;
        else if (isYesterday) return `Yesterday ${hours}:${minutes}`;
        else if (messageDate.getFullYear() === now.getFullYear())
            return `${pad(messageDate.getDate())}.${pad(messageDate.getMonth() + 1)} ${hours}:${minutes}`;
        else
            return `${pad(messageDate.getDate())}.${pad(messageDate.getMonth() + 1)}.${messageDate.getFullYear()} ${hours}:${minutes}`;
    }

    const urlParams = new URLSearchParams(window.location.search);
    const chatIdFromURL = urlParams.get("chat_id");
    if (chatIdFromURL) {
        loadChats(currentPage);

        // Wait for the chats to load and then open the desired one.
        const observer = new MutationObserver(() => {
            const chatToOpen = document.querySelector(
                `.user[data-chat-id="${chatIdFromURL}"]`,
            );
            if (chatToOpen) {
                chatToOpen.click();
                observer.disconnect();
            }
        });

        observer.observe(document.querySelector(".users-list"), {
            childList: true,
        });
    } else {
        loadChats(currentPage);
    }
});

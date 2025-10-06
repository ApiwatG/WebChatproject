// Initialize Pusher
const pusher = new Pusher("9fb9967f0fe9d1b937af", {
    cluster: "ap1",
    encrypted: true,
});

// Join the channel
const channel = pusher.subscribe("chat." + roomId);

// Listen for messages
channel.bind("MessageSent", function (data) {
    // แสดงข้อความใหม่ทันที (ทุกคนในห้อง)
    addMessage(data.user, data.message, data.user === currentUser, data.time);
    if (soundEnabled) {
        new Audio("/sounds/notification.mp3").play();
    }
});

// Load initial messages
fetch("/chat/" + roomId + "/messages")
    .then((response) => response.json())
    .then((messages) => {
        messages.forEach((msg) => {
            addMessage(
                msg.user,
                msg.message,
                msg.user === currentUser,
                msg.time
            );
        });
    });

// Send message function
async function sendMessage(e) {
    e.preventDefault();
    const messageInput = document.getElementById("message");
    const message = messageInput.value.trim();

    if (!message) return;

    try {
        const response = await fetch("/chat/" + roomId + "/send", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({ message }),
        });

        if (response.ok) {
            messageInput.value = "";
            // ไม่ต้อง addMessage ทันที รอ event จาก Pusher
        } else {
            throw new Error("Failed to send message");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Failed to send message. Please try again.");
    }
}

// Add message to UI
function addMessage(user, message, isSent = false, time = null) {
    const messagesDiv = document.getElementById("messages");
    const msgDiv = document.createElement("div");
    msgDiv.className = isSent ? "msg sent" : "msg recv";

    // time เป็น string ที่ได้จาก backend
    const timeStr = time
        ? typeof time === "string"
            ? time.substring(11, 16)
            : ""
        : "";

    msgDiv.innerHTML = `
        <strong>${user}:</strong> 
        <span>${message}</span>
        ${timeStr ? `<small class="time">${timeStr}</small>` : ""}
    `;

    messagesDiv.appendChild(msgDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// Handle form submit
document.getElementById("chat-form").addEventListener("submit", sendMessage);

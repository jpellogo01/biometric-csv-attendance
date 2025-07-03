// handleLogin.js
document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    fetch("auth.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ email: email, password: password }),
    })
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            window.location.href = "dashboard.php"; // redirect to a protected page
        } else {
            document.querySelector(".error").textContent = data.message;
        }
    })
    .catch((error) => {
        console.error("Error:", error);
    });
});

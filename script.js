document.getElementById("explainForm").addEventListener("submit", async function (e) {
    e.preventDefault();
    const resultDiv = document.getElementById("result");
    resultDiv.innerText = "Thinking...";
    const formData = new FormData();
    formData.append("code", document.getElementById("codeInput").value);
    formData.append("language", document.getElementById("language").value);
    formData.append("mode", document.getElementById("mode").value);
    const response = await fetch("explain.php", {
        method: "POST",
        body: formData
    });
    const data = await response.json();
    resultDiv.innerText = data.explanation || data.error;
});
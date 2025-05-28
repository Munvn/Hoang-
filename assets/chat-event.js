document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".typing-area");
  const inputField = form.querySelector(".input-field");
  const fileInput = form.querySelector(".file-input");
  const sendBtn = form.querySelector("button[type='submit']");
  const chatBox = document.querySelector(".chat-box");
  const incoming_id = form.querySelector(".incoming_id").value;

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "message.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        let response = JSON.parse(xhr.responseText);
        if (response.success) {
          inputField.value = "";
          fileInput.value = "";
          checkInput();
          scrollToBottom();
        }
      }
    };
    let formData = new FormData(form);
    formData.append("action", "insert");
    xhr.send(formData);
  });

  function checkInput() {
    if (inputField.value.trim() !== "" || fileInput.files.length > 0) {
      sendBtn.classList.add("active");
      sendBtn.disabled = false;
    } else {
      sendBtn.classList.remove("active");
      sendBtn.disabled = true;
    }
  }

  inputField.addEventListener("input", checkInput);
  fileInput.addEventListener("change", checkInput);
  checkInput();

  setInterval(() => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "message.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        let response = JSON.parse(xhr.responseText);
        chatBox.innerHTML =
          response.chat || "<div class='text'>Không có tin nhắn.</div>";
        if (!chatBox.classList.contains("active")) {
          scrollToBottom();
        }
      }
    };
    let formData = new FormData();
    formData.append("action", "getChat");
    formData.append("incoming_id", incoming_id);
    xhr.send(formData);
  }, 500);

  function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  chatBox.onmouseenter = () => chatBox.classList.add("active");
  chatBox.onmouseleave = () => chatBox.classList.remove("active");
});

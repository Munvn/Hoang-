const form = document.querySelector(".login form");
const submitBtn = document.querySelector(".button input");
const errorText = document.querySelector(".error-text");

form.onsubmit = (e) => {
  e.preventDefault();
};

submitBtn.onclick = () => {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "backend/login.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      let data = xhr.response;
      if (data === "success") {
        location.href = "http://localhost/BCTT/admin.php";
      } else {
        errorText.style.display = "block";
        errorText.textContent = data;
      }
    }
  };
  let formData = new FormData(form);
  xhr.send(formData);
};

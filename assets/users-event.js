const searchBar = document.querySelector(".search input");
const searchIcon = document.querySelector(".search button");
const usersList = document.querySelector(".users-list");

searchIcon.onclick = function () {
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if (searchBar.classList.contains("active")) {
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
};

searchBar.onkeyup = () => {
  let searchTerm = searchBar.value;
  if (searchTerm !== "") {
    searchBar.classList.add("active");
  } else {
    searchBar.classList.remove("active");
  }

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "backend/search.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        usersList.innerHTML = xhr.responseText || "Lỗi tải danh sách.";
        console.log("Search response:", xhr.responseText);
      } else {
        console.error("Lỗi gọi AJAX (search):", xhr.status, xhr.statusText);
        usersList.innerHTML = "Lỗi tải danh sách (search): " + xhr.statusText;
      }
    }
  };
  xhr.onerror = () => {
    console.error("Lỗi mạng khi gọi search.php");
    usersList.innerHTML = "Lỗi mạng khi tải danh sách.";
  };
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("searchTerm=" + encodeURIComponent(searchTerm));
};

setInterval(() => {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "backend/users.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        if (!searchBar.classList.contains("active")) {
          usersList.innerHTML = xhr.responseText || "Lỗi tải danh sách.";
          console.log("Users response:", xhr.responseText);
        }
      } else {
        console.error("Lỗi gọi AJAX (users):", xhr.status, xhr.statusText);
        usersList.innerHTML = "Lỗi tải danh sách (users): " + xhr.statusText;
      }
    }
  };
  xhr.onerror = () => {
    console.error("Lỗi mạng khi gọi users.php");
    usersList.innerHTML = "Lỗi mạng khi tải danh sách.";
  };
  xhr.send();
}, 500);

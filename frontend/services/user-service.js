var UserService = {

 
  init: function () {
    const token = localStorage.getItem("user_token");
    const parsed = Utils.parseJwt(token);

    if (parsed?.user) {
      window.location.replace("index.html");
      return;
    }

    $("#login-form").validate({
      submitHandler: function (form) {
        const entity = Object.fromEntries(new FormData(form).entries());
        UserService.login(entity);
      }
    });
  },

  
  login: function (entity) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/login",
      type: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify(entity),
      success: function (result) {
        localStorage.setItem("user_token", result.data.token);
        window.location.replace("index.html"); 
      },
      error: function (xhr) {
        toastr.error(xhr.responseJSON?.message || "Login failed");
      }
    });
  },

  // Clear token and redirect to login page
  logout: function () {
    localStorage.removeItem("user_token");
    window.location.replace("pages/login.html");  
  },

  // Generate navigation menu based on user role
  generateMenuItems: function () {
    const token = localStorage.getItem("user_token");
    const parsed = Utils.parseJwt(token);

    if (!parsed || !parsed.user) {
      window.location.replace("pages/login.html"); 
      return;
    }

    const user = parsed.user;
    const nav = document.getElementById("nav-menu");
    nav.innerHTML = "";

    // HOME
    nav.innerHTML += `
      <li class="nav-item">
        <a class="nav-link" href="#home">Home</a>
      </li>
    `;

    if (user.role === Constants.ADMIN_ROLE) {
      nav.innerHTML += `
        <li class="nav-item">
          <a class="nav-link" href="#students">Students</a>
        </li>
      `;
    }

    if (user.role === Constants.USER_ROLE) {
      nav.innerHTML += `
        <li class="nav-item">
          <a class="nav-link" href="#about">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#contact">Contact</a>
        </li>
      `;
    }

    
    nav.innerHTML += `
      <li class="nav-item">
        <button class="btn btn-danger ms-3" onclick="UserService.logout()">Logout</button>
      </li>
    `;
  }
};

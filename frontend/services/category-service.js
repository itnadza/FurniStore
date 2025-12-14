var CategoryService = {

  getAll: function (success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "categories",
      type: "GET",
      success: success,
      error: error
    });
  },

  getById: function (id, success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "categories/" + id,
      type: "GET",
      success: success,
      error: error
    });
  },

  create: function (entity, success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "categories",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify(entity),
      success: success,
      error: error
    });
  },

  update: function (id, entity, success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "categories/" + id,
      type: "PUT",
      contentType: "application/json",
      data: JSON.stringify(entity),
      success: success,
      error: error
    });
  },

  delete: function (id, success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "categories/" + id,
      type: "DELETE",
      success: success,
      error: error
    });
  }
};

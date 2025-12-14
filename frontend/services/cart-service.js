var CartService = {

  getCart: function (success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "cart",
      type: "GET",
      success: success,
      error: error
    });
  },

  addItem: function (entity, success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "cart",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify(entity),
      success: success,
      error: error
    });
  },

  removeItem: function (id, success, error) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "cart/" + id,
      type: "DELETE",
      success: success,
      error: error
    });
  }
};

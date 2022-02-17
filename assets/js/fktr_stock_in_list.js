jQuery(document).ready(function () {
  jQuery(".btn_download_pdf").click(function (e) {
    e.preventDefault();
    var popUpObj;
    popUpObj = window.open(
      "admin-post.php?action=products_print_pdf&post_type=fktr_product",
      "ModalPopUp",
      "toolbar=no," +
        "titlebar=no," +
        "scrollbars=no," +
        "location=no," +
        "statusbar=no," +
        "menubar=no," +
        "resizable=0," +
        "width=700," +
        "height=500," +
        "left = 350," +
        "top=100"
    );
    popUpObj.focus();
  });
});

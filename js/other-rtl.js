// Search Apps
$(document).on("keydown", "#search-form", function (e) {
  var id = this.id;
  var base_url = document
    .querySelector("meta[property='base_url']")
    .getAttribute("content");
  var search_url = base_url + "/" + "json-search";

  $("#" + id).autocomplete({
    source: function (request, response) {
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: search_url,
        type: "post",
        dataType: "json",
        data: {
          search: request.term,
          request: 1,
        },
        success: function (data) {
          response(data);
        },
      });
    },
    select: function (event, ui) {
      window.location.href = ui.item.url;
      return false;
    },
    open: function () {
      $("ul.ui-menu").width($(this).innerWidth());
    },
    create: function (event, ui) {
      $(this).data("ui-autocomplete")._renderItem = function (ul, item) {
        var rich_html =
          '<div class="d-flex flex-row"><div><img src=\'' +
          item.image +
          '\' class="ms-2 rounded" /></div><div class="my-auto"><strong>' +
          item.title +
          "</strong>" +
          "</span></div></div>";
        return $("<li></li>")
          .data("item.autocomplete", item)
          .append(rich_html)
          .appendTo(ul);
      };
    },
  });
});

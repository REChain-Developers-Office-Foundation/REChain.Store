// Search Apps
$(document).on("keydown", "#search-form", function (e) {
  var id = this.id;
  var base_url = document
    .querySelector("meta[property='base_url']")
    .getAttribute("content");
  var search_url = base_url + "/" + "json-search";

  $("#" + id).autocomplete({
    minLength: 0,
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
          '\' class="rounded" /></div><div class="my-auto"><strong>' +
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

// Social media share
function sm_share(url, title, w, h) {
  "use strict";

  var dualScreenLeft =
    window.screenLeft != undefined ? window.screenLeft : screen.left;
  var dualScreenTop =
    window.screenTop != undefined ? window.screenTop : screen.top;

  var width = window.innerWidth
    ? window.innerWidth
    : document.documentElement.clientWidth
    ? document.documentElement.clientWidth
    : screen.width;
  var height = window.innerHeight
    ? window.innerHeight
    : document.documentElement.clientHeight
    ? document.documentElement.clientHeight
    : screen.height;

  var left = width / 2 - w / 2 + dualScreenLeft;
  var top = height / 2 - h / 2 + dualScreenTop;
  var newWindow = window.open(
    url,
    title,
    "scrollbars=yes, width=" +
      w +
      ", height=" +
      h +
      ", top=" +
      top +
      ", left=" +
      left
  );

  if (window.focus) {
    newWindow.focus();
  }
}

// Infinite Scroll
$(document).ready(function () {
  "use strict";

  if (document.getElementById("infinite-scroll")) {
    $("#infinite-scroll").infiniteScroll({
      path: ".pagination__next",
      append: ".infinity-scroll",
      history: false,
      hideNav: ".pagination-next",
      status: ".page-load-status",
    });
  }
});

// Apps star ratings
$(function () {
  "use strict";

  $(".ratings").rating(function (vote, event, data_vote_id) {
    $.ajax({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      url: "/vote/" + data_vote_id + "",
      type: "POST",
      data: {
        vote: vote,
      },
      success: function (data) {
        $(".voteinfo").fadeIn("slow");
        $(".voteinfo").html(data);
        $(".voteinfo").delay(2000).fadeOut("slow");
      },
    });
  });
});

// Comment & Review form apps star ratings
$(function () {
  "use strict";

  $(".user_ratings").rating(function () {});
});

$(document).ready(function () {
  "use strict";

  $("#terms a").click(function (e) {
    var txt = $(e.target).text();
    $("#search-form").val(txt);
    $("#search-form").keydown();
    $("#search-form").keydown();
    return false;
  });
});

// Redirections
$(document).ready(function () {
  "use strict";

  if (window.location.href.indexOf("/redirect/") > -1) {
    var id_data = document.getElementById("redirect");
    var app_id = id_data.getAttribute("data-app-id");
    var app_delay = id_data.getAttribute("data-redirection-delay");

    let time = app_delay / 1000 - 1;
    let countdown = setInterval(update, 1000);

    function update() {
      let min = Math.floor(time);
      let sec = time;
      document.getElementById("countdown").innerHTML = `(${sec})`;
      time--;
      min == 0 && sec == 0 ? clearInterval(countdown) : countdown;
    }

    var base_url = document
      .querySelector("meta[property='base_url']")
      .getAttribute("content");

    window.setTimeout(function () {
      window.location.href = base_url + "/download/" + app_id + "";
    }, app_delay);
  }
});

// Screenshot slider
$(document).ready(function () {
  "use strict";

  $("#right").click(function () {
    var leftPos = $("#screenshot-main").scrollLeft();
    $("#screenshot-main").animate(
      {
        scrollLeft: leftPos + 250,
      },
      600
    );
  });

  $("#left").click(function () {
    var leftPos2 = $("#screenshot-main").scrollLeft();
    $("#screenshot-main").animate(
      {
        scrollLeft: leftPos2 - 250,
      },
      600
    );
  });

  var ar = new Array(33, 34, 35, 36, 37, 38, 39, 40);

  $(document).keydown(function (e) {
    var key = e.which;
    if ($.inArray(key, ar) > -1) {
      e.preventDefault();
      return false;
    }
    return true;
  });
});

// Smooth Scroll
$(document).ready(function () {
  "use strict";

  $(".add-comment").click(function () {
    event.preventDefault();
    document.querySelector(".comment-box").style.display = "block";
    scrollSmoothTo("review-title");
  });
});

function scrollSmoothTo(elementId) {
  "use strict";

  var element = document.getElementById(elementId);
  element.scrollIntoView({
    block: "start",
    behavior: "smooth",
  });
}

// ReadMoreJS
/**
 * @app ReadMoreJS
 * @desc Breaks the content of an element to the specified number of words
 * @version 1.1.0
 * @license The MIT License (MIT)
 * @author George Raptis | http://georap.gr
 */
(function (win, doc, undef) {
  "use strict";

  var RM = {};

  RM.helpers = {
    extendObj: function () {
      for (var i = 1, l = arguments.length; i < l; i++) {
        for (var key in arguments[i]) {
          if (arguments[i].hasOwnProperty(key)) {
            if (
              arguments[i][key] &&
              arguments[i][key].constructor &&
              arguments[i][key].constructor === Object
            ) {
              arguments[0][key] = arguments[0][key] || {};
              this.extendObj(arguments[0][key], arguments[i][key]);
            } else {
              arguments[0][key] = arguments[i][key];
            }
          }
        }
      }
      return arguments[0];
    },
  };

  RM.countWords = function (str) {
    return str.split(/\s+/).length;
  };

  RM.generateTrimmed = function (str, wordsNum) {
    return str.split(/\s+/).slice(0, wordsNum).join(" ") + "...";
  };

  RM.init = function (options) {
    var defaults = {
      target: "",
      numOfWords: 50,
      toggle: true,
      moreLink: "read more...",
      lessLink: "read less",
      linkClass: "rm-link",
      containerClass: false,
    };
    options = RM.helpers.extendObj({}, defaults, options);

    var target = doc.querySelectorAll(options.target),
      targetLen = target.length,
      targetContent,
      trimmedTargetContent,
      targetContentWords,
      initArr = [],
      trimmedArr = [],
      i,
      j,
      l,
      moreContainer,
      rmLink,
      moreLinkID,
      index;

    for (i = 0; i < targetLen; i++) {
      targetContent = target[i].innerHTML;
      trimmedTargetContent = RM.generateTrimmed(
        targetContent,
        options.numOfWords
      );
      targetContentWords = RM.countWords(targetContent);

      initArr.push(targetContent);
      trimmedArr.push(trimmedTargetContent);

      if (options.numOfWords < targetContentWords - 1) {
        target[i].innerHTML = trimmedArr[i];

        moreContainer = doc.createElement("div");
        if (options.containerClass) {
          moreContainer.className = options.containerClass;
        }

        moreContainer.innerHTML =
          '<a id="rm-more_' +
          i +
          '"' +
          ' class="' +
          options.linkClass +
          '"' +
          ' style="cursor:pointer;" data-readmore="anchor">' +
          options.moreLink +
          "</a>";
        target[i].parentNode.insertBefore(moreContainer, target[i].nextSibling);
      }
    }

    rmLink = doc.querySelectorAll('[data-readmore="anchor"]');

    for (j = 0, l = rmLink.length; j < l; j++) {
      rmLink[j].onclick = function () {
        moreLinkID = this.getAttribute("id");
        index = moreLinkID.split("_")[1];

        if (this.getAttribute("data-clicked") !== "true") {
          target[index].innerHTML = initArr[index];
          if (options.toggle !== false) {
            this.innerHTML = options.lessLink;
            this.setAttribute("data-clicked", true);
          } else {
            this.innerHTML = "";
          }
        } else {
          target[index].innerHTML = trimmedArr[index];
          this.innerHTML = options.moreLink;
          this.setAttribute("data-clicked", false);
        }
      };
    }
  };

  window.$readMoreJS = RM;
})(this, this.document);

// Validate Email
function validateEmail($email) {
  "use strict";

  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test($email);
}

// Comment Form Control
function form_control() {
  "use strict";

  var name = $.trim($("#name").val());
  var email = $.trim($("#email").val());
  var title = $.trim($("#title").val());
  var comment = $.trim($("#comment").val());
  if (document.getElementById("rating")) {
    var voting_data = document.getElementById("rating");
    var rating_id = voting_data.getAttribute("data-rating-id");
    var type = 1;
  } else {
    var type = 2;
  }
  var comment_data = document.getElementById("comment-section");
  var fill_all_fields = comment_data.getAttribute("data-fill-all-fields");

  if (validateEmail(email) && name != "" && title != "" && comment != "") {
    comment_send(type);
  } else {
    $("#comment_result").html(
      '<div class="alert alert-danger show mt-3 mb-2" role="alert">' +
        fill_all_fields +
        ""
    );
  }
}

// Post Comment
function comment_send(type) {
  "use strict";

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    type: "POST",
    url: "/comment",
    data: $("#comment-form").serialize(),
    success: function (msg) {
      $("#comment-section :input").prop("disabled", true);
      $("#comment_result").html(msg);
      $("#comment-form")[0].reset();
    },
  });
}

// Submission Form Control
function submission_form_control() {
  "use strict";

  var name = $.trim($("#name").val());
  var email = $.trim($("#email").val());
  var title = $.trim($("#title").val());
  var description = $.trim($("#description").val());
  var category = $.trim($("#category").val());
  var platform = $.trim($("#platform").val());
  var developer = $.trim($("#developer").val());
  var url = $.trim($("#url").val());
  var license = $.trim($("#license").val());
  var file_size = $.trim($("#file-size").val());
  var version = $.trim($("#version").val());
  var detailed_description = $.trim($("#detailed-description").val());
  var image = $.trim($("#image").val());
  var submission_data = document.getElementById("submission-section");
  var fill_all_fields = submission_data.getAttribute("data-fill-all-fields");

  if (
    validateEmail(email) &&
    name != "" &&
    title != "" &&
    description != "" &&
    category != "" &&
    platform != "" &&
    developer != "" &&
    url != ""
  ) {
    submission_send();
  } else {
    $("#submission-result").html(
      '<div class="alert alert-danger show mt-2 mb-2" role="alert">' +
        fill_all_fields +
        ""
    );
  }
}

// Post Submission
function submission_send() {
  "use strict";

  var frm = $("#submission-form");
  var formData = new FormData(frm[0]);
  formData.append("image", $("input[type=file]")[0].files[0]);

  try {
    var recaptcha = grecaptcha.getResponse();
    formData.append("recaptcha", recaptcha);
  } catch (e) {}

  var submission_data = document.getElementById("submission-section");
  var data_error = submission_data.getAttribute("data-error");
  var data_recaptcha_error = submission_data.getAttribute(
    "data-recaptcha-error"
  );

  const recaptcha_errors = [
    "missing-input-secret",
    "invalid-input-secret",
    "missing-input-response",
    "invalid-input-response",
    "bad-request",
    "timeout-or-duplicate",
  ];

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    type: "POST",
    enctype: "multipart/form-data",
    url: "/submission",
    data: formData,
    processData: false,
    contentType: false,
    success: function (msg) {
      $("#submission-section :input").prop("disabled", true);
      $("#submission-result").html(msg);
      $("#submission-form")[0].reset();
    },
    error: function (xhr) {
      $.each(xhr.responseJSON.errors, function (key, value) {
        if (recaptcha_errors.indexOf(value) > -1) {
          grecaptcha.reset();
          $("#submission-result").html(
            '<div class="alert alert-danger mb-2 mt-2" role="alert"><b>' +
              data_error +
              ":</b> " +
              data_recaptcha_error +
              "</div"
          );
          return false;
        }

        $("#submission-result").html(
          '<div class="alert alert-danger mb-2 mt-2" role="alert"><b>' +
            data_error +
            ":</b> " +
            value +
            "</div"
        );
        return false;
      });
    },
  });
}

// Change position of elements in the app page
function moveDiv() {
  if ($(window).width() < 990) {
    $("#download_section").insertAfter("#app_data");
    $("#popular_apps").addClass("mt-3");
    $("#download_section").addClass("mt-3");
  } else {
    $("#download_section").insertBefore("#move_item");
    $("#popular_apps").removeClass("mt-3");
    $("#download_section").removeClass("mt-3");
  }
}
$(document).ready(function () {
  moveDiv();
  $(window).resize(function () {
    moveDiv();
  });
});

// Report Submission Form Control
function report_submission_form() {
  "use strict";

  var email = $.trim($("#email").val());
  var reason = $.trim($("#reason").val());

  report_submission_send();
}

// Report Submission
function report_submission_send() {
  "use strict";

  var frm = $("#report-submission-form");
  var formData = new FormData(frm[0]);

  try {
    var recaptcha = grecaptcha.getResponse();
    formData.append("recaptcha", recaptcha);
  } catch (e) {}

  var submission_data = document.getElementById("report-submission-section");
  var data_error = submission_data.getAttribute("data-error");
  var app_id = submission_data.getAttribute("data-station-id");

  formData.append("app_id", app_id);

  var data_recaptcha_error = submission_data.getAttribute(
    "data-recaptcha-error"
  );

  const recaptcha_errors = [
    "missing-input-secret",
    "invalid-input-secret",
    "missing-input-response",
    "invalid-input-response",
    "bad-request",
    "timeout-or-duplicate",
  ];

  var base_url = document
    .querySelector("meta[property='base_url']")
    .getAttribute("content");
  var report_url = base_url + "/" + "report";

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    type: "POST",
    url: report_url,
    data: formData,
    processData: false,
    contentType: false,
    success: function (msg) {
      $("#report-submission-form")[0].reset();
      $(".g-recaptcha").remove();
      $("#report-submission-section :input").prop("disabled", true);
      $("#report-submission-result").html(msg);
    },
    error: function (xhr) {
      $.each(xhr.responseJSON.errors, function (key, value) {
        if (recaptcha_errors.indexOf(value) > -1) {
          grecaptcha.reset();
          $("#report-submission-result").html(
            '<div class="alert alert-danger mt-3 show" role="alert"><b>' +
              data_error +
              ":</b> " +
              data_recaptcha_error +
              "</div"
          );
          return false;
        }

        $("#report-submission-result").html(
          '<div class="alert alert-danger mt-3 show" role="alert"><b>' +
            data_error +
            ":</b> " +
            value +
            "</div"
        );
        return false;
      });
    },
  });
}

// Add to Favorites
$(document).ready(function () {
  $(".add-favorites").click(function () {
    var check_status = $("#heart").data("checked");

    if (check_status == "1") {
      $("#heart").css("fill", "#f4aec2");
      $("#heart").data("checked", "0");

      var player_data = document.getElementById("app");
      var player_thumbnail = player_data.getAttribute("data-thumb");
      var player_url = player_data.getAttribute("data-url");
      var player_title = player_data.getAttribute("data-title");
      var remove_message = player_data.getAttribute("data-remove-message");
      var cookie_prefix = player_data.getAttribute("data-cookie-prefix");

      var listen_history_last = Cookies.get(cookie_prefix + "_favorites");

      var listen_history = Cookies.get(cookie_prefix + "_favorites");
      var listen_data =
        "|" + player_url + "," + player_thumbnail + "," + player_title;
      var listen_historyy = listen_history_last.replace(listen_data, "");

      Cookies.set(cookie_prefix + "_favorites", listen_historyy, {
        expires: 365,
      });

      var notify = new notificationManager({
        container: $("#notificationsContainer"),
      });

      var anim = true;
      var auto = true;

      notify.setPosition("topright");

      notify.addNotification({
        message: remove_message,
        animate: anim,
        autoRemove: auto,
        backgroundColor: "#92c66c",
        progressColor: "#ffffff",
      });
    }

    if (check_status == "0") {
      var player_data = document.getElementById("app");
      var player_thumbnail = player_data.getAttribute("data-thumb");
      var player_url = player_data.getAttribute("data-url");
      var player_title = player_data.getAttribute("data-title");
      var add_message = player_data.getAttribute("data-add-message");
      var cookie_prefix = player_data.getAttribute("data-cookie-prefix");

      var favorite_history = Cookies.get(cookie_prefix + "_favorites");
      var favorite_data =
        "|" + player_url + "," + player_thumbnail + "," + player_title;

      if (favorite_history == undefined) {
        Cookies.set(cookie_prefix + "_favorites", favorite_data, {
          expires: 365,
        });
        favorite_history = favorite_data;
      }

      if (favorite_history.indexOf(favorite_data) === -1) {
        Cookies.set(
          cookie_prefix + "_favorites",
          favorite_history + favorite_data,
          {
            expires: 365,
          }
        );
      }

      var favorite_history_last = Cookies.get(cookie_prefix + "_favorites");

      if (favorite_history_last.indexOf(favorite_data) != -1) {
        var favorite_historyy = favorite_history_last.replace(
          favorite_data,
          ""
        );

        Cookies.set(
          cookie_prefix + "_favorites",
          favorite_historyy + favorite_data,
          {
            expires: 365,
          }
        );
      }

      $("#heart").css("fill", "#fff");
      $("#heart").data("checked", "1");

      var notify = new notificationManager({
        container: $("#notificationsContainer"),
      });

      var anim = true;
      var auto = true;

      notify.setPosition("topright");

      notify.addNotification({
        message: add_message,
        animate: anim,
        autoRemove: auto,
        backgroundColor: "#92c66c",
        progressColor: "#ffffff",
      });
    }
  });
});

// Contact Form Control
function contact_form() {
  "use strict";

  var name = $.trim($("#name").val());
  var email = $.trim($("#email").val());
  var subject = $.trim($("#subject").val());
  var message = $.trim($("#message").val());

  contact_form_send();
}

// Contact Form Submission
function contact_form_send() {
  "use strict";

  var frm = $("#contact-form");
  var formData = new FormData(frm[0]);
  try {
    var recaptcha = grecaptcha.getResponse();
    formData.append("recaptcha", recaptcha);
  } catch (e) {}

  var submission_data = document.getElementById("contact-form-section");
  var data_error = submission_data.getAttribute("data-error");
  var data_recaptcha_error = submission_data.getAttribute(
    "data-recaptcha-error"
  );

  const recaptcha_errors = [
    "missing-input-secret",
    "invalid-input-secret",
    "missing-input-response",
    "invalid-input-response",
    "bad-request",
    "timeout-or-duplicate",
  ];

  $.ajax({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    type: "POST",
    url: "contact-form",
    data: formData,
    processData: false,
    contentType: false,
    success: function (msg) {
      $("#contact-form")[0].reset();
      $(".g-recaptcha").remove();
      $("#contact-form-section :input").prop("disabled", true);
      $("#contact-form-result").html(msg);
    },
    error: function (xhr) {
      $.each(xhr.responseJSON.errors, function (key, value) {
        if (recaptcha_errors.indexOf(value) > -1) {
          grecaptcha.reset();
          $("#contact-form-result").html(
            '<div class="alert alert-danger show" role="alert"><b>' +
              data_error +
              ":</b> " +
              data_recaptcha_error +
              "</div"
          );
          return false;
        }

        $("#contact-form-result").html(
          '<div class="alert alert-danger show" role="alert"><b>' +
            data_error +
            ":</b> " +
            value +
            "</div"
        );
        return false;
      });
    },
  });
}

// Rate the App
$(document).ready(function () {
  $(".rate_app").click(function () {
    var app_id = $(this).attr("data-id");
    var rate_action = $(this).attr("data-action");

    var vote_data = document.getElementById("vote-data");
    var success_message = vote_data.getAttribute("data-vote-success");
    var error_message = vote_data.getAttribute("data-vote-error");

    $.ajax({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      url: "/vote",
      type: "post",
      dataType: "json",
      data: {
        app_id: app_id,
        direction: rate_action,
      },
      success: function (response) {
        if (response.success == true) {
          var notify = new notificationManager({
            container: $("#notificationsContainer"),
          });

          var anim = true;
          var auto = true;

          notify.setPosition("topright");

          notify.addNotification({
            message: success_message,
            animate: anim,
            autoRemove: auto,
            backgroundColor: "#92c66c",
            progressColor: "#fff",
          });

          ajaxCallBack(response.vote);
        } else {
          var notify = new notificationManager({
            container: $("#notificationsContainer"),
          });

          var anim = true;
          var auto = true;

          notify.setPosition("topright");

          notify.addNotification({
            message: error_message,
            animate: anim,
            autoRemove: auto,
            backgroundColor: "#FFC300",
            progressColor: "#fff",
          });
        }
      },
    });

    function ajaxCallBack(retString) {
      thevalue_d = retString;
      $('[data-id="' + app_id + '"] [id="' + rate_action + '"]').text(
        thevalue_d
      );
    }
  });
});

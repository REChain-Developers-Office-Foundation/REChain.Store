// Lazy Load
$(document).ready(function () {
  "use strict";

  $(".lazy").Lazy({
    scrollDirection: "vertical",
    effect: "fadeIn",
    visibleOnly: true,
    onError: function (element) {
      console.log("error loading " + element.data("src"));
    },
  });
});

/*
 * Bootstrap Cookie Alert by Wruczek
 * https://github.com/Wruczek/Bootstrap-Cookie-Alert
 * Released under MIT license
 */
(function () {
  "use strict";

  var cookieAlert = document.querySelector(".cookiealert");
  var acceptCookies = document.querySelector(".accept-cookies");

  if (!cookieAlert) {
    return;
  }

  cookieAlert.offsetHeight; // Force browser to trigger reflow (https://stackoverflow.com/a/39451131)

  // Show the alert if we cant find the "acceptCookies" cookie
  if (!getCookie("acceptCookies")) {
    cookieAlert.classList.add("show");
  }

  // When clicking on the agree button, create a 1 year
  // cookie to remember user's choice and close the banner
  acceptCookies.addEventListener("click", function () {
    setCookie("acceptCookies", true, 365);
    cookieAlert.classList.remove("show");
  });

  // Cookie functions from w3schools
  function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) === " ") {
        c = c.substring(1);
      }
      if (c.indexOf(name) === 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
})();

// Show more/less content
$(document).ready(function () {
  "use strict";

  if (document.getElementById("app-description")) {
    var pagination_data = document.getElementById("app-description");
    var show_more = pagination_data.getAttribute("data-show-more");
    var show_less = pagination_data.getAttribute("data-show-less");

    $readMoreJS.init({
      target: ".app-description",
      numOfWords: 75,
      toggle: true,
      moreLink: show_more,
      lessLink: show_less,
    });
  }
});

// Show progress bar using data attributes
$(document).ready(function () {
  "use strict";

  $(".progress > div").css("width", function () {
    return $(this).parent().data("bar-width") + "%";
  });
});

// Main Slider
$(document).ready(function () {
  "use strict";

  if (document.getElementById("swiper-main")) {
    var swiper = new Swiper(".swiper-main", {
      loop: true,
      autoplay: {
        delay: 3500,
      },
      pagination: {
        el: ".swiper-pagination-main",
        clickable: true,
        renderBullet: function (index, className) {
          return '<span class="' + className + '">' + (index + 1) + "</span>";
        },
      },

      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
    $(".swiper-pagination-bullet").hover(function () {
      "use strict";

      $(this).trigger("click");
    });
  }
});

// Rating Function
(function (a, d) {
  "use strict";
  a.fn.rating = function (b) {
    b = b || function () {};
    this.each(function (d, c) {
      a(c)
        .data("rating", {
          callback: b,
        })
        .bind("init.rating", a.fn.rating.init)
        .bind("set.rating", a.fn.rating.set)
        .bind("hover.rating", a.fn.rating.hover)
        .trigger("init.rating");
    });
  };
  a.extend(a.fn.rating, {
    init: function (h) {
      var d = a(this),
        g = "",
        j = null,
        f = d.children(),
        c = 0,
        b = f.length;
      for (; c < b; c++) {
        g = g + '<a class="star" title="' + a(f[c]).val() + '" />';
        if (a(f[c]).is(":checked")) {
          j = a(f[c]).val();
        }
      }
      f.hide();
      d.append('<div class="stars">' + g + "</div>").trigger("set.rating", j);
      a("a", d).bind("click", a.fn.rating.click);
      d.trigger("hover.rating");
    },
    set: function (f, g) {
      var c = a(this),
        d = a("a", c),
        b = undefined;
      if (g) {
        d.removeClass("fullStar");
        b = d.filter(function (e) {
          if (a(this).attr("title") == g) {
            return a(this);
          } else {
            return false;
          }
        });
        b.addClass("fullStar").prevAll().addClass("fullStar");
      }
      return;
    },
    hover: function (d) {
      var c = a(this),
        b = a("a", c);
      b.bind("mouseenter", function (f) {
        a(this).addClass("tmp_fs").prevAll().addClass("tmp_fs");
        a(this).nextAll().addClass("tmp_es");
      });
      b.bind("mouseleave", function (f) {
        a(this).removeClass("tmp_fs").prevAll().removeClass("tmp_fs");
        a(this).nextAll().removeClass("tmp_es");
      });
    },
    click: function (g) {
      g.preventDefault();

      var f = a(g.target),
        c = f.parent().parent(),
        b = c.children("input"),
        d = f.attr("title");
      var matchInput = b.filter(function (e) {
        if (a(this).val() == d) {
          return true;
        } else {
          return false;
        }
      });
      matchInput.attr("checked", true);
      var sort_type = document.getElementsByClassName("user_ratings")[0].id;

      var rating_data = document.getElementById("rating");
      var sort_type = rating_data.getAttribute("data-rating-id");

      c.trigger("set.rating", matchInput.val())
        .data("rating")
        .callback(d, g, sort_type);
    },
  });
})(jQuery);

// Tooltips
$(function () {
  "use strict";

  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});

// SimpleLightbox
$(function () {
  "use strict";

  if (document.getElementById("screenshot-main")) {
    $("#screenshot-main a").simpleLightbox();
  }
});

if (document.getElementById("app")) {
  var player_data = document.getElementById("app");
  var player_thumbnail = player_data.getAttribute("data-thumb");
  var player_url = player_data.getAttribute("data-url");
  var player_title = player_data.getAttribute("data-title");
  var cookie_prefix = player_data.getAttribute("data-cookie-prefix");

  var listen_history = Cookies.get(cookie_prefix + "_history");
  var listen_data =
    "|" + player_url + "," + player_thumbnail + "," + player_title;

  if (listen_history == undefined) {
    Cookies.set(cookie_prefix + "_history", listen_data, {
      expires: 365,
    });
    listen_history = listen_data;
  }

  if (listen_history.indexOf(listen_data) === -1) {
    Cookies.set(cookie_prefix + "_history", listen_history + listen_data, {
      expires: 365,
    });
  }

  var listen_history_last = Cookies.get(cookie_prefix + "_history");

  if (listen_history_last.indexOf(listen_data) != -1) {
    var listen_historyy = listen_history_last.replace(listen_data, "");

    Cookies.set(cookie_prefix + "_history", listen_historyy + listen_data, {
      expires: 365,
    });
  }

  var cookie_prefix = player_data.getAttribute("data-cookie-prefix");
  var favorite_history = Cookies.get(cookie_prefix + "_favorites");
  var favorite_data = "|" + player_url + "," + player_thumbnail;
  if (
    favorite_history != undefined &&
    favorite_history.indexOf(favorite_data) != -1
  ) {
    $("#heart").css("fill", "#fff");
    $("#heart").data("checked", "1");
  }
}

if (document.getElementById("favorites-page")) {
  var favorites_page = document.getElementById("favorites-page");
  var data_cookie = favorites_page.getAttribute("data-cookie-name");
  var data_no_favorites = favorites_page.getAttribute("data-no-favorites");

  var ked = Cookies.get(data_cookie);

  var i = 0;
  if (ked != undefined && ked != "") {
    ked_values = ked.split("|");

    $.each(ked_values, function (imagee) {
      values = ked_values[i].split(",");

      if (values.length > 1) {
        $(".app-list").prepend(
          '<div class="col-4 col-md-1-5 mb-2"><a href="' +
            values[0] +
            '"><img src="/images/pixel.png" data-src="' +
            values[1] +
            '" width="300" height="300" alt="" class="img-fluid lazy"></a><span class="title mt-2">' +
            values[2] +
            "</span></div>"
        );
      }
      i++;
    });
  } else {
    $(".app-list").prepend(
      '<div class="col-md-12 col-12 mb-2">' + data_no_favorites + "</div>"
    );
  }
}

if (document.getElementById("history-page")) {
  var history_page = document.getElementById("history-page");
  var data_cookie = history_page.getAttribute("data-cookie-name");
  var data_no_history = history_page.getAttribute("data-no-history");

  var ked = Cookies.get(data_cookie);

  var i = 0;
  if (ked != undefined && ked != "") {
    ked_values = ked.split("|");

    $.each(ked_values, function (imagee) {
      values = ked_values[i].split(",");

      if (values.length > 1) {
        $(".app-list").prepend(
          '<div class="col-4 col-md-1-5 mb-2"><a href="' +
            values[0] +
            '"><img src="/images/pixel.png" data-src="' +
            values[1] +
            '" width="300" height="300" alt="" class="img-fluid lazy"></a><span class="title mt-2">' +
            values[2] +
            "</span></div>"
        );
      }
      i++;
    });
  } else {
    $(".app-list").prepend(
      '<div class="col-md-12 col-12 mb-2">' + data_no_history + "</div>"
    );
  }
}

(function () {
  "use strict";

  document
    .querySelector("#navbarSideCollapse")
    .addEventListener("click", function () {
      document.querySelector(".offcanvas-collapse").classList.toggle("open");
    });

  document.querySelector("#closeMenu").addEventListener("click", function () {
    $("#navbarSideCollapse").click();
  });
})();

(function ($) {
  ("use strict");
  /*=================================
      JS Index Here
  ==================================*/
  /*
    01. On Load Function
    02. Preloader
    03. Mobile Menu Active
    04. Sticky fix
    05. Scroll To Top
    06. Set Background Image
    07. Hero Slider Active 
    08. Global Slider
    09. Ajax Contact Form
    10. Magnific Popup
    11. Filter
    12. Popup Sidemenu
    13. Search Box Popup
    14. Accordion Class Toggle
    15. Count Down
    16. Shape Mockup
    17. Range Slider
    18. Woocommerce Toggle
    19. Quantity Added
  */
  /*=================================
      JS Index End
  ==================================*/
  /*

  /*---------- 01. On Load Function ----------*/
  $(window).on("load", function () {
    $(".preloader").fadeOut();
  });

  /*---------- 02. Preloader ----------*/
  if ($(".preloader").length > 0) {
    $(".preloaderCls").each(function () {
      $(this).on("click", function (e) {
        e.preventDefault();
        $(".preloader").css("display", "none");
      });
    });
  }

  /*---------- 03. Mobile Menu Active ----------*/
  $.fn.vsmobilemenu = function (options) {
    var opt = $.extend({
      menuToggleBtn: ".vs-menu-toggle",
      bodyToggleClass: "vs-body-visible",
      subMenuClass: "vs-submenu",
      subMenuParent: "vs-item-has-children",
      subMenuParentToggle: "vs-active",
      meanExpandClass: "vs-mean-expand",
      appendElement: '<span class="vs-mean-expand"></span>',
      subMenuToggleClass: "vs-open",
      toggleSpeed: 400,
    },
      options
    );

    return this.each(function () {
      var menu = $(this); // Select menu

      // Menu Show & Hide
      function menuToggle() {
        menu.toggleClass(opt.bodyToggleClass);

        // collapse submenu on menu hide or show
        var subMenu = "." + opt.subMenuClass;
        $(subMenu).each(function () {
          if ($(this).hasClass(opt.subMenuToggleClass)) {
            $(this).removeClass(opt.subMenuToggleClass);
            $(this).css("display", "none");
            $(this).parent().removeClass(opt.subMenuParentToggle);
          }
        });
      }

      // Class Set Up for every submenu
      menu.find("li").each(function () {
        var submenu = $(this).find("ul");
        submenu.addClass(opt.subMenuClass);
        submenu.css("display", "none");
        submenu.parent().addClass(opt.subMenuParent);
        submenu.prev("a").append(opt.appendElement);
        submenu.next("a").append(opt.appendElement);
      });

      // Toggle Submenu
      function toggleDropDown($element) {
        if ($($element).next("ul").length > 0) {
          $($element).parent().toggleClass(opt.subMenuParentToggle);
          $($element).next("ul").slideToggle(opt.toggleSpeed);
          $($element).next("ul").toggleClass(opt.subMenuToggleClass);
        } else if ($($element).prev("ul").length > 0) {
          $($element).parent().toggleClass(opt.subMenuParentToggle);
          $($element).prev("ul").slideToggle(opt.toggleSpeed);
          $($element).prev("ul").toggleClass(opt.subMenuToggleClass);
        }
      }

      // Submenu toggle Button
      var expandToggler = "." + opt.meanExpandClass;
      $(expandToggler).each(function () {
        $(this).on("click", function (e) {
          e.preventDefault();
          toggleDropDown($(this).parent());
        });
      });

      // Menu Show & Hide On Toggle Btn click
      $(opt.menuToggleBtn).each(function () {
        $(this).on("click", function () {
          menuToggle();
        });
      });

      // Hide Menu On out side click
      menu.on("click", function (e) {
        e.stopPropagation();
        menuToggle();
      });

      // Stop Hide full menu on menu click
      menu.find("div").on("click", function (e) {
        e.stopPropagation();
      });
    });
  };

  $(".vs-menu-wrapper").vsmobilemenu();

  /*---------- 04. Sticky fix ----------*/
  var lastScrollTop = "";
  var scrollToTopBtn = ".scrollToTop";

  function stickyMenu($targetMenu, $toggleClass, $parentClass) {
    var st = $(window).scrollTop();
    var height = $targetMenu.css("height");
    $targetMenu.parent().css("min-height", height);
    if ($(window).scrollTop() > 800) {
      $targetMenu.parent().addClass($parentClass);

      if (st > lastScrollTop) {
        $targetMenu.removeClass($toggleClass);
      } else {
        $targetMenu.addClass($toggleClass);
      }
    } else {
      $targetMenu.parent().css("min-height", "").removeClass($parentClass);
      $targetMenu.removeClass($toggleClass);
    }
    lastScrollTop = st;
  }
  $(window).on("scroll", function () {
    stickyMenu($(".sticky-active"), "active", "will-sticky");
    if ($(this).scrollTop() > 500) {
      $(scrollToTopBtn).addClass("show");
    } else {
      $(scrollToTopBtn).removeClass("show");
    }
  });

  /*---------- 05. Scroll To Top ----------*/
  $(scrollToTopBtn).each(function () {
    $(this).on("click", function (e) {
      e.preventDefault();
      $("html, body").animate({
        scrollTop: 0,
      },
        lastScrollTop / 3
      );
      return false;
    });
  });

  /*---------- 06. Set Background Image ----------*/
  if ($("[data-bg-src]").length > 0) {
    $("[data-bg-src]").each(function () {
      var src = $(this).attr("data-bg-src");
      $(this).css("background-image", "url(" + src + ")");
      $(this).removeAttr("data-bg-src").addClass("background-image");
    });
  }

  /*----------- 07. Hero Slider Active ----------*/
  $(".vs-hero-carousel").each(function () {
    var vsHslide = $(this);

    // Get Data From Dom
    function d(data) {
      return vsHslide.data(data);
    }

    vsHslide.layerSlider({
      globalBGColor: d('globalbgcolor') ? d('globalbgcolor') : false,
      allowRestartOnResize: true,
      maxRatio: d("maxratio") ? d("maxratio") : 1,
      type: d("slidertype") ? d("slidertype") : "responsive",
      pauseOnHover: d("pauseonhover") ? true : false,
      navPrevNext: d("navprevnext") ? true : false,
      hoverPrevNext: d("hoverprevnext") ? true : false,
      hoverBottomNav: d("hoverbottomnav") ? true : false,
      navStartStop: d("navstartstop") ? true : false,
      navButtons: d("navbuttons") ? true : false,
      loop: d("loop") === false ? false : true,
      autostart: d("autostart") ? true : false,
      height: d("height") ? d("height") : 1080,
      responsiveUnder: d("responsiveunder") ? d("responsiveunder") : 1220,
      layersContainer: d("container") ? d("container") : 1220,
      showCircleTimer: d("showcircletimer") ? true : false,
      skinsPath: "layerslider/skins/",
      thumbnailNavigation: d("thumbnailnavigation") === false ? false : true,
    });
  });

  /*----------- 08. Global Slider ----------*/
  $(".vs-carousel").each(function () {
    var asSlide = $(this);

    // Collect Data
    function d(data) {
      return asSlide.data(data);
    }

    // Custom Arrow Button
    var prevButton =
      '<button type="button" class="slick-prev"><i class="' +
      d("prev-arrow") +
      '"></i></button>',
      nextButton =
        '<button type="button" class="slick-next"><i class="' +
        d("next-arrow") +
        '"></i></button>';

    // Function For Custom Arrow Btn
    $("[data-slick-next]").each(function () {
      $(this).on("click", function (e) {
        e.preventDefault();
        $($(this).data("slick-next")).slick("slickNext");
      });
    });

    $("[data-slick-prev]").each(function () {
      $(this).on("click", function (e) {
        e.preventDefault();
        $($(this).data("slick-prev")).slick("slickPrev");
      });
    });

    // Check for arrow wrapper
    if (d("arrows") == true) {
      if (!asSlide.closest(".arrow-wrap").length) {
        asSlide.closest(".container").parent().addClass("arrow-wrap");
      }
    }

    asSlide.slick({
      dots: d("dots") ? true : false,
      fade: d("fade") ? true : false,
      arrows: d("arrows") ? true : false,
      speed: d("speed") ? d("speed") : 1000,
      asNavFor: d("asnavfor") ? d("asnavfor") : false,
      autoplay: d("autoplay") ? true : false,
      infinite: d("infinite") == false ? false : true,
      slidesToShow: d("slide-show") ? d("slide-show") : 1,
      adaptiveHeight: d("adaptive-height") ? true : false,
      centerMode: d("center-mode") ? true : false,
      autoplaySpeed: d("autoplay-speed") ? d("autoplay-speed") : 8000,
      centerPadding: d("center-padding") ? d("center-padding") : "0",
      focusOnSelect: d("focuson-select") == false ? false : true,
      pauseOnFocus: d("pauseon-focus") ? true : false,
      pauseOnHover: d("pauseon-hover") ? true : false,
      variableWidth: d("variable-width") ? true : false,
      vertical: d("vertical") ? true : false,
      verticalSwiping: d("vertical") ? true : false,
      prevArrow: d("prev-arrow") ?
        prevButton : '<button type="button" class="slick-prev"><i class="far fa-chevron-left"></i></button>',
      nextArrow: d("next-arrow") ?
        nextButton : '<button type="button" class="slick-next"><i class="far fa-chevron-right"></i></button>',
      rtl: $("html").attr("dir") == "rtl" ? true : false,
      responsive: [{
        breakpoint: 1600,
        settings: {
          arrows: d("xl-arrows") ? true : false,
          dots: d("xl-dots") ? true : false,
          slidesToShow: d("xl-slide-show") ?
            d("xl-slide-show") : d("slide-show"),
          centerMode: d("xl-center-mode") ? true : false,
          centerPadding: 0,
        },
      },
      {
        breakpoint: 1400,
        settings: {
          arrows: d("ml-arrows") ? true : false,
          dots: d("ml-dots") ? true : false,
          slidesToShow: d("ml-slide-show") ?
            d("ml-slide-show") : d("slide-show"),
          centerMode: d("ml-center-mode") ? true : false,
          centerPadding: 0,
        },
      },
      {
        breakpoint: 1200,
        settings: {
          arrows: d("lg-arrows") ? true : false,
          dots: d("lg-dots") ? true : false,
          slidesToShow: d("lg-slide-show") ?
            d("lg-slide-show") : d("slide-show"),
          centerMode: d("lg-center-mode") ? d("lg-center-mode") : false,
          centerPadding: 0,
        },
      },
      {
        breakpoint: 992,
        settings: {
          arrows: d("md-arrows") ? true : false,
          dots: d("md-dots") ? true : false,
          slidesToShow: d("md-slide-show") ? d("md-slide-show") : 1,
          centerMode: d("md-center-mode") ? d("md-center-mode") : false,
          centerPadding: 0,
        },
      },
      {
        breakpoint: 767,
        settings: {
          arrows: d("sm-arrows") ? true : false,
          dots: d("sm-dots") ? true : false,
          slidesToShow: d("sm-slide-show") ? d("sm-slide-show") : 1,
          centerMode: d("sm-center-mode") ? d("sm-center-mode") : false,
          centerPadding: 0,
        },
      },
      {
        breakpoint: 576,
        settings: {
          arrows: d("xs-arrows") ? true : false,
          dots: d("xs-dots") ? true : false,
          slidesToShow: d("xs-slide-show") ? d("xs-slide-show") : 1,
          centerMode: d("xs-center-mode") ? d("xs-center-mode") : false,
          centerPadding: 0,
        },
      },
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ],
    });
  });

  /*----------- 09. Ajax Contact Form ----------*/
  var form = ".ajax-contact";
  var invalidCls = "is-invalid";
  var $email = '[name="email"]';
  var $validation = '[name="firstname"],[name="message"]'; // Required fields only (lastname, email, number are conditionally required)
  var formMessages = $(".form-messages");

  function sendContact() {
    var formData = $(form).serialize();
    var valid;
    valid = validateContact();
    if (valid) {
      // Show sending message popup
      showSendingMessagePopup();
      
      jQuery
        .ajax({
          url: $(form).attr("action"),
          data: formData,
          type: "POST",
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        })
        .done(function (response) {
          // Hide sending message popup
          hideSendingMessagePopup();
          // Clear the form.
          $(form + ' input:not([type="submit"]),' + form + " textarea").val("");
          // Remove validation classes
          $(form + ' input, ' + form + ' textarea').removeClass(invalidCls);
          $('.error-message').text('');
          
          // Show SweetAlert2 success message
          Swal.fire({
            icon: 'success',
            title: 'Message Sent!',
            text: response || 'Thank you for contacting us! We will get back to you soon.',
            confirmButtonColor: '#490d59',
            confirmButtonText: 'OK'
          });
        })
        .fail(function (data) {
          // Hide sending message popup
          hideSendingMessagePopup();
          
          // Show SweetAlert2 error message
          var errorMessage = "Oops! An error occurred and your message could not be sent.";
          if (data.responseText !== "") {
            errorMessage = data.responseText;
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
          });
        });
    }
  }

  function showSendingMessagePopup() {
    // Create popup overlay if it doesn't exist
    if ($('#sending-message-popup').length === 0) {
      $('body').append(
        '<div id="sending-message-popup" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">' +
        '<div style="background:white;padding:30px 40px;border-radius:10px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.3);">' +
        '<div style="margin-bottom:15px;"><i class="fas fa-spinner fa-spin" style="font-size:32px;color:#490d59;"></i></div>' +
        '<p style="margin:0;font-size:16px;color:#1b130d;font-weight:500;">Sending message...</p>' +
        '</div>' +
        '</div>'
      );
    } else {
      $('#sending-message-popup').show();
    }
  }

  function hideSendingMessagePopup() {
    $('#sending-message-popup').hide();
  }

  function validateContact() {
    var valid = true;
    var formInput;
    var errorMessages = [];

    // Clear all previous error states
    $($validation).removeClass(invalidCls);
    $($email).removeClass(invalidCls);
    $('.error-message').text('').hide();

    // Validate First Name (Required)
    var firstName = $('#firstname').val().trim();
    if (firstName === "" || firstName.length === 0) {
      $('#firstname').addClass(invalidCls);
      $('#firstname-error').text('First name is required.').show();
      valid = false;
    } else if (firstName.length < 2) {
      $('#firstname').addClass(invalidCls);
      $('#firstname-error').text('First name must be at least 2 characters.').show();
      valid = false;
    } else if (firstName.length > 50) {
      $('#firstname').addClass(invalidCls);
      $('#firstname-error').text('First name must not exceed 50 characters.').show();
      valid = false;
    } else if (!/^[a-zA-Z\s'-]+$/.test(firstName)) {
      $('#firstname').addClass(invalidCls);
      $('#firstname-error').text('First name can only contain letters, spaces, hyphens, and apostrophes.').show();
      valid = false;
    } else {
      $('#firstname').removeClass(invalidCls);
      $('#firstname-error').hide();
    }

    // Validate Last Name (Optional - only validate if provided)
    var lastName = $('#lastname').val().trim();
    if (lastName.length > 0) {
      if (lastName.length < 2) {
        $('#lastname').addClass(invalidCls);
        $('#lastname-error').text('Last name must be at least 2 characters.').show();
        valid = false;
      } else if (lastName.length > 50) {
        $('#lastname').addClass(invalidCls);
        $('#lastname-error').text('Last name must not exceed 50 characters.').show();
        valid = false;
      } else if (!/^[a-zA-Z\s'-]+$/.test(lastName)) {
        $('#lastname').addClass(invalidCls);
        $('#lastname-error').text('Last name can only contain letters, spaces, hyphens, and apostrophes.').show();
        valid = false;
      } else {
        $('#lastname').removeClass(invalidCls);
        $('#lastname-error').hide();
      }
    } else {
      // Last name is optional, so clear any errors if empty
      $('#lastname').removeClass(invalidCls);
      $('#lastname-error').hide();
    }

    // Validate Email (Optional but must be valid if provided)
    var emailValue = $($email).val().trim();
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var emailValid = true;
    
    if (emailValue.length > 0) {
      if (!emailRegex.test(emailValue)) {
        $($email).addClass(invalidCls);
        $('#email-error').text('Please enter a valid email address (e.g., example@domain.com).').show();
        valid = false;
        emailValid = false;
      } else if (emailValue.length > 100) {
        $($email).addClass(invalidCls);
        $('#email-error').text('Email address must not exceed 100 characters.').show();
        valid = false;
        emailValid = false;
      } else {
        $($email).removeClass(invalidCls);
        $('#email-error').hide();
      }
    } else {
      $($email).removeClass(invalidCls);
      $('#email-error').hide();
    }

    // Validate Phone Number (Optional but must be valid if provided)
    var phoneValue = $('[name="number"]').val().trim();
    var phoneDigits = phoneValue.replace(/\D/g, '');
    var phoneValid = true;
    
    if (phoneValue.length > 0) {
      if (phoneDigits.length < 10) {
        $('[name="number"]').addClass(invalidCls);
        $('#number-error').text('Phone number must contain at least 10 digits.').show();
        valid = false;
        phoneValid = false;
      } else if (phoneDigits.length > 15) {
        $('[name="number"]').addClass(invalidCls);
        $('#number-error').text('Phone number must not exceed 15 digits.').show();
        valid = false;
        phoneValid = false;
      } else {
        $('[name="number"]').removeClass(invalidCls);
        $('#number-error').hide();
      }
    } else {
      $('[name="number"]').removeClass(invalidCls);
      $('#number-error').hide();
    }

    // Validate that at least Email OR Phone is provided
    if (emailValue.length === 0 && phoneValue.length === 0) {
      $($email).addClass(invalidCls);
      $('[name="number"]').addClass(invalidCls);
      $('#email-error').text('Either email address or phone number is required.').show();
      $('#number-error').text('Either email address or phone number is required.').show();
      valid = false;
    } else if (emailValue.length === 0 && phoneValue.length > 0 && phoneValid) {
      // Phone is provided and valid, clear email error
      $($email).removeClass(invalidCls);
      $('#email-error').hide();
    } else if (phoneValue.length === 0 && emailValue.length > 0 && emailValid) {
      // Email is provided and valid, clear phone error
      $('[name="number"]').removeClass(invalidCls);
      $('#number-error').hide();
    }

    // Validate Message (Required)
    var messageValue = $('#message').val().trim();
    if (messageValue === "" || messageValue.length === 0) {
      $('#message').addClass(invalidCls);
      $('#message-error').text('Message is required.').show();
      valid = false;
    } else if (messageValue.length < 10) {
      $('#message').addClass(invalidCls);
      $('#message-error').text('Message must be at least 10 characters long.').show();
      valid = false;
    } else if (messageValue.length > 1000) {
      $('#message').addClass(invalidCls);
      $('#message-error').text('Message must not exceed 1000 characters.').show();
      valid = false;
    } else {
      $('#message').removeClass(invalidCls);
      $('#message-error').hide();
    }

    // Show general error message if validation fails
    if (!valid) {
      formMessages.removeClass("success");
      formMessages.addClass("error");
      formMessages.text("Please correct the errors below and try again.");
    } else {
      formMessages.removeClass("error");
      formMessages.text("");
    }

    return valid;
  }

  $(form).on("submit", function (element) {
    element.preventDefault();
    sendContact();
  });

  // Real-time validation on input blur
  $('#firstname, #lastname, #email, #number, #message').on('blur', function() {
    validateContact();
  });

  // Clear error state on input (for better UX)
  $('#firstname, #message').on('input', function() {
    var $field = $(this);
    if ($field.val().trim().length > 0) {
      $field.removeClass(invalidCls);
      $('#' + $field.attr('id') + '-error').hide();
    }
  });

  // For email and phone - clear errors when either is filled (since only one is required)
  $('#email, #number').on('input', function() {
    var emailValue = $('#email').val().trim();
    var phoneValue = $('#number').val().trim();
    
    // If at least one is filled, clear the "either/or" error
    if (emailValue.length > 0 || phoneValue.length > 0) {
      $('#email-error').text('').hide();
      $('#number-error').text('').hide();
      $('#email').removeClass(invalidCls);
      $('#number').removeClass(invalidCls);
    }
  });

  // For lastname - clear errors when typing (optional field)
  $('#lastname').on('input', function() {
    var $field = $(this);
    $field.removeClass(invalidCls);
    $('#lastname-error').hide();
  });

  /*----------- 10. Magnific Popup ----------*/
  /* magnificPopup img view */
  $(".popup-image").magnificPopup({
    type: "image",
    gallery: {
      enabled: true,
    },
  });

  /* magnificPopup video view */
  $(".popup-video").magnificPopup({
    type: "iframe",
  });

  /*----------- 11. Filter ----------*/
  $(".filter-active").imagesLoaded(function () {
    var $filter = ".filter-active",
      $filterItem = ".filter-item",
      $filterMenu = ".filter-menu-active";

    if ($($filter).length > 0) {
      var $grid = $($filter).isotope({
        itemSelector: $filterItem,
        filter: "*",
        masonry: {
          // use outer width of grid-sizer for columnWidth
          columnWidth: $filterItem,
        },
      });

      // filter items on button click
      $($filterMenu).on("click", "button", function () {
        var filterValue = $(this).attr("data-filter");
        $grid.isotope({
          filter: filterValue,
        });
      });

      // Menu Active Class
      $($filterMenu).on("click", "button", function (event) {
        event.preventDefault();
        $(this).addClass("active");
        $(this).siblings(".active").removeClass("active");
      });
    }
  });


  /*---------- 12. Popup Sidemenu ----------*/
  function popupSideMenu($sideMenu, $sideMunuOpen, $sideMenuCls, $toggleCls) {
    // Sidebar Popup
    $($sideMunuOpen).on("click", function (e) {
      e.preventDefault();
      $($sideMenu).addClass($toggleCls);
    });
    $($sideMenu).on("click", function (e) {
      e.stopPropagation();
      $($sideMenu).removeClass($toggleCls);
    });
    var sideMenuChild = $sideMenu + " > div";
    $(sideMenuChild).on("click", function (e) {
      e.stopPropagation();
      $($sideMenu).addClass($toggleCls);
    });
    $($sideMenuCls).on("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $($sideMenu).removeClass($toggleCls);
    });
  }
  popupSideMenu(
    ".sidemenu-wrapper",
    ".sideMenuToggler",
    ".sideMenuCls",
    "show"
  );


  /*---------- 13. Search Box Popup ----------*/
  function popupSarchBox($searchBox, $searchOpen, $searchCls, $toggleCls) {
    $($searchOpen).on("click", function (e) {
      e.preventDefault();
      $($searchBox).addClass($toggleCls);
    });
    $($searchBox).on("click", function (e) {
      e.stopPropagation();
      $($searchBox).removeClass($toggleCls);
    });
    $($searchBox)
      .find("form")
      .on("click", function (e) {
        e.stopPropagation();
        $($searchBox).addClass($toggleCls);
      });
    $($searchCls).on("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      $($searchBox).removeClass($toggleCls);
    });
  }
  popupSarchBox(
    ".popup-search-box",
    ".searchBoxTggler",
    ".searchClose",
    "show"
  );



  /*----------- 14. Accordion Class Toggle ----------*/
  $(".accordion-button").on("click", function () {
    let btn = $(this).closest(".accordion-item");
    btn.toggleClass("active").siblings().removeClass("active");
  });



  /*----------- 15. Count Down ----------*/
  $.fn.countdown = function () {
    $(this).each(function () {
      var $counter = $(this),
        countDownDate = new Date($counter.data('end-date')).getTime(), // Set the date we're counting down toz
        exprireCls = 'expired';

      // Finding Function
      function s$(element) {
        return $counter.find(element);
      }

      // Update the count down every 1 second
      var counter = setInterval(function () {
        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // if low than 10 add zero
        function addZero(element) {
          return element < 10 ? '0' + element : element;
        }

        // If the count down is over, write some text 
        if (distance < 0) {
          clearInterval(counter);
          $counter.addClass(exprireCls);
          $counter.find('.message').css('display', 'block');
        } else {
          // Output the result in elements
          s$('.day').html(addZero(days))
          s$('.hour').html(addZero(hours))
          s$('.minute').html(addZero(minutes))
          s$('.seconds').html(addZero(seconds))
        }
      }, 1000);
    })
  }

  if ($('.countdown-active').length) {
    $('.countdown-active').countdown();
  }




  /*----------- 16. Shape Mockup ----------*/
  $.fn.shapeMockup = function () {
    var $shape = $(this);
    $shape.each(function () {
      var $currentShape = $(this),
        shapeTop = $currentShape.data("top"),
        shapeRight = $currentShape.data("right"),
        shapeBottom = $currentShape.data("bottom"),
        shapeLeft = $currentShape.data("left");
      $currentShape
        .css({
          top: shapeTop,
          right: shapeRight,
          bottom: shapeBottom,
          left: shapeLeft,
        })
        .removeAttr("data-top")
        .removeAttr("data-right")
        .removeAttr("data-bottom")
        .removeAttr("data-left")
        .parent()
        .addClass("shape-mockup-wrap");
    });
  };

  if ($(".shape-mockup")) {
    $(".shape-mockup").shapeMockup();
  }




  /*----------- 17. Range Slider ----------*/
  $("#slider-range").slider({
    range: true,
    min: 40,
    max: 300,
    values: [60, 570],
    slide: function (event, ui) {
      $("#minAmount").text("$" + ui.values[0]);
      $("#maxAmount").text("$" + ui.values[1]);
    },
  });
  $("#minAmount").text("$" + $("#slider-range").slider("values", 0));
  $("#maxAmount").text("$" + $("#slider-range").slider("values", 1));




  /*----------- 18. Woocommerce Toggle ----------*/
  // Ship To Different Address
  $("#ship-to-different-address-checkbox").on("change", function () {
    if ($(this).is(":checked")) {
      $("#ship-to-different-address").next(".shipping_address").slideDown();
    } else {
      $("#ship-to-different-address").next(".shipping_address").slideUp();
    }
  });

  // Login Toggle
  $(".woocommerce-form-login-toggle a").on("click", function (e) {
    e.preventDefault();
    $(".woocommerce-form-login").slideToggle();
  });

  // Coupon Toggle
  $(".woocommerce-form-coupon-toggle a").on("click", function (e) {
    e.preventDefault();
    $(".woocommerce-form-coupon").slideToggle();
  });

  // Woocommerce Shipping Method
  $(".shipping-calculator-button").on("click", function (e) {
    e.preventDefault();
    $(this).next(".shipping-calculator-form").slideToggle();
  });

  // Woocommerce Payment Toggle
  $('.wc_payment_methods input[type="radio"]:checked')
    .siblings(".payment_box")
    .show();
  $('.wc_payment_methods input[type="radio"]').each(function () {
    $(this).on("change", function () {
      $(".payment_box").slideUp();
      $(this).siblings(".payment_box").slideDown();
    });
  });

  // Woocommerce Rating Toggle
  $(".rating-select .stars a").each(function () {
    $(this).on("click", function (e) {
      e.preventDefault();
      $(this).siblings().removeClass("active");
      $(this).parent().parent().addClass("selected");
      $(this).addClass("active");
    });
  });

  /*---------- 19. Quantity Added ----------*/
  $(".quantity-plus").each(function () {
    $(this).on("click", function (e) {
      e.preventDefault();
      var $qty = $(this).siblings(".qty-input");
      var currentVal = parseInt($qty.val());
      if (!isNaN(currentVal)) {
        $qty.val(currentVal + 1);
      }
    });
  });

  $(".quantity-minus").each(function () {
    $(this).on("click", function (e) {
      e.preventDefault();
      var $qty = $(this).siblings(".qty-input");
      var currentVal = parseInt($qty.val());
      if (!isNaN(currentVal) && currentVal > 1) {
        $qty.val(currentVal - 1);
      }
    });
  });




})(jQuery);
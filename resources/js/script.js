$(document).ready(function() {

  /* Adding Sticky Navigation */
  $(".js--about-section").waypoint(function(direction) {
    if(direction == "down") {
      $("nav").addClass('sticky-nav');
    } else {
      $("nav").removeClass('sticky-nav');
    }
  }); 

  /* Scrolling to contact section */
  $(".js--scroll-to-contact").click(function() {
    $('html, body').animate({scrollTop: $('.js--contact').offset().top}, 1000);
  });

  /* Smooth scrolling of navigation */
  $('a[href*="#"]')
    .not('[href="#"]')
    .not('[href="#0"]')
    .click(function(event) {
      if (
        location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
        && 
        location.hostname == this.hostname
      ) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          event.preventDefault();
          $('html, body').animate({
            scrollTop: target.offset().top
          }, 1000, function() {
            var $target = $(target);
            $target.focus();
            if ($target.is(":focus")) {
              return false;
            } else {
              $target.attr('tabindex','-1');
              $target.focus();
            };
          });
        }
      }
    });

  // Add this code to enable scrolling to all the navbar sections
  $('nav a[href*="#"]').click(function(event) {
    event.preventDefault();
    var targetSection = $(this.hash);
    $('html, body').animate({
      scrollTop: targetSection.offset().top
    }, 1000);
  });

  /* Animation on Scroll */
  $(".js--about-section").waypoint(function(direction) {
    $(".js--about-box").addClass('animate__animated animate__fadeIn');
  }, {
    offset: '50%'
  });

  $(".js--services-section").waypoint(function(direction) {
    $(".js--service-box").addClass('animate__animated animate__zoomIn');
  }, {
    offset: '50%'
  });

  $(".js--packages-section").waypoint(function(direction) {
    $(".js--enterprise").addClass('animate__animated animate__pulse');
  });

  // No special handling needed for the login link; it will just work
});

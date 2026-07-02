document.addEventListener('click', function (event) {
  var replyLink = event.target.closest('.reply-link[data-login-required]');
  if (!replyLink) {
    return;
  }

  event.preventDefault();
  window.alert('Login to join the conversation!');
});

document.addEventListener('DOMContentLoaded', function () {
  var nav = document.querySelector('.nav');
  if (!nav) {
    return;
  }

  var navToggle = nav.querySelector('.nav-toggle');
  var submenuToggles = nav.querySelectorAll('.submenu-toggle');
  var parentLinks = nav.querySelectorAll('.menu-item-has-children > .nav-link');
  var mobileNavQuery = window.matchMedia('(max-width: 900px)');

  function setSubmenuState(item, isOpen) {
    var toggle = item.querySelector(':scope > .submenu-toggle');
    item.classList.toggle('is-submenu-open', isOpen);
    if (toggle) {
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
  }

  if (navToggle) {
    navToggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  submenuToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      var item = toggle.closest('.menu-item');
      if (!item) {
        return;
      }

      var isOpen = !item.classList.contains('is-submenu-open');
      setSubmenuState(item, isOpen);
    });
  });

  parentLinks.forEach(function (link) {
    link.addEventListener('click', function (event) {
      if (!mobileNavQuery.matches) {
        return;
      }

      var item = link.closest('.menu-item');
      if (!item) {
        return;
      }

      if (!item.classList.contains('is-submenu-open')) {
        event.preventDefault();
        setSubmenuState(item, true);
      }
    });
  });
});

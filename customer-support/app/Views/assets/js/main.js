(function () {
  'use strict';

  // Hide the spinner / preloader once the page is loaded
  window.addEventListener('load', function () {
    var loader = document.getElementById('spinner') || document.getElementById('preloader');
    if (loader) loader.classList.add('hidden');
  });

  // Front office: mobile navigation toggle
  var navToggle = document.getElementById('navToggle');
  var mainNav = document.getElementById('mainNav');
  if (navToggle && mainNav) {
    navToggle.addEventListener('click', function () { mainNav.classList.toggle('open'); });
  }

  // Back office: sidebar toggle
  var sideToggle = document.getElementById('sideToggle');
  var sidebar = document.getElementById('sidebar');
  if (sideToggle && sidebar) {
    sideToggle.addEventListener('click', function () { sidebar.classList.toggle('open'); });
  }

  // Front office: back to top button
  var backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', function () {
      backToTop.classList.toggle('show', window.scrollY > 300);
    });
    backToTop.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }
})();

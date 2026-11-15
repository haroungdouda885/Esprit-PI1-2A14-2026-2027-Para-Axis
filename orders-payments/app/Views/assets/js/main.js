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

// Back office: table search — <input data-search="#tableId"> filters the rows of that table
document.querySelectorAll('[data-search]').forEach(function (input) {
  input.addEventListener('input', function () {
    var q = input.value.toLowerCase();
    document.querySelectorAll(input.dataset.search + ' tbody tr').forEach(function (tr) {
      tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
  });
});

// Flash message (toast) displayed after an action: hides itself after a few seconds
document.querySelectorAll('.toast.show, .admin-toast.show').forEach(function (t) {
  setTimeout(function () { t.classList.remove('show'); }, 2600);
});

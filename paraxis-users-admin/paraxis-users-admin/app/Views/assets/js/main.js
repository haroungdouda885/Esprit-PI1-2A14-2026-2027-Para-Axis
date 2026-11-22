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

// Front office (Orders, member 3): "Add to bag" WITHOUT reloading the page.
// The form still works without JavaScript (classic POST + redirect).
function showToast(message, isError) {
  var t = document.createElement('div');
  t.className = 'toast' + (isError ? ' bad' : '');
  t.setAttribute('role', 'status');
  t.textContent = message;
  document.body.appendChild(t);
  requestAnimationFrame(function () { t.classList.add('show'); });
  setTimeout(function () {
    t.classList.remove('show');
    setTimeout(function () { t.remove(); }, 300);
  }, 2400);
}

document.querySelectorAll('form[data-ajax-add]').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var button = form.querySelector('button');
    if (button) button.disabled = true;

    fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'fetch' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var count = document.getElementById('bagCount');
        if (count) count.textContent = data.count;
        var link = document.getElementById('inBagLink');
        if (link && data.inBag > 0) {
          link.querySelector('span').textContent = data.inBag;
          link.classList.remove('hidden');
        }
        showToast(data.message, !data.ok);
      })
      .catch(function () { form.submit(); }) // fallback: normal submit
      .finally(function () { if (button) button.disabled = false; });
  });
});

// Bag page (Orders, member 3): "+" / "−" WITHOUT reloading the page.
// The updated bag is fetched, then only the product list, the counters and the total are replaced
// (what the customer typed in the checkout form is kept).
document.addEventListener('submit', function (e) {
  var form = e.target;
  if (!form.matches('form[data-ajax-update]')) return;
  e.preventDefault();

  fetch(form.action, { method: 'POST', body: new FormData(form) }) // the server answers with the bag page
    .then(function (r) { return r.text(); })
    .then(function (html) {
      var doc = new DOMParser().parseFromString(html, 'text/html');
      var newLines = doc.getElementById('bagLines');
      var oldLines = document.getElementById('bagLines');
      var newTotal = doc.getElementById('checkoutTotal');
      if (!newLines || !oldLines || (document.getElementById('checkoutTotal') && !newTotal)) {
        window.location.reload(); // bag became empty: the checkout block changes, reload once
        return;
      }
      oldLines.innerHTML = newLines.innerHTML;
      ['bagCount', 'bagHeadCount', 'checkoutTotal'].forEach(function (id) {
        var a = document.getElementById(id), b = doc.getElementById(id);
        if (a && b) a.textContent = b.textContent;
      });
      var toast = doc.querySelector('.toast.show');
      if (toast) showToast(toast.textContent, toast.classList.contains('bad'));
    })
    .catch(function () { form.submit(); });
});

/* MEMBER 5 (users) - "show another image" button of the built-in CAPTCHA */
document.addEventListener('click', function (event) {
  var btn = event.target.closest('[data-captcha-refresh]');
  if (!btn) return;
  var img = btn.parentNode.querySelector('.captcha-img');
  if (img) img.src = 'index.php?page=captcha&t=' + Date.now();
  var input = document.getElementById('captcha');
  if (input) { input.value = ''; input.focus(); }
});

<!-- Page-specific script ($extraScripts): opens the registration form with the chosen event -->
<script>
  (function () {
    var modal = document.getElementById('regModal');
    if (!modal) return;
    var close = function () { modal.classList.remove('open'); };

    document.querySelectorAll('[data-register]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.getElementById('regEvent').value = btn.dataset.register;
        document.getElementById('regTitle').textContent = btn.dataset.title;
        document.getElementById('regWhen').textContent = btn.dataset.when;
        modal.classList.add('open');
      });
    });
    document.getElementById('regClose').addEventListener('click', close);
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
  })();
</script>

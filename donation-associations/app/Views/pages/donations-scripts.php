<!-- Page-specific script ($extraScripts): donation form (choose association, money / products, total) -->
<script>
  (function () {
    var modal = document.getElementById('donModal');
    if (!modal) return;
    var close = function () { modal.classList.remove('open'); };

    document.querySelectorAll('[data-donate]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.getElementById('donAssoc').value = btn.dataset.donate;
        document.getElementById('donName').textContent = btn.dataset.name;
        document.getElementById('donCause').textContent = 'Category: ' + btn.dataset.cause;
        modal.classList.add('open');
      });
    });
    document.getElementById('donClose').addEventListener('click', close);
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });

    // show the "money" or the "products" part
    var showType = function () {
      var type = modal.querySelector('input[name="type"]:checked').value;
      modal.querySelectorAll('[data-part]').forEach(function (part) {
        part.hidden = part.dataset.part !== type;
      });
      modal.querySelector('input[name="amount"]').required = type === 'money';
    };
    modal.querySelectorAll('input[name="type"]').forEach(function (r) { r.addEventListener('change', showType); });
    showType();

    // value of the chosen products
    var total = function () {
      var sum = 0;
      modal.querySelectorAll('input[data-price]').forEach(function (i) {
        sum += (parseInt(i.value, 10) || 0) * parseFloat(i.dataset.price);
      });
      document.getElementById('donTotal').textContent = sum.toFixed(2);
    };
    modal.querySelectorAll('input[data-price]').forEach(function (i) { i.addEventListener('input', total); });
  })();
</script>

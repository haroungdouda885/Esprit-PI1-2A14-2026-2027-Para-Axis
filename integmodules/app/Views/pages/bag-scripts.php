<!-- Page-specific script ($extraScripts): card fields are shown only for the card payment.
     Cash on delivery = only the delivery address is needed. -->
<script>
  (function () {
    var method  = document.getElementById('paymentMethod');
    var fields  = document.getElementById('paymentFields');
    var note    = document.getElementById('cashNote');
    var confirm = document.getElementById('confirmBtn');
    if (!method || !fields) return;

    function update() {
      var card = method.value === 'card';
      fields.style.display = card ? '' : 'none';
      fields.classList.toggle('hidden', !card);
      if (note) note.style.display = card ? 'none' : '';
      if (confirm) confirm.textContent = card ? 'Confirm order and payment ↗' : 'Confirm order ↗';
    }
    method.addEventListener('change', update);
    update();
  })();
</script>

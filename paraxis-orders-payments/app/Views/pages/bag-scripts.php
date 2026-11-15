<!-- Page-specific script ($extraScripts): card fields are shown only for the card payment -->
<script>
  (function () {
    var method = document.getElementById('paymentMethod');
    var fields = document.getElementById('paymentFields');
    if (method && fields) {
      method.addEventListener('change', function () {
        fields.classList.toggle('hidden', method.value !== 'card');
      });
    }
  })();
</script>

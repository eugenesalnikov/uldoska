document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('sendListingForm').addEventListener('submit', function () {
    const btn = document.getElementById('sendListingBtn');
    btn.disabled = true;
    btn.textContent = 'Отправка...';
  });
});

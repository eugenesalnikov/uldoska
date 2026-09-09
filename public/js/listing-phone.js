document.addEventListener('DOMContentLoaded', () => {
  const input = document.querySelector('[data-phone]');
  if (!input) return;

  const form = input.closest('form');
  if (!form) return;

  const normalize = (value) => {
    let phone = value.replace(/[^\d+]/g, '');

    if (phone.startsWith('8') && phone.length === 11) {
      phone = '+7' + phone.slice(1);
    } else if (phone.startsWith('7') && phone.length === 11) {
      phone = '+' + phone;
    } else if (phone.startsWith('9') && phone.length === 10) {
      phone = '+7' + phone;
    }

    return phone;
  };

  form.addEventListener('submit', (event) => {
    input.value = normalize(input.value);

    if (!/^\+7\d{10}$/.test(input.value)) {
      event.preventDefault();
      alert('Укажите телефон в формате +7XXXXXXXXXX');
    }
  });
});

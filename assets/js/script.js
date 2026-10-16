// Client-side validation for forms with the data-validate attribute
document.querySelectorAll('form[data-validate]').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    var problems = [];
    form.querySelectorAll('input[required]').forEach(function (input) {
      var v = input.value.trim();
      if (v === '') {
        problems.push((input.name || 'Field') + ' is required.');
      } else if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) {
        problems.push('Please enter a valid email address.');
      } else if (input.minLength > 0 && v.length < input.minLength) {
        problems.push(input.name + ' must be at least ' + input.minLength + ' characters.');
      }
    });
    var pw = form.querySelector('#password');
    var cf = form.querySelector('#confirm');
    if (pw && cf && pw.value !== cf.value) {
      problems.push('Passwords do not match.');
    }
    var old = form.querySelector('.js-errors');
    if (old) old.remove();
    if (problems.length) {
      e.preventDefault();
      var box = document.createElement('div');
      box.className = 'js-errors bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4';
      box.innerHTML = problems.map(function (p) {
        var s = document.createElement('div');
        s.textContent = p;
        return s.outerHTML;
      }).join('');
      form.prepend(box);
    }
  });
});

// Live search: filter the visible book cards as you type
var search = document.getElementById('book-search');
if (search) {
  search.addEventListener('input', function () {
    var term = this.value.toLowerCase();
    document.querySelectorAll('.book-card').forEach(function (card) {
      card.style.display = card.textContent.toLowerCase().indexOf(term) !== -1 ? '' : 'none';
    });
  });
}

/* Employra — small UI behaviours (no dependencies besides Bootstrap's bundle). */
(function () {
  'use strict';

  /* Show / hide password */
  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.querySelector(btn.getAttribute('data-toggle-password'));
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      var icon = btn.querySelector('.bi');
      if (icon) icon.className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });

  /* Bootstrap client-side validation on forms marked .needs-validation */
  document.querySelectorAll('form.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var pw = form.querySelector('[data-match]');
      if (pw) {
        var other = form.querySelector(pw.getAttribute('data-match'));
        pw.setCustomValidity(other && pw.value !== other.value ? 'Passwords do not match' : '');
      }
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        var firstBad = form.querySelector(':invalid');
        if (firstBad) firstBad.focus();
      }
      form.classList.add('was-validated');
    });
  });

  /* Password strength meter */
  var meter = document.querySelector('[data-strength-for]');
  if (meter) {
    var target = document.querySelector(meter.getAttribute('data-strength-for'));
    var label = document.querySelector('[data-strength-label]');
    var bar = meter.querySelector('span');
    if (target && bar) {
      target.addEventListener('input', function () {
        var v = target.value, score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
        if (/\d/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        var colors = ['#e5484d', '#e5484d', '#ff9a3c', '#3ea66b', '#12a150'];
        var words = ['Too short', 'Weak', 'Fair', 'Good', 'Strong'];
        if (!v) { score = 0; }
        bar.style.width = (v ? Math.max(score, 1) * 25 : 0) + '%';
        bar.style.background = colors[score];
        if (label) label.textContent = v ? words[score] : '';
      });
    }
  }

  /* Register: switch fields by chosen role */
  var roleInputs = document.querySelectorAll('input[name="role"]');
  if (roleInputs.length) {
    var apply = function () {
      var role = (document.querySelector('input[name="role"]:checked') || {}).value || 'candidate';
      document.querySelectorAll('[data-role-only]').forEach(function (el) {
        var on = el.getAttribute('data-role-only') === role;
        el.classList.toggle('d-none', !on);
        el.querySelectorAll('input,select,textarea').forEach(function (i) {
          if (i.hasAttribute('data-required-when-visible')) i.required = on;
        });
      });
      document.querySelectorAll('[data-role-text]').forEach(function (el) {
        el.textContent = role === 'company' ? el.getAttribute('data-company') : el.getAttribute('data-candidate');
      });
    };
    roleInputs.forEach(function (r) { r.addEventListener('change', apply); });
    apply();
  }

  /* Save / unsave a job (UI only — remembered in this browser until the backend exists) */
  var key = 'employra_saved_jobs';
  var saved = [];
  try { saved = JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) { saved = []; }
  document.querySelectorAll('.save-btn[data-save]').forEach(function (btn) {
    var id = btn.getAttribute('data-save');
    if (saved.indexOf(id) > -1) btn.classList.add('saved');
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var i = saved.indexOf(id);
      if (i > -1) { saved.splice(i, 1); btn.classList.remove('saved'); } else { saved.push(id); btn.classList.add('saved'); }
      btn.setAttribute('aria-pressed', btn.classList.contains('saved') ? 'true' : 'false');
      try { localStorage.setItem(key, JSON.stringify(saved)); } catch (err) { /* storage may be blocked */ }
    });
  });

  /* Auto-submit filter forms when a filter changes (jobs page) */
  document.querySelectorAll('form[data-autosubmit] input[type="checkbox"], form[data-autosubmit] input[type="number"], form[data-autosubmit] input[type="range"], form[data-autosubmit] select').forEach(function (el) {
    el.addEventListener('change', function () { el.form.submit(); });
  });
})();

/* ---- Candidate area helpers (batch 2) ---- */
document.addEventListener('DOMContentLoaded', function () {
  // Show chosen file name in the CV dropzone
  var fileInput = document.getElementById('cv-file');
  if (fileInput) {
    var label = fileInput.closest('.dropzone');
    var out = label.querySelector('[data-file-name]');
    fileInput.addEventListener('change', function () { out.textContent = fileInput.files[0] ? fileInput.files[0].name : ''; });
    ['dragenter', 'dragover'].forEach(function (e) { label.addEventListener(e, function () { label.classList.add('dragover'); }); });
    ['dragleave', 'drop'].forEach(function (e) { label.addEventListener(e, function () { label.classList.remove('dragover'); }); });
  }

  document.querySelectorAll('[data-add-onboarding-row]').forEach(function (button) {
    var target = document.getElementById(button.getAttribute('data-add-onboarding-row'));
    if (!target) return;

    var updateButton = function () {
      button.disabled = target.querySelectorAll('input, select').length >= Number(button.getAttribute('data-max-items') || 5);
    };

    button.addEventListener('click', function () {
      var template = document.getElementById(button.getAttribute('data-row-template'));
      var row;
      var input;
      if (template && template.content) {
        row = template.content.firstElementChild.cloneNode(true);
        input = row.querySelector('input, select');
      } else {
        row = document.createElement('div');
        row.className = 'input-group mb-2';

        input = document.createElement('input');
        input.type = 'text';
        input.name = button.getAttribute('data-field-name');
        input.className = 'form-control';
        input.placeholder = button.getAttribute('data-placeholder') || '';
        input.required = true;
        var datalist = button.getAttribute('data-datalist');
        if (datalist) input.setAttribute('list', datalist);

        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'btn btn-outline-navy';
        remove.textContent = 'Remove';
        remove.setAttribute('aria-label', 'Remove this preference');
        remove.addEventListener('click', function () {
          row.remove();
          updateButton();
        });

        row.appendChild(input);
        row.appendChild(remove);
      }

      if (!input) return;
      input.required = true;
      target.appendChild(row);
      input.focus();
      updateButton();

      var removeButton = row.querySelector('[data-remove-onboarding-row]');
      if (removeButton) {
        removeButton.addEventListener('click', function () {
          row.remove();
          updateButton();
        });
      }
    });

    updateButton();
  });

  document.querySelectorAll('[data-remove-onboarding-row]').forEach(function (button) {
    button.addEventListener('click', function () {
      var row = button.closest('.input-group');
      var container = row && row.parentElement;
      if (row) row.remove();
      var addButton = container && document.querySelector('[data-add-onboarding-row="' + container.id + '"]');
      if (addButton) {
        addButton.disabled = container.querySelectorAll('input, select').length >= Number(addButton.getAttribute('data-max-items') || 5);
      }
    });
  });

  var salaryMin = document.getElementById('expected_salary_min') || document.getElementById('f-salary-min');
  var salaryMax = document.getElementById('expected_salary_max') || document.getElementById('f-salary-max');
  if (salaryMin && salaryMax) {
    var currency = new Intl.NumberFormat('en-NG', {
      style: 'currency',
      currency: 'NGN',
      maximumFractionDigits: 0
    });
    var minOutput = document.querySelector('[data-salary-min-output]');
    var maxOutput = document.querySelector('[data-salary-max-output]');

    var updateSalary = function (changed) {
      if (Number(salaryMin.value) > Number(salaryMax.value)) {
        if (changed === salaryMax) salaryMin.value = salaryMax.value;
        else salaryMax.value = salaryMin.value;
      }
      if (minOutput) minOutput.textContent = currency.format(Number(salaryMin.value));
      if (maxOutput) maxOutput.textContent = currency.format(Number(salaryMax.value));
    };

    salaryMin.addEventListener('input', function () { updateSalary(salaryMin); });
    salaryMax.addEventListener('input', function () { updateSalary(salaryMax); });
    updateSalary();
  }

  // Skill tag input
  document.querySelectorAll('[data-tag-input]').forEach(function (box) {
    var input = box.querySelector('input');
    var fieldName = box.getAttribute('data-name');
    box.addEventListener('click', function (ev) {
      if (ev.target.tagName === 'BUTTON') { ev.target.closest('.skill-chip').remove(); }
    });
    input.addEventListener('keydown', function (ev) {
      if (ev.key === 'Enter' && input.value.trim()) {
        ev.preventDefault();
        var chip = document.createElement('span');
        chip.className = 'skill-chip';
        chip.textContent = input.value.trim();
        if (fieldName) {
          var hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = fieldName;
          hidden.value = input.value.trim();
          chip.appendChild(hidden);
        }
        var b = document.createElement('button');
        b.type = 'button'; b.setAttribute('aria-label', 'Remove ' + chip.textContent); b.innerHTML = '&times;';
        chip.appendChild(b); box.insertBefore(chip, input); input.value = '';
      }
    });
  });
});

/* ---- Employer area helpers (batch 3) ---- */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-range]').forEach(function (r) {
    var out = r.closest('div').querySelector('[data-range-out]');
    r.addEventListener('input', function () { if (out) { out.textContent = r.value + '%'; } });
  });
});

/* ---- Admin helpers: clicking a table row opens its detail panel, unless the click was on a button/link/menu ---- */
document.addEventListener('click', function (e) {
  var row = e.target.closest('tr[data-row-link]');
  if (!row || e.target.closest('a, button, [data-stop-row], .dropdown-menu')) { return; }
  var el = document.querySelector(row.getAttribute('data-row-link'));
  if (el && window.bootstrap) { bootstrap.Offcanvas.getOrCreateInstance(el).show(); }
});

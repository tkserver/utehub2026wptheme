document.addEventListener('click', function (event) {
  var replyLink = event.target.closest('.reply-link[data-login-required]');
  if (!replyLink) {
    return;
  }

  event.preventDefault();
  window.alert('Login to join the conversation!');
});

document.addEventListener('DOMContentLoaded', function () {
  var nav = document.querySelector('.nav');
  if (!nav) {
    return;
  }

  var navToggle = nav.querySelector('.nav-toggle');
  var submenuToggles = nav.querySelectorAll('.submenu-toggle');
  var parentLinks = nav.querySelectorAll('.menu-item-has-children > .nav-link');
  var mobileNavQuery = window.matchMedia('(max-width: 900px)');

  function setSubmenuState(item, isOpen) {
    var toggle = item.querySelector(':scope > .submenu-toggle');
    item.classList.toggle('is-submenu-open', isOpen);
    if (toggle) {
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
  }

  if (navToggle) {
    navToggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
  }

  submenuToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      var item = toggle.closest('.menu-item');
      if (!item) {
        return;
      }

      var isOpen = !item.classList.contains('is-submenu-open');
      setSubmenuState(item, isOpen);
    });
  });

  parentLinks.forEach(function (link) {
    link.addEventListener('click', function (event) {
      if (!mobileNavQuery.matches) {
        return;
      }

      var item = link.closest('.menu-item');
      if (!item) {
        return;
      }

      if (!item.classList.contains('is-submenu-open')) {
        event.preventDefault();
        setSubmenuState(item, true);
      }
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  var topicForm = document.querySelector('.bbp-topic-form form#new-post');
  if (!topicForm) {
    return;
  }

  var titleInput = topicForm.querySelector('#bbp_topic_title');
  var contentInput = topicForm.querySelector('#bbp_topic_content');
  var forumSelect = topicForm.querySelector('#bbp_forum_id');
  var submitButton = topicForm.querySelector('#bbp_topic_submit');
  var fieldset = topicForm.querySelector('fieldset.bbp-form');
  var isSubmitting = false;

  function getFieldLabel(field) {
    if (!field || !field.id) {
      return 'Required field';
    }

    var label = topicForm.querySelector('label[for="' + field.id + '"] .bbp-field-label') || topicForm.querySelector('label[for="' + field.id + '"]');
    return label ? label.textContent.trim() : 'Required field';
  }

  function isBlank(field) {
    return !field || !field.value || !field.value.trim();
  }

  function isContentBlank(field) {
    if (!field) {
      return true;
    }

    if (window.tinyMCE && window.tinyMCE.get(field.id)) {
      return !window.tinyMCE.get(field.id).getContent({ format: 'text' }).trim();
    }

    return isBlank(field);
  }

  function isEmptyForum(field) {
    return field && (!field.value || field.value === '0' || field.value === '-1');
  }

  function ensureNotice() {
    var notice = topicForm.querySelector('.bbp-topic-form-client-notice');
    if (notice) {
      return notice;
    }

    notice = document.createElement('div');
    notice.className = 'bbp-template-notice bbp-topic-form-client-notice error';
    notice.setAttribute('role', 'alert');
    notice.setAttribute('aria-live', 'polite');
    notice.innerHTML = '<ul></ul>';

    if (fieldset && fieldset.firstElementChild) {
      fieldset.insertBefore(notice, fieldset.firstElementChild.nextElementSibling);
    } else {
      topicForm.insertBefore(notice, topicForm.firstChild);
    }

    return notice;
  }

  function setFieldInvalid(field, invalid) {
    if (!field) {
      return;
    }

    field.classList.toggle('is-invalid', invalid);
    field.setAttribute('aria-invalid', invalid ? 'true' : 'false');
  }

  function getErrors() {
    var errors = [];

    if (isBlank(titleInput)) {
      errors.push({ field: titleInput, message: getFieldLabel(titleInput) + ' is required.' });
    }

    if (isContentBlank(contentInput)) {
      errors.push({ field: contentInput, message: getFieldLabel(contentInput) + ' is required.' });
    }

    if (isEmptyForum(forumSelect)) {
      errors.push({ field: forumSelect, message: getFieldLabel(forumSelect) + ' is required.' });
    }

    return errors;
  }

  function syncSubmitButton(errors) {
    if (!submitButton || isSubmitting) {
      return;
    }

    var isValid = errors.length === 0;
    submitButton.classList.toggle('is-form-valid', isValid);
    submitButton.classList.toggle('is-form-invalid', !isValid);
    submitButton.setAttribute('aria-disabled', isValid ? 'false' : 'true');
  }

  function renderErrors(errors) {
    var notice = ensureNotice();
    var list = notice.querySelector('ul');

    list.innerHTML = '';
    errors.forEach(function (error) {
      var item = document.createElement('li');
      item.textContent = error.message;
      list.appendChild(item);
    });

    notice.hidden = errors.length === 0;
    topicForm.classList.toggle('has-topic-form-errors', errors.length > 0);

    [titleInput, contentInput, forumSelect].forEach(function (field) {
      setFieldInvalid(field, errors.some(function (error) {
        return error.field === field;
      }));
    });

    syncSubmitButton(errors);
  }

  function validateTopicForm(showNotice) {
    var errors = getErrors();
    syncSubmitButton(errors);

    if (showNotice || topicForm.classList.contains('has-topic-form-errors')) {
      renderErrors(errors);
    }

    return errors;
  }

  [titleInput, contentInput, forumSelect].forEach(function (field) {
    if (!field) {
      return;
    }

    field.addEventListener('input', function () {
      validateTopicForm(false);
    });
    field.addEventListener('change', function () {
      validateTopicForm(false);
    });
  });

  topicForm.addEventListener('submit', function (event) {
    if (isSubmitting) {
      event.preventDefault();
      return;
    }

    var errors = validateTopicForm(true);
    if (!errors.length) {
      isSubmitting = true;
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.setAttribute('aria-disabled', 'true');
        submitButton.classList.add('is-submitting');
      }
      return;
    }

    event.preventDefault();
    errors[0].field.focus();

    var notice = topicForm.querySelector('.bbp-topic-form-client-notice');
    if (notice) {
      notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  validateTopicForm(false);
});
